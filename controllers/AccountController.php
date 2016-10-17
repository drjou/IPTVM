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
        $productProvider = $model->findProducts();
        $productcardProvider = $model->findProductcards();
        return $this->render('view', [
            'model' => $model,
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
        $products = ArrayHelper::map(Product::find()->select(['productId', 'productName'])->all(), 'productId', 'productName');
        return $this->render('create', [
            'model' => $model,
            'products' => $products,
        ]);
    }
    
}