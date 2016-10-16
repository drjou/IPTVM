<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\AdminSearch;
use app\models\Admin;

class AdminController extends Controller{
    /**
     * 设置访问权限
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
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
                    'logout' => ['get', 'post'],
                    'index' => ['get'],
                    'delete-all' => ['get'],
                ]
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
            'captcha' =>  [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
                'height' => 34,
                'width' => 80,
                'minLength' => 4,
                'maxLength' => 4
            ],
        ];
    }
    /**
     * Login Action
     * @return \yii\web\Response|string
     */
    public function actionLogin(){
        //如果用户已登录，则直接跳转到主页
        if(!Yii::$app->user->isGuest){
            return $this->redirect(['site/index']);
        }
        
        $model = new LoginForm();
        //如果用户输入用户名密码登录，并验证成功，则跳转到其登录前访问的页面
        if($model->load(Yii::$app->request->post()) && $model->login()){
            Yii::info('login success', 'admin');
            return $this->goBack();
        }
        //如果未登录，则进入登录页面
        return $this->renderPartial('login', [
            'model' => $model,
        ]);
    }
    /**
     * Logout Action
     * @return \yii\web\Response
     */
    public function actionLogout(){
        Yii::$app->user->logout();
        return $this->goHome();
    }
    /**
     * Index Action 显示所有的管理员信息
     * @return string
     */
    public function actionIndex(){
        $searchModel = new AdminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]); 
    }
    
    /**
     * 删除选中的一些管理员
     * @param string $keys
     * @return \yii\web\Response
     */
    public function actionDeleteAll($keys){
        //将得到的字符串转为php数组
        $ids = explode(',', $keys);
        //使用","作为分隔符将数组转为字符串
        $admins = implode('","', $ids);
        //在最终的字符串前后各加一个"
        $admins = '"' . $admins . '"';
        $model = new Admin();
        //调用model的deleteAll方法删除数据
        $model->deleteAll("id in($admins)");
        return $this->redirect(['index']);
    }
    
}