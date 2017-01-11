<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\Threshold;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
class ThresholdController extends Controller{
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
                    'update' => ['get', 'post']
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
    
    public function actionIndex(){
        $model = Threshold::find()->one();
        return $this->render('index',[
            'model' => $model
        ]);
    }
    
    public function actionUpdate(){
        $model = Threshold::find()->one();
        $model->scenario = Threshold::SCENARIO_UPDATE;
        if($model->load(\Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['index']);
        }
        return $this->render('update',[
            'model' => $model
        ]);
    }
}