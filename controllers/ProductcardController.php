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
        return $this->redirect(['index']);
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
        if($model->load(Yii::$app->request->post()) && $model->save()){
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
        if($model->cardState == 1){
            throw new HttpException(500, "You can't update the productcard that has been used.");
        }
        if($model->load(Yii::$app->request->post()) && $model->save()){
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
        return $this->redirect(['index']);
    }
}