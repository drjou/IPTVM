<?php
namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class MonitorController extends Controller{
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
     * Dashboard action
     */
    public function actionIndex(){
        //$cpu = new Admin();
        return $this->render('index', []);
    }
    /**
     * 显示CPU折线图
     */
    public function actionCpuChart(){
        return $this->render('cpu-chart', []);
    }
}