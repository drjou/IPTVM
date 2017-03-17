<?php
namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\Timezone;
use yii\web\HttpException;
use app\models\TimezoneSearch;
use yii\helpers\Url;

class TimezoneController extends Controller{
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
                    'view' => ['get'],
                    'create' => ['get', 'post'],
                    'update' => ['get', 'post'],
                    'set-timezone' => ['get'],
                    'delete' => ['get'],
                    'enable' => ['get'],
                    'disable' => ['get'],
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
     * 获取全部的timezone信息
     */
    public function actionIndex(){
        $searchModel = new TimezoneSearch();
        $dataProvider = $searchModel->search(\Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * 查看时区详情
     * @param string $timezone
     * @return string
     */
    public function actionView($timezone){
        $model = Timezone::findOne($timezone);
        return $this->render('view', [
            'model' => $model,
        ]);
    }
    /**
     * 新增timezone
     * @return string
     */
    public function actionCreate(){
        $model = new Timezone();
        if($model->load(\Yii::$app->request->post()) && $model->save()){
            \Yii::info("create timezone $model->timezone", 'administrator');
            return $this->redirect(['view', 'timezone' => $model->timezone]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    
    /**
     * 修改timezone
     * @return string
     */
    public function actionUpdate($timezone){
        $model = Timezone::findOne($timezone);
        if($model->load(\Yii::$app->request->post()) && $model->save()){
            \Yii::info("update timezone $model->timezone", 'administrator');
            return $this->redirect(['view', 'timezone' => $model->timezone]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    
    /**
     * 设置时区
     * @param string $timezone
     * @return \yii\web\Response
     */
    public function actionSetTimezone($timezone){
        $current = Timezone::getCurrentTimezone();
        $new = Timezone::findOne($timezone);
        $new->isCurrent  = 1;
        if($new->save()){
            $current->isCurrent = 0;
            $current->save();
            \Yii::info("set current timezone $new->timezone", 'administrator');
            return $this->redirect(\Yii::$app->request->referrer);
        }else{
            throw new HttpException(500, 'failed to modify timezone!');
        }
    }
    
    /**
     * 删除时区
     * @param string $timezone
     */
    public function actionDelete($timezone){
        $model = Timezone::findOne($timezone);
        if($model->isCurrent == 1){
            throw new HttpException(500, 'you can\'t delete the current timezone' );
        }
        $model->delete();
        \Yii::info("delete timezone $model->timezone", 'administrator');
        return $this->redirect(['index']);
    }
    
    /**
     * 启用时区
     * @param string $timezone
     */
    public function actionEnable($timezone){
        $model = Timezone::findOne($timezone);
        $model->status = 1;
        $model->save();
        \Yii::info("enable timezone $model->timezone", 'administrator');
        return $this->redirect(['index']);
    }
    
    /**
     * 禁用时区
     * @param string $timezone
     */
    public function actionDisable($timezone){
        $model = Timezone::findOne($timezone);
        if($model->isCurrent == 1){
            throw new HttpException(500, 'you can\'t disable the current timezone' );
        }
        $model->status = 0;
        $model->save();
        \Yii::info("disable timezone $model->timezone", 'administrator');
        return $this->redirect(['index']);
    }
}