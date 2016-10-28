<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\ProductcardSearch;
use app\models\Productcard;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use app\models\Product;
use yii\web\UploadedFile;

class ProductcardController extends Controller{
    /**
     * 设置访问权限
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'delete-all' => ['get'],
                    'import' => ['get', 'post'],
                    'export' => ['get'],
                    'view' => ['get'],
                    'create' => ['get', 'post'],
                    'update' => ['get', 'post'],
                    'delete' => ['get'],
                ],
            ],
        ];
    }
    /**
     * 独立操作
     * {@inheritDoc}
     * @see \yii\base\Controller::actions()
     */
    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    /**
     * Index Action 显示所有的充值卡信息
     * @return string
     */
    public function actionIndex(){
        $searchModel = new ProductcardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * 删除选中的一些产品充值卡
     * @param string $keys
     * @return \yii\web\Response
     */
    public function actionDeleteAll($keys){
        //将得到的字符串转为php数组
        $cardNumbers = explode(',', $keys);
        foreach ($cardNumbers as $cardNumber){
            $card = Productcard::findProductcardById($cardNumber);
            if($card->cardState == 1){
                throw new HttpException(500, "these productcards contain productcard that has been used, you can't delete it");
            }
        }
        //使用","作为分隔符将数组转为字符串
        $cards = implode('","', $cardNumbers);
        //在最终的字符串前后各加一个"
        $cards = '"' . $cards . '"';
        $model = new Productcard();
        //调用model的deleteAll方法删除数据
        $model->deleteAll("cardNumber in($cards)");
        Yii::info('delete selected ' . count($cardNumbers) . ' productcards, they are ' . $keys, 'administrator');
        return $this->redirect(['index']);
    }
    
    /**
     * import productcards
     */
    public function actionImport(){
        $model = new Productcard();
        $model->scenario = Productcard::SCENARIO_IMPORT;
        $state = [
            'message' => 'Info:please import a xml file. Format as below:</br>'
            . '&lt;?xml version="1.0" encoding="UTF-8"?&gt;</br>'
            . '&lt;message&gt;</br>'
            . '&nbsp;&nbsp;&lt;Productcard&gt;</br>'
            . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;cardNumber&gt;0909201220130001&lt;/cardNumber&gt;</br>'
            . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;cardValue&gt;365&lt;/cardValue&gt;</br>'
            . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;productId&gt;1&lt;/productId&gt;</br>'
            . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;cardState&gt;1&lt;/cardState&gt;</br>'
            . '&nbsp;&nbsp;&lt;/Productcard&gt;</br>'
            . '&nbsp;&nbsp;&lt;Productcard&gt;</br>'
            . '&nbsp;&nbsp;&nbsp;&nbsp;······</br>'
            . '&nbsp;&nbsp;&lt;/Productcard&gt;</br>'
            . '&lt;/message&gt;</br>',
            'class' => 'alert-info',
            'percent' => 0,
            'label' => '0%',
        ];
        if($model->load(Yii::$app->request->post())){
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            try {
                $xmlArray = simplexml_load_file($model->importFile->tempName);
                $productcards = json_decode(json_encode($xmlArray), true);
                $columns = ['cardNumber', 'cardValue', 'productId', 'cardState', 'createTime', 'updateTime'];
                $rows = ArrayHelper::getColumn($productcards['Productcard'], function($element){
                    $now = date('Y-m-d H:i:s', time());
                    return [$element['cardNumber'], $element['cardValue'], $element['productId'], $element['cardState'], $now, $now];
                });
                    $db = Yii::$app->db;
                    $db->createCommand()->batchInsert('productcard', $columns, $rows)->execute();
                    $productcardStr = implode(',', ArrayHelper::getColumn($productcards['Productcard'], 'cardNumber'));
                    Yii::info("import " . count($rows) . " productcards, they are $productcardStr", 'administrator');
                    $state['message'] = 'Success:import success, there are totally ' . count($rows) .' productcards added to DB, they are ' . $productcardStr;
                    $state['class'] = 'alert-success';
                    $state['percent'] = 100;
                    $state['label'] = '100% completed';
            }catch (\Exception $e){
                $state['message'] = 'Error:' . $e->getMessage();
                $state['class'] = 'alert-danger';
            }
        }
        return $this->render('import', [
            'model' => $model,
            'state' => $state,
        ]);
    }
    /**
     * 导出所有的productcards
     */
    public function actionExport(){
        $model = new Productcard();
        $productcards = $model->find()->all();
        $response = Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_XML,
            'formatters' => [
                \yii\web\Response::FORMAT_XML => [
                    'class' => 'yii\web\XmlResponseFormatter',
                    'rootTag' => 'message', //根节点
                    'itemTag' => 'productcard',
                ],
            ],
            'data' => $productcards,
        ]);
        $formatter = new \yii\web\XmlResponseFormatter();
        $formatter->rootTag = 'message';
        $formatter->format($response);
        Yii::$app->response->sendContentAsFile($response->content, 'productcards.xml')->send();
        Yii::info('export all productcards', 'administrator');
    }
    
    /**
     * View Action 显示productcar的详细信息
     * @param string $cardNumber
     * @return string
     */
    public function actionView($cardNumber){
        $model = Productcard::findProductcardById($cardNumber);
        return $this->render('view', [
            'model' => $model,
        ]);
    }
    /**
     * 创建新的productcard
     * @return string
     */
    public function actionCreate(){
        $model = new Productcard();
        $model->scenario = Productcard::SCENARIO_SAVE;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::info("create productcard $model->cardNumber", 'administrator');
            return $this->redirect(['view', 'cardNumber' => $model->cardNumber]);
        }
        $products = ArrayHelper::map(Product::find()->all(), 'productId', 'productName');
        return $this->render('create', [
            'model' => $model, 
            'products' => $products,
        ]);
    }
    /**
     * 修改指定的productcard
     * @param string $cardNumber
     * @return \yii\web\Response|string
     */
    public function actionUpdate($cardNumber){
        $model = Productcard::findProductcardById($cardNumber);
        $model->scenario = Productcard::SCENARIO_SAVE;
        if($model->cardState == 1){
            throw new HttpException(500, "You can't update the productcard that has been used.");
        }
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::info("update productcard $model->cardNumber", 'administrator');
            return $this->redirect(['view', 'cardNumber' => $model->cardNumber]);
        }
        $products = ArrayHelper::map(Product::find()->all(), 'productId', 'productName');
        return $this->render('update', [
            'model' => $model,
            'products' => $products,
        ]);
    }
    /**
     * 删除指定的productcard
     * @param string $cardNumber
     * @throws HttpException
     * @return \yii\web\Response
     */
    public function actionDelete($cardNumber){
        $model = Productcard::findProductcardById($cardNumber);
        if($model->cardState == 1){
            throw new HttpException(500, "You can't delete the productcard that has been used.");
        }
        $model->delete();
        Yii::info("delete productcard $model->cardNumber", 'administrator');
        return $this->redirect(['index']);
    }
}