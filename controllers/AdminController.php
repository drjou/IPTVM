<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;

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
                    'logout' => ['get'],
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
}