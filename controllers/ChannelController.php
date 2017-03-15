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
        $channelNames = [];
        foreach ($channelIds as $channelId){
            $channel = Channel::findChannelById($channelId);
            array_push($channelNames, $channel->channelName);
        }
        //使用","作为分隔符将数组转为字符串
        $channels = implode('","', $channelIds);
        //在最终的字符串前后各加一个"
        $channels = '"' . $channels . '"';
        $model = new Channel();
        //调用model的deleteAll方法删除数据
        $model->deleteAll("channelId in($channels)");
        Yii::info('delete selected ' . count($channelNames) . ' channels, they are ' . implode(',', $channelNames), 'administrator');
        return $this->redirect(['index']);
    }
    /**
     * 文件导入channels信息
     * @return string
     */
    public function actionImport(){
        $model = new Channel();
        $model->scenario = Channel::SCENARIO_IMPORT;
        $state = [
            'message' => 'Info:please import a xml file. Format as below:</br>'
                        . '&lt;?xml version="1.0" encoding="UTF-8"?&gt;</br>'
                        . '&lt;message&gt;</br>'
                        . '&nbsp;&nbsp;&lt;Channel&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;channelName&gt;bein&lt;/channelName&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;channelIp&gt;188.138.89.40&lt;/channelIp&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;channelPic&gt;/images/bein1.jpg&lt;/channelPic&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;channelUrl&gt;http://188.138.89.40/IPTV_Files/bein1/bein1.m3u8.jpg&lt;/channelUrl&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;urlType&gt;entire&lt;/urlType&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;channelType&gt;live&lt;/channelType&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;languageId&gt;1&lt;/languageId&gt;</br>'
                        . '&nbsp;&nbsp;&lt;/Channel&gt;</br>'
                        . '&nbsp;&nbsp;&lt;Channel&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;······</br>'
                        . '&nbsp;&nbsp;&lt;/Channel&gt;</br>'
                        . '&lt;/message&gt;</br>',
            'class' => 'alert-info',
            'percent' => 0,
            'label' => '0%',
        ];
        if($model->load(Yii::$app->request->post())){
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            try {
                $xmlArray = simplexml_load_file($model->importFile->tempName);
                $channels = json_decode(json_encode($xmlArray), true);
                $columns = ['channelName', 'channelIp', 'channelPic', 'channelUrl', 'urlType', 'channelType', 'languageId', 'createTime', 'updateTime'];
                $rows = ArrayHelper::getColumn($channels['Channel'], function($element){
                    $now = time();
                    return [$element['channelName'], $element['channelIp'], $element['channelPic'], $element['channelUrl'], $element['urlType'], $element['channelType'], $element['languageId'], $now, $now];
                });
                    $db = Yii::$app->db;
                    $db->createCommand()->batchInsert('channel', $columns, $rows)->execute();
                    $channelStr = implode(',', ArrayHelper::getColumn($channels['Channel'], 'channelName'));
                    Yii::info("import " . count($rows) . " channels, they are $channelStr", 'administrator');
                    $state['message'] = 'Success:import success, there are totally ' . count($rows) .' channels added to DB, they are ' . $channelStr;
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
     * 导出所有的channels信息
     */
    public function actionExport(){
        $model = new Channel();
        $channels = $model->find()->all();
        $response = Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_XML,
            'formatters' => [
                \yii\web\Response::FORMAT_XML => [
                    'class' => 'yii\web\XmlResponseFormatter',
                    'rootTag' => 'message', //根节点
                    'itemTag' => 'channel',
                ],
            ],
            'data' => $channels,
        ]);
        $formatter = new \yii\web\XmlResponseFormatter();
        $formatter->rootTag = 'message';
        $formatter->format($response);
        Yii::$app->response->sendContentAsFile($response->content, 'channels.xml')->send();
        Yii::info('export all channels', 'administrator');
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
                Yii::info("create channel $model->channelName", 'administrator');
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
                    Yii::info("update channel $model->channelName", 'administrator');
                    return $this->redirect(['view', 'channelId' => $model->channelId]);
                }
            }else{
                if($model->save()){
                    Yii::info("update channel $model->channelName", 'administrator');
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
        Yii::info("delete channel $model->channelName", 'administrator');
        return $this->redirect(['index']);
    }
}