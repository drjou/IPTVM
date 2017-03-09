<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\Server;
use yii\helpers\ArrayHelper;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\UploadedFile;
use app\models\StreamSearch;
use app\models\Stream;

class StreamController extends Controller{
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
                    'delete-all' => ['get'],
                    'create' => ['get', 'post'],
                    'delete' => ['get'],
                    'import' => ['get', 'post'],
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
    /**
     * 显示所有stream信息
     */
    public function actionIndex(){
        $filterModel = new StreamSearch();
        $dataProvider = $filterModel->search(Yii::$app->request->queryParams);
        $allServers = Server::find()->asArray()->all();
        $servers = ArrayHelper::map($allServers, 'serverName', 'serverName');
        return $this->render('index',[
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
            'servers' => $servers
        ]);
    }
    /**
     * 查看stream详情
     * @param string $streamName
     * @param string $server
     */
    public function actionView($streamName, $server){
        $model = Stream::findStreamByKey($streamName, $server);
        return $this->render('view',[
            'model' => $model
        ]);
    }
    /**
     * 创建新的stream
     */
    public function actionCreate(){
        $model = new Stream();
        $model->scenario = Stream::SCENARIO_SAVE;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::info("create a stream named $model->streamName on the server $model->server", 'stream');
            $this->redirect(['view', 'streamName' => $model->streamName, 'server' => $model->server]);
        }
        $allServers = Server::find()->asArray()->all();
        $servers = ArrayHelper::map($allServers, 'serverName', 'serverName');
        return $this->render('create',[
            'model' => $model,
            'servers' => $servers
        ]);
    }
    /**
     * 删除指定的stream
     * @param string $streamName
     * @param string $server
     * @return \yii\web\Response
     */
    public function actionDelete($streamName, $server){
        $model = Stream::findStreamByKey($streamName, $server);
        $model->delete();
        Yii::info("delete $model->streamName on $model->server", 'Stream');
        return $this->redirect(['index']);
    }
    /**
     * 批量删除stream
     * @param string $keys
     */
    public function actionDeleteAll($keys){
        $stream = json_decode($keys);
        $streams = [];
        for($i=0;$i<count($stream);$i++){
            $p = '("' . $stream[$i]->streamName . '","' . $stream[$i]->server . '")';
            array_push($streams, $p);
        }
        $pr = implode(',', $streams);
        $model = new Stream();
        $model->deleteAll("(streamName,server) in ($pr)");
        Yii::info("delete selected " . count($stream) . " streams", 'Stream');
        return $this->redirect(['index']);
    }
    /**
     * 批量导入stream
     * @return string
     */
    public function actionImport(){
        $model = new Stream();
        $model->scenario = Stream::SCENARIO_IMPORT;
        $state = [
            'message' => 'Info:please import a xml file. Format as below:</br>'
                        . '&lt;?xml version="1.0" encoding="UTF-8"?&gt;</br>'
                        . '&lt;message&gt;</br>'
                        . '&nbsp;&nbsp;&lt;Stream&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;streamName&gt;nirvana6&lt;/streamName&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;source&gt;source&lt;/source&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;server&gt;server1&lt;/server&gt;</br>'
                        . '&nbsp;&nbsp;&lt;/Stream&gt;</br>'
                        . '&nbsp;&nbsp;&lt;Stream&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;······</br>'
                        . '&nbsp;&nbsp;&lt;/Stream&gt;</br>'
                        . '&lt;/message&gt;</br>',
            'class' => 'alert-info',
            'percent' => 0,
            'label' => '0%',
        ];
        if($model->load(Yii::$app->request->post())){
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            try {
                $xmlArray = simplexml_load_file($model->importFile->tempName);
                $streams = json_decode(json_encode($xmlArray), true);
                $columns = ['streamName', 'status', 'source', 'sourceStatus', 'server', 'createTime', 'updateTime'];
                $allStreams = null;
                if(ArrayHelper::isIndexed($streams['Stream'])){
                    $allStreams = $streams['Stream'];
                }else{
                    $allStreams = [$streams['Stream']];
                }
                $rows = ArrayHelper::getColumn($allStreams, function($element){
                    $now = date('Y-m-d H:i:s', time());
                    return [$element['streamName'], 1, $element['source'], 1, $element['server'], $now, $now];
                });
                    $db = Yii::$app->db;
                    $db->createCommand()->batchInsert('stream', $columns, $rows)->execute();
                    $streamStr = implode(',', ArrayHelper::getColumn($allStreams, 'streamName'));
                    $serverStr = implode(',', ArrayHelper::getColumn($allStreams, 'server'));
                    Yii::info("import " . count($rows) . " streams, they are $streamStr on $serverStr respectively", 'Stream');
                    $state['message'] = 'Success:import success, there are totally ' . count($rows) .' streams added to DB, they are ' . $streamStr . ' on ' . $serverStr . ' respectively';
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
     * 更新进程信息
     * @param string $processName
     * @param string $server
     */
    public function actionUpdate($streamName, $server){
        $model = Stream::findStreamByKey($streamName, $server);
        $model->scenario = Stream::SCENARIO_SAVE;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::info("Update $model->streamName on $model->server", 'Stream');
            return $this->redirect(['view', 'streamName' => $model->streamName, 'server' => $model->server]);
        }
        $allServers = Server::find()->asArray()->all();
        $servers = ArrayHelper::map($allServers, 'serverName', 'serverName');
        return $this->render('update',[
            'model' => $model,
            'servers' => $servers
        ]);
    }
    
}