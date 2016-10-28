<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LanguageSearch;
use app\models\Language;
use yii\web\HttpException;
use yii\web\UploadedFile;
use yii\helpers\ArrayHelper;

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
                    'import' => ['get', 'post'],
                    'export' => ['get'],
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
        // for log
        $languageNames = [];
        foreach ($languageIds as $languageId){
            $lang = Language::findLanguageById($languageId);
            if(!empty($lang->channels)){
                throw new HttpException(500, "these languages contain language with channels using it, you can't delete it");
            }
            array_push($languageNames, $lang->languageName);
        }
        //使用","作为分隔符将数组转为字符串
        $languages = implode('","', $languageIds);
        //在最终的字符串前后各加一个"
        $languages = '"' . $languages . '"';
        $model = new Language();
        //调用model的deleteAll方法删除数据
        $model->deleteAll("languageId in($languages)");
        Yii::info('delete selected ' . count($languageNames) . ' languages, they are ' . implode(',', $languageNames), 'administrator');
        return $this->redirect(['index']);
    }
    
/**
     * import languages
     */
    public function actionImport(){
        $model = new Language();
        $model->scenario = Language::SCENARIO_IMPORT;
        $state = [
            'message' => 'Info:please import a xml file. Format as below:</br>'
            . '&lt;?xml version="1.0" encoding="UTF-8"?&gt;</br>'
            . '&lt;message&gt;</br>'
            . '&nbsp;&nbsp;&lt;Language&gt;</br>'
            . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;languageName&gt;English&lt;/languageName&gt;</br>'
            . '&nbsp;&nbsp;&lt;/Language&gt;</br>'
            . '&nbsp;&nbsp;&lt;Language&gt;</br>'
            . '&nbsp;&nbsp;&nbsp;&nbsp;······</br>'
            . '&nbsp;&nbsp;&lt;/Language&gt;</br>'
            . '&lt;/message&gt;</br>',
            'class' => 'alert-info',
            'percent' => 0,
            'label' => '0%',
        ];
        if($model->load(Yii::$app->request->post())){
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            try {
                $xmlArray = simplexml_load_file($model->importFile->tempName);
                $languages = json_decode(json_encode($xmlArray), true);
                $columns = ['languageName', 'createTime', 'updateTime'];
                $rows = ArrayHelper::getColumn($languages['Language'], function($element){
                    $now = date('Y-m-d H:i:s', time());
                    return [$element['languageName'], $now, $now];
                });
                    $db = Yii::$app->db;
                    $db->createCommand()->batchInsert('language', $columns, $rows)->execute();
                    $languageStr = implode(',', ArrayHelper::getColumn($languages['Language'], 'languageName'));
                    Yii::info("import " . count($rows) . " languages, they are $languageStr", 'administrator');
                    $state['message'] = 'Success:import success, there are totally ' . count($rows) .' languages added to DB, they are ' . $languageStr;
                    $state['class'] = 'alert-success';
                    $state['percent'] = 100;
                    $state['label'] = '100% completed';
            }catch (\Exception $e){
                $state['message'] = 'Error:' . $e->getMessage();
                $state['class'] = 'alert-danger';
            }
        }
        return $this->render('import', [
            'model' => $model,
            'state' => $state,
        ]);
    }
    /**
     * 导出所有的languages
     */
    public function actionExport(){
        $model = new Language();
        $productcards = $model->find()->all();
        $response = Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_XML,
            'formatters' => [
                \yii\web\Response::FORMAT_XML => [
                    'class' => 'yii\web\XmlResponseFormatter',
                    'rootTag' => 'message', //根节点
                    'itemTag' => 'language',
                ],
            ],
            'data' => $productcards,
        ]);
        $formatter = new \yii\web\XmlResponseFormatter();
        $formatter->rootTag = 'message';
        $formatter->format($response);
        Yii::$app->response->sendContentAsFile($response->content, 'languages.xml')->send();
        Yii::info('export all languages', 'administrator');
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
        $model->scenario = Language::SCENARIO_SAVE;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::info("create language $model->languageName", 'administrator');
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
        $model->scenario = Language::SCENARIO_SAVE;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::info("update language $model->languageName", 'administrator');
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
        Yii::info("delete language $model->languageName", 'administrator');
        return $this->redirect(['index']);
    }
}