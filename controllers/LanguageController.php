<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LanguageSearch;
use app\models\Language;
use yii\web\HttpException;

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
                    'view' => ['get'],
                    'create' => ['get', 'post'],
                    'update' => ['get', 'post'],
                    'delete' => ['get'],
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
        foreach ($languageIds as $languageId){
            $lang = Language::findLanguageById($languageId);
            if(!empty($lang->channels)){
                throw new HttpException(500, "these languages contain language with channels using it, you can't delete it");
            }
        }
        //使用","作为分隔符将数组转为字符串
        $languages = implode('","', $languageIds);
        //在最终的字符串前后各加一个"
        $languages = '"' . $languages . '"';
        $model = new Language();
        //调用model的deleteAll方法删除数据
        $model->deleteAll("accountId in($languages)");
        return $this->redirect(['index']);
    }
    /**
     * 查看language的详细信息
     * @param int $languageId
     * @return string
     */
    public function actionView($languageId){
        $model = Language::findLanguageById($languageId);
        $channelProvider = $model->findChannels();
        return $this->render('view', [
            'model' => $model,
            'channelProvider' => $channelProvider,
        ]);
    }
    /**
     * 创建新的language
     * @return \yii\web\Response|string
     */
    public function actionCreate(){
        $model = new Language();
        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['view', 'languageId' => $model->languageId]);
        }
        return $this->render('create', [
            'model' => $model,
        ]);
    }
    /**
     * 修改指定的language
     * @param int $languageId
     * @return \yii\web\Response|string
     */
    public function actionUpdate($languageId){
        $model = Language::findLanguageById($languageId);
        if($model->load(Yii::$app->request->post()) && $model->save()){
            return $this->redirect(['view', 'languageId' => $model->languageId]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }
    /**
     * 删除指定的language
     * @param int $languageId
     * @throws HttpException
     * @return \yii\web\Response
     */
    public function actionDelete($languageId){
        $model = Language::findLanguageById($languageId);
        if(!empty($model->channels)){
            throw new HttpException(500, "You can't delete the language with channels using it.");
        }
        $model->delete();
        return $this->redirect(['index']);
    }
}