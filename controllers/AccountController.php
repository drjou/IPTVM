<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Account;
use app\models\AccountSearch;
use app\models\Product;
use yii\helpers\ArrayHelper;
use yii\db\Exception;
use yii\web\HttpException;

class AccountController extends Controller{
    /**
     * 访问权限设置
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
            ]
        ];
    }
    /**
     * Index Action 显示所有的account信息
     * @return string
     */
    public function actionIndex(){
        $searchModel = new AccountSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]); 
    }
    /**
     * 多选删除操作
     * @param string $keys
     * @return string
     */
    public function actionDeleteAll($keys){
        //将得到的字符串转为php数组
        $accountIds = explode(',', $keys);
        foreach ($accountIds as $accountId){
            $acc = Account::findAccountById($accountId);
            if($acc->state == '1001' || $acc->state == '1004'){
                throw new HttpException(500, 'you can\'t delete the account whose state is 1001 or 1004');
            }
        }
        //使用","作为分隔符将数组转为字符串
        $accounts = implode('","', $accountIds);
        //在最终的字符串前后各加一个"
        $accounts = '"' . $accounts . '"';
        $model = new Account();
        //调用model的deleteAll方法删除数据
        $model->deleteAll("accountId in($accounts)");
        return $this->redirect(['index']);
    }
    /**
     * View Action 查看account的详细信息
     * @param unknown $accountId
     */
    public function actionView($accountId){
        $model = Account::findAccountById($accountId);
        $bindProvider = $model->findBindProducts();
        $productProvider = $model->findAccountProducts();
        $productcardProvider = $model->findProductcards();
        return $this->render('view', [
            'model' => $model,
            'bindProvider' => $bindProvider,
            'productProvider' => $productProvider,
            'productcardProvider' => $productcardProvider,
        ]);
    }
    
    /**
     * Create Action 新增account
     * @return string
     */
    public function actionCreate(){
        $model = new Account();
        if($model->load(Yii::$app->request->post())){
            if($model->state == '1003'){
                if($model->save()){
                    return $this->redirect(['view', 'accountId' => $model->accountId]);
                }
            }else{
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();//开启事务
                try {
                    if($model->save()){//保存account信息
                        $columns = ['accountId', 'productId', 'bindDay', 'isActive', 'activeDate'];
                        $rows = [];
                        foreach ($model->products as $product){
                            $row = [$model->accountId, $product, 356, 0, '3000-01-01'];
                            array_push($rows, $row);
                        }
                        //将预绑定的产品 信息插入表stbbind中
                        $db->createCommand()->batchInsert('stbbind', $columns, $rows)->execute();
                        $transaction->commit();
                        return $this->redirect(['view', 'accountId' => $model->accountId]);
                    }
                }catch (Exception $e){
                    $transaction->rollBack();
                    $model->addError('accountId', "add account $model->accountId failed! please try again.");
                }
            }
        }
        $model->enable = 1;
        $products = ArrayHelper::map(Product::find()->select(['productId', 'productName'])->all(), 'productId', 'productName');
        return $this->render('create', [
            'model' => $model,
            'products' => $products,
        ]);
    }
    /**
     * 修改account信息
     * @param string $accountId
     * @return \yii\web\Response
     */
    public function actionUpdate($accountId){
        $model = Account::findAccountById($accountId);
        if($model->state == '1001' || $model->state == '1004'){
            throw new HttpException(500, 'you can\'t update the account whose state is 1001 or 1004');
        }
        if($model->load(Yii::$app->request->post())){
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();//开启事务
            try {
                if($model->save()){//保存更改，stbbind表级联更改
                    //删除表stbbind中所有该account的预绑定信息
                    $db->createCommand()->delete('stbbind', 'accountId=:accountId', ['accountId' => $model->accountId])->execute();
                    if($model->state == '1002'){//如果更新后的状态为1002，表示有数据要向stbbin表中插入
                        $columns = ['accountId', 'productId', 'bindDay', 'isActive', 'activeDate'];
                        $rows = [];
                        foreach ($model->products as $product){
                            $row = [$model->accountId, $product, 356, 0, '3000-01-01'];
                            array_push($rows, $row);
                        }
                        $db->createCommand()->batchInsert('stbbind', $columns, $rows)->execute();
                        $transaction->commit();
                        return $this->redirect(['view', 'accountId' => $model->accountId]);
                    }
                }
            }catch (Exception $e){
                $transaction->rollBack();
                $model->addError('accountId', "update account $model->accountId failed! please try again.");
            }
        }
        $products = ArrayHelper::map(Product::find()->select(['productId', 'productName'])->all(), 'productId', 'productName');
        return $this->render('update', [
            'model' => $model,
            'products' => $products,
        ]);
    }
    /**
     * 删除account
     * @param string $accountId
     * @return \yii\web\Response
     */
    public function actionDelete($accountId){
        $model = Account::findAccountById($accountId);
        if($model->state == '1001' || $model->state == '1004'){
            throw new HttpException(500, 'you can\'t delete the account whose state is 1001 or 1004');
        }
        $model->delete();
        return $this->redirect(['index']);
    }
    /**
     * 设置account禁用
     * @param string $accountId
     * @return \yii\web\Response
     */
    public function actionDisable($accountId){
        $model = Account::findAccountById($accountId);
        $model->enable = 0;
        $model->save();
        return $this->redirect(['index']);
    }
    /**
     * 设置account启用
     * @param string $accountId
     * @return \yii\web\Response
     */
    public function actionEnable($accountId){
        $model = Account::findAccountById($accountId);
        $model->enable = 1;
        $model->save();
        return $this->redirect(['index']);
    }
    
}