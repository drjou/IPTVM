<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LanguageSearch;
use app\models\Language;

class LanguageController extends Controller{
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
                    'delete-all' => ['get'],
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
            ],
        ];
    }
    /**
     * Index Action 显示所有的language信息
     * @return string
     */
    public function actionIndex(){
        $searchModel = new LanguageSearch();
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
        $languageIds = explode(',', $keys);
        //使用","作为分隔符将数组转为字符串
        $languages = implode('","', $languageIds);
        //在最终的字符串前后各加一个"
        $languages = '"' . $languages . '"';
        $model = new Language();
        //调用model的deleteAll方法删除数据
        $model->deleteAll("accountId in($languages)");
        return $this->redirect(['index']);
    }
}