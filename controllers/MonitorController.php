<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\CPUSearch;
use app\models\CPU;
use app\models\Server;

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
        $server = new Server();
        $data = Server::find()->all();
        return $this->render('index', ['server' => $server, 'data' => $data]);
    }
    /**
     * 显示CPU折线图
     */
    public function actionCpuChart(){
        $cpuData = CPU::find()->asArray()->all();
        return $this->render('cpu-chart', ['cpuData' => $cpuData]);
    }
    /**
     * 显示CPU表格
     */
    public function actionCpuGrid(){
        $searchModel = new CPUSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('cpu-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]); 
    }
    /**
     * 显示RAM折线图
     */
    public function actionRamChart(){
        return $this->render('ram-chart', []);
    }
}