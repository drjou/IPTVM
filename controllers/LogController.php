<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\AdminLogSearch;
use app\models\StbLogSearch;
use app\models\AdminLog;
use app\models\StbLog;

class LogController extends Controller{
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
                    'admin' => ['get'],
                    'stb' => ['get'],
                    'admin-view' => ['get'],
                    'stb-view' => ['get'],
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
     * admin log list
     * @return string
     */
    public function actionAdmin(){
        $searchModel = new AdminLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * admin log detail
     * @param int $id
     * @return string
     */
    public function actionAdminView($id){
        $model = AdminLog::findAdminLogById($id);
        return $this->render('admin-view', [
            'model' => $model,
        ]);
    }
    
    /**
     * stb log list
     * @return string
     */
    public function actionStb(){
        $searchModel = new StbLogSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('stb', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * stb log detail
     * @param int $id
     * @return string
     */
    public function actionStbView($id){
        $model = StbLog::findStbLogById($id);
        return $this->render('stb-view', [
            'model' => $model,
        ]);
    }
}