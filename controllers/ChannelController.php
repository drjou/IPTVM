<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\ChannelSearch;
use app\models\Channel;
use app\models\Language;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class ChannelController extends Controller{
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
     * Index Action 显示所有的channel信息
     * @return string
     */
    public function actionIndex(){
        $searchModel = new ChannelSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * 删除选中的一些channels
     * @param unknown $keys
     * @return \yii\web\Response
     */
    public function actionDeleteAll($keys){
        //将得到的字符串转为php数组
        $channelIds = explode(',', $keys);
        //使用","作为分隔符将数组转为字符串
        $channels = implode('","', $channelIds);
        //在最终的字符串前后各加一个"
        $channels = '"' . $channels . '"';
        $model = new Channel();
        //调用model的deleteAll方法删除数据
        $model->deleteAll("channelId in($channels)");
        return $this->redirect(['index']);
    }
    /**
     * View Action 查看channel具体信息
     * @param int $channelId
     * @return string
     */
    public function actionView($channelId){
        $model = Channel::findChannelById($channelId);
        $productProvider = $model->findProducts();
        $directoryProvider = $model->findDirectories();
        return $this->render('view', [
            'model' => $model,
            'productProvider' => $productProvider,
            'directoryProvider' => $directoryProvider,
        ]);
    }
    /**
     * 创建新的channel
     * @return string
     */
    public function actionCreate(){
        $model = new Channel();
        $model->scenario = Channel::SCENARIO_ADD;
        if($model->load(Yii::$app->request->post())){
            $model->thumbnail = UploadedFile::getInstance($model, 'thumbnail');
            if($model->thumbnail && $model->save()){
                $dir = dirname(__DIR__) . '/web/images/channels';
                if(!is_dir($dir)){
                    mkdir($dir);
                }
                $model->thumbnail->saveAs('images/channels' . '/' . $model->thumbnail->baseName . '.' . $model->thumbnail->extension);
                return $this->redirect(['view', 'channelId' => $model->channelId]);
            }
        }
        $languages = ArrayHelper::map(Language::find()->all(), 'languageId', 'languageName');
        return $this->render('create', [
            'model' => $model,
            'languages' => $languages,
        ]);
    }
    /**
     * 更新指定的channel信息
     * @param int $channelId
     * @return \yii\web\Response
     */
    public function actionUpdate($channelId){
        $model = Channel::findChannelById($channelId);
        $model->scenario = Channel::SCENARIO_UPDATE;
        if($model->load(Yii::$app->request->post())){
            $model->thumbnail = UploadedFile::getInstance($model, 'thumbnail');
            if(!empty($model->thumbnail)){//如果更改了图片
                //先删除原图
                unlink(dirname(__DIR__) . '/web' . $model->channelPic);
                //保存结果到数据库
                if($model->save()){
                    $dir = dirname(__DIR__) . '/web/images/channels';
                    if(!is_dir($dir)){
                        mkdir($dir);
                    }
                    //保存新图到服务器
                    $model->thumbnail->saveAs('images/channels' . '/' . $model->thumbnail->baseName . '.' . $model->thumbnail->extension);
                    return $this->redirect(['view', 'channelId' => $model->channelId]);
                }
            }else{
                if($model->save()){
                    return $this->redirect(['view', 'channelId' => $model->channelId]);
                }
            }
        }
        $languages = ArrayHelper::map(Language::find()->all(), 'languageId', 'languageName');
        return $this->render('update', [
            'model' => $model,
            'languages' => $languages,
        ]);
    }
    
    /**
     * 删除指定的channel
     * @param int $channelId
     * @return \yii\web\Response
     */
    public function actionDelete($channelId){
        $model = Channel::findChannelById($channelId);
        unlink(dirname(__DIR__) . '/web' . $model->channelPic);
        $model->delete();
        return $this->redirect(['index']);
    }
}