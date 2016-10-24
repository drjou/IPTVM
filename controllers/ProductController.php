<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\ProductSearch;
use app\models\Product;
use yii\helpers\ArrayHelper;
use yii\web\HttpException;
use app\models\Channel;
use yii\db\Exception;

class ProductController extends Controller{
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
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'delete-all' => ['get'],
                    'view' => ['get'],
                ]
            ]
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
     * Index Action 显示所有的产品
     * @return string
     */
    public function actionIndex(){
        $searchModel = new ProductSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * 删除选中的一些产品
     * @param string $keys
     * @return \yii\web\Response
     */
    public function actionDeleteAll($keys){
        //将得到的字符串转为php数组
        $productIds = explode(',', $keys);
        $productNames = [];
        foreach ($productIds as $productId){
            $prod = Product::findProductById($productId);
            $states = ArrayHelper::getColumn($prod->productcards, 'cardState');
            if(in_array(1, $states)){
                throw new HttpException(500, "these products contain product whose productcards have been used, you can't delete it.");
            }
            array_push($productNames, $prod->productName);
        }
        //使用","作为分隔符将数组转为字符串
        $products = implode('","', $productIds);
        //在最终的字符串前后各加一个"
        $products = '"' . $products . '"';
        $model = new Product();
        //调用model的deleteAll方法删除数据
        $model->deleteAll("productId in($products)");
        Yii::info('delete selected ' . count($productNames) . ' products, they are ' . implode(',', $productNames), 'administrator');
        return $this->redirect(['index']);
    }
    /**
     * View Action 显示product的详细信息
     * @param int $productId
     * @return string
     */
    public function actionView($productId){
        $model = Product::findProductById($productId);
        $cardProvider = $model->findProductcards();
        $bindProvider = $model->findBindAccounts();
        $accountProvider = $model->findAccounts();
        $channelProvider = $model->findChannels();
        return $this->render('view', [
            'model' => $model,
            'cardProvider' => $cardProvider,
            'bindProvider' => $bindProvider,
            'accountProvider' => $accountProvider,
            'channelProvider' => $channelProvider,
        ]);
    }
    /**
     * 创建新的product
     * @return string
     */
    public function actionCreate(){
        $model = new Product();
        if($model->load(Yii::$app->request->post())){
            if(!empty($model->channels)){//添加的channels不为空
                //将channels信息添加到product_channel表中
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();
                try {
                    if($model->save()){
                        $columns = ['productId', 'channelId'];
                        $rows = [];
                        foreach ($model->channels as $channel){
                            $row = [$model->productId, $channel];
                            array_push($rows, $row);
                        }
                        $db->createCommand()->batchInsert('product_channel', $columns, $rows)->execute();
                        $transaction->commit();
                        Yii::info("create product $model->productName", 'administrator');
                        return $this->redirect(['view', 'productId' => $model->productId]);
                    }
                }catch(Exception $e){
                    $transaction->rollBack();
                    $model->addError('productName', "add product $model->productName failed! please try again.");
                }
            }else{
                if($model->save()){
                    Yii::info("create product $model->productName", 'administrator');
                    return $this->redirect(['view', 'productId' => $model->productId]);
                }
            }
        }
        $channels = ArrayHelper::map(Channel::find()->all(), 'channelId', 'channelName');
        return $this->render('create', [
            'model' => $model,
            'channels' => $channels,
        ]);
    }
    /**
     * 更新指定的product
     * @param int $productId
     * @return string
     */
    public function actionUpdate($productId){
        $model = Product::findProductById($productId);
        //修改前的channels列表
        $oldChannels = ArrayHelper::getColumn($model->channels, 'channelId');
        if($model->load(Yii::$app->request->post())){
            //修改后的新的channels列表
            $newChannels = $model->channels;
            if(empty($newChannels)){
                $newChannels = [];
            }
            //增加的channels
            $addChannels = array_diff($newChannels, $oldChannels);
            //删除的channels
            $delChannels = array_diff($oldChannels, $newChannels);
            if(!empty($addChannels) || !empty($delChannels)){//如果发生增删
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();
                try {
                    if($model->save()){
                        if(!empty($addChannels)){
                            $columns = ['productId', 'channelId'];
                            $rows = [];
                            foreach ($addChannels as $channel){
                                $row = [$model->productId, $channel];
                                array_push($rows, $row);
                            }
                            $db->createCommand()->batchInsert('product_channel', $columns, $rows)->execute();
                        }
                        if(!empty($delChannels)){
                            $db->createCommand()->delete('product_channel', ['productId' => $model->productId, 'channelId' => $delChannels])->execute();
                        }
                        $transaction->commit();
                        Yii::info("update product $model->productName", 'administrator');
                        return $this->redirect(['view', 'productId' => $productId]);
                    }
                }catch (Exception $e){
                    $transaction->rollBack();
                    $model->addError('productName', "update product $model->productName failed! please try again.");
                }
            }else{
                if($model->save()){
                    Yii::info("update product $model->productName", 'administrator');
                    return $this->redirect(['view', 'productId' => $model->productId]);
                }
            }
        }
        
        $channels = ArrayHelper::map(Channel::find()->all(), 'channelId', 'channelName');
        return $this->render('update', [
            'model' => $model,
            'channels' => $channels,
        ]);
    }
    /**
     * 删除指定的product
     * @param int $productId
     * @throws HttpException
     * @return \yii\web\Response
     */
    public function actionDelete($productId){
        $model = Product::findProductById($productId);
        $states = ArrayHelper::getColumn($model->productcards, 'cardState');
        if(in_array(1, $states)){
            throw new HttpException(500, "You can't delete the product whose productcards have been used.");
        }
        $model->delete();
        Yii::info("delete product $model->productName", 'administrator');
        return $this->redirect(['index']);
    }
}