<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Admin;

class ProfileController extends Controller{
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
                    'password-modify' => ['get', 'post'],
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
     * 显示个人信息
     * @return string
     */
    public function actionIndex(){
        $model = Admin::findAdminById(Yii::$app->user->identity->id);
        return $this->render('index', [
            'model' => $model,
        ]);
    }
    /**
     * 修改个人信息
     * @return \yii\web\Response|string
     */
    public function actionInfoModify(){
        $model = Admin::findAdminById(Yii::$app->user->identity->id);
        $model->scenario = Admin::SCENARIO_UPDATE;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['index']);
        }
        return $this->render('info-modify', [
            'model' => $model,
        ]);
    }
    /**
     * 修改密码
     * @return string
     */
    public function actionPasswordModify(){
        $model = Admin::findAdminById(Yii::$app->user->identity->id);
        $model->scenario = Admin::SCENARIO_PASSWORD;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['index']);
        }
        return $this->render('password-modify', [
            'model' => $model,
        ]);
    }
}