<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\CPUSearch;
use app\models\CPU;
use app\models\Server;
use yii\helpers\ArrayHelper;
use app\models\RAM;
use app\models\Disk;
use app\models\Load;
use app\models\RAMSearch;
use app\models\DiskSearch;
use app\models\LoadSearch;
use app\models\RealTime;
use app\models\Threshold;
use yii\db\Query;
use app\models\MySqlSearch;
use app\models\NginxSearch;
use app\models\StreamInfo;
use app\models\StreamInfoSearch;
use app\models\Stream;
use app\models\ServerSearch;
use app\models\StreamSearch;
use app\models\Nginx;
use app\models\NginxInfo;
use app\models\MysqlInfo;
use app\models\MySql;
use app\models\OnlineClientSearch;
use app\models\StreamingLogSearch;
use app\models\StreamingLog;
use app\models\AgentLog;
use app\models\AgentLogSearch;
use app\models\StreamAccessLogSearch;
use app\models\StreamAccessLog;
use app\models\NginxInfoSearch;
use app\models\MysqlInfoSearch;
use app\models\Timezone;

class MonitorController extends Controller
{

    /**
     * 设置访问权限
     * 
     * {@inheritDoc}
     *
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => [
                            '@'
                        ]
                    ]
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'servers-fault' => ['get'],
                    'servers-status' => ['get','post'],
                    'streams-monitor' => ['get','post'],
                    'cpu-chart' => ['get'],
                    'cpu-grid' => ['get'],
                    'ram-chart' => ['get'],
                    'ram-grid' => ['get'],
                    'disk-chart' => ['get'],
                    'disk-grid' => ['get'],
                    'load-chart' => ['get'],
                    'load-grid' => ['get'],
                    'mysql-grid' => ['get'],
                    'streams' => ['get'],
                    'streams-grid' => ['get'],
                    'servers' => ['get'],
                    'server-detail' => ['get','post'],
                    'update-gauge-info' => ['get'],
                    'update-line-info' => ['get'],
                    'update-heat-map' => ['get'],
                    'update-warning-line' => ['get'],
                ]
            ]
        ];
    }

    /**
     * 独立操作
     * 
     * {@inheritDoc}
     *
     * @see \yii\base\Controller::actions()
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction'
            ]
        ];
    }
    /**
     * Dashboard action
     */
    public function actionIndex()
    {
        $servers = $this->getRealtimeServerStatus();
        $streams = $this->getRealtimeStreamStatus();
        $mysqls = $this->getRealtimeMysqlStatus();
        $nginxes = $this->getRealtimeNginxStatus();
        $onlineClients = $this->getOnlineClients();
        $filter = [
            0 => 'DOWN',
            1 => 'UP'
        ];
        return $this->render('index',[
            'servers' => $servers,
            'streams' => $streams,
            'mysqls' => $mysqls,
            'nginxes' => $nginxes,
            'onlineClients' => $onlineClients,
            'filter' => $filter,
        ]);
    }
    /**
     * Servers Status action
     */
    public function actionServersStatus(){
        $filterModel = new ServerSearch();
        $dataProvider = $filterModel->search(\Yii::$app->request->queryParams);
        $model = new Server();
        $model->scenario = Server::SCENARIO_SELECT_SERVERS;
        if($model->load(Yii::$app->request->post())){
            $serversStr = implode(',', $model->servers);
            return $this->redirect(['servers','servers'=>$serversStr]);
        }
        $servers = ArrayHelper::map(Server::find()->asArray()->all(), 'serverName', 'serverName');
        return $this->render('servers-status', [
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
            'model' => $model,
            'servers' => $servers
        ]);
    }
    
    /**
     * Servers Fault action
     */
    public function actionServersFault()
    {
        $startTime = time()-24*3600;
        $endTime = time();
        $range = $this->getDateRange($startTime, $endTime);
        $minDate = Timezone::date(CPU::find()->min('recordTime'));
        $threshold = Threshold::find()->one();
        $servers = Server::find()->asArray()->all();
        $data = [];
        for($i=0;$i<count($servers);$i++){
            array_push($data, [
                'name'=>$servers[$i]['serverName'],
                'data'=>[]
            ]);
        }
        $mySqlData = [
            ['name'=>'count','data'=>[]]
        ];
        $nginxData = $this->getNginxWarningData($startTime, $endTime);
        $nginxData2 = $this->getNginxServers($startTime, $endTime);
        return $this->render('servers-fault', [
            'cpuData' => $data,
            'ramData' => $data,
            'diskData' => $data,
            'loadData' => $data,
            'streamData' => $data,
            'mySqlData' => $mySqlData,
            'nginxData' => $mySqlData,
            'range' =>  $range,
            'minDate' => $minDate,
            'cpuThreshold' => $threshold->cpu+0,
            'memoryThreshold' => $threshold->memory+0,
            'diskThreshold' => $threshold->disk+0,
            'loadsThreshold' => $threshold->loads+0
        ]);
    }

    /**
     * stream-monitor界面所需数据
     * @param string $serverName
     */
    public function actionStreamsMonitor($serverName=null)
    {
        if($serverName===null){
            $serverName = Server::find()->one()->serverName;
        }
        $model = new Server();
        $model->serverName = $serverName;
        $model->scenario = Server::SCENARIO_SELECT_STREAMS;
        if($model->load(Yii::$app->request->post())){
            return $this->redirect(['streams', 'serverName'=>$model->serverName, 'streams'=>implode(',', $model->streams)]);
        }
        $streams = ArrayHelper::map($model->getStreams()->all(), 'streamName', 'streamName');
        $filterModel = new StreamSearch();
        $dataProvider = $filterModel->searchOnSomeServer(Yii::$app->request->queryParams, $serverName);
        return $this->render('streams-monitor',[
            'servers' => $this->getServersForDrop(),
            'model' => $model,
            'streams' => $streams,
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
            'serverName' => $serverName
        ]);
    }
    /**
     * streaming log界面数据
     * @return string
     */
    public function actionStreamingLog(){
        $filterModel = new StreamingLogSearch();
        $dataProvider = $filterModel->search(Yii::$app->request->queryParams);
        return $this->render('streaming-log',[
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider
        ]);
    }
    /**
     * 删除一个streaming log
     */
    public function actionDeleteStreamingLog($id){
        $model = StreamingLog::findLogById($id);
        $model->delete();
        Yii::info("delete a streaming log named $id", 'monitor');
        return $this->redirect(['streaming-log']);
    }
    /**
     * agent log界面数据
     * @return string
     */
    public function actionAgentLog(){
        $filterModel = new AgentLogSearch();
        $dataProvider = $filterModel->search(Yii::$app->request->queryParams);
        return $this->render('agent-log',[
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider
        ]);
    }
    /**
     * 删除一个agent log
     */
    public function actionDeleteAgentLog($id){
        $model = AgentLog::findLogById($id);
        $model->delete();
        Yii::info("delete a agent log named $id", 'monitor');
        return $this->redirect(['agent-log']);
    }
    /**
     * stream access log界面数据
     * @return string
     */
    public function actionStreamAccessLog(){
        $filterModel = new StreamAccessLogSearch();
        $dataProvider = $filterModel->search(Yii::$app->request->queryParams);
        return $this->render('stream-access-log',[
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider
        ]);
    }
    /**
     * 删除一个stream access log
     */
    public function actionDeleteStreamAccessLog($id){
        $model = StreamAccessLog::findLogById($id);
        $model->delete();
        Yii::info("delete a stream access log named $id", 'monitor');
        return $this->redirect(['stream-access-log']);
    }
    
    /**
     * 传回cpu折线图相关数据
     * @param string $serverName
     */
    public function actionCpuChart($serverName)
    {
        $startTime = time()-24*3600;
        $endTime = time();
        $range = $this->getDateRange($startTime, $endTime);
        $minDate = Timezone::date(CPU::find()->where(['server'=>$serverName])->min('recordTime'));
        $data = [
            [
                'name' => 'utilize',
                'data' => []
            ],
            [
                'name' => 'user',
                'data' => []
            ],
            [
                'name' => 'system',
                'data' => []
            ],
            [
                'name' => 'wait',
                'data' => []
            ],
            [
                'name' => 'hardIrq',
                'data' => []
            ],
            [
                'name' => 'softIrq',
                'data' => []
            ],
            [
                'name' => 'nice',
                'data' => []
            ],
            [
                'name' => 'steal',
                'data' => []
            ],
            [
                'name' => 'guest',
                'data' => []
            ],
            [
                'name' => 'idle',
                'data' => []
            ]
        ];;
        return $this->render('cpu-chart', [
            'data' => $data,
            'range' =>  $range,
            'minDate' => $minDate
        ]);
    }
    
    

    /**
     * 传回CPU表格数据
     */
    public function actionCpuGrid($serverName, $type)
    {
        $searchModel = new CPUSearch();
        $dataProvider = null;
        if($type==1){
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }else{
            $dataProvider = $searchModel->searchWarning(Yii::$app->request->queryParams);
        }
        return $this->render('cpu-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'servers' => $this->getServersForDrop()
        ]);
    }

    /**
     * 传回RAM折线图数据
     */
    public function actionRamChart($serverName)
    {
        $startTime = time()-24*3600;
        $endTime = time();
        $range = $this->getDateRange($startTime, $endTime);
        $minDate = Timezone::date(RAM::find()->where(['server'=>$serverName])->min('recordTime'));
        $data = [
            [
                'name' => 'utilize',
                'data' => []
            ]
        ];
        return $this->render('ram-chart', [
            'data' => $data,
            'range' => $range,
            'minDate' => $minDate
        ]);
    }
    /**
     * 传回RAM表格数据
     * @param string $serverName
     */
    public function actionRamGrid($serverName, $type){
        $searchModel = new RAMSearch();
        $dataProvider = null;
        if($type==1){
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }else{
            $dataProvider = $searchModel->searchWarning(Yii::$app->request->queryParams);
        }
        return $this->render('ram-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'servers' => $this->getServersForDrop()
        ]);
    }
    
    /**
     * 传回Disk折线图数据
     * @param string $serverName
     */
    public function actionDiskChart($serverName){
        $startTime = time()-24*3600;
        $endTime = time();
        $range = $this->getDateRange($startTime, $endTime);
        $minDate = Timezone::date(Disk::find()->where(['server'=>$serverName])->min('recordTime'));
        $data = [
            [
                'name' => 'used',
                'data' => []
            ],
            [
                'name' => 'free',
                'data' => []
            ]
        ];
        return $this->render('disk-chart', [
            'data' => $data,
            'range' => $range,
            'minDate' => $minDate
        ]);
    }
    
    /**
     * 传回Disk表格数据
     * @param string $serverName
     */
    public function actionDiskGrid($serverName, $type){
        $searchModel = new DiskSearch();
        $dataProvider = null;
        if($type==1){
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }else{
            $dataProvider = $searchModel->searchWarning(Yii::$app->request->queryParams);
        }
        return $this->render('disk-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'servers' => $this->getServersForDrop()
        ]);
    }
    
    /**
     * 传回Load折线图数据
     * @param string $serverName
     */
    public function actionLoadChart($serverName){
        $startTime = time()-24*3600;
        $endTime = time();
        $range = $this->getDateRange($startTime, $endTime);
        $minDate = Timezone::date(Load::find()->where(['server'=>$serverName])->min('recordTime'));
        $data = [
            [
                'name' => 'load of 1 minute',
                'data' => []
            ],
            [
                'name' => 'load of 5 minute',
                'data' => []
            ],
            [
                'name' => 'load of 15 minute',
                'data' => []
            ]
        ];
        return $this->render('load-chart', [
            'data' => $data,
            'range' => $range,
            'minDate' => $minDate
        ]);
    }
    
    /**
     * 传回Disk表格数据
     * @param string $serverName
     */
    public function actionLoadGrid($serverName, $type){
        $searchModel = new LoadSearch();
        $dataProvider = null;
        if($type==1){
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }else{
            $dataProvider = $searchModel->searchWarning(Yii::$app->request->queryParams);
        }
        return $this->render('load-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'servers' => $this->getServersForDrop()
        ]);
    }
    /**
     * MySql数据
     */
    public function actionMysqlGrid(){
        $filterModel = new MySqlSearch();
        $dataProvider = $filterModel->search(Yii::$app->request->queryParams);
        return $this->render('mysql-grid',[
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
            'servers' => $this->getServersForDrop()
        ]);
    }
    /**
     * MySqlInfo数据
     */
    public function actionMysqlInfoGrid($serverName){
        $filterModel = new MysqlInfoSearch();
        $dataProvider = $filterModel->search(Yii::$app->request->queryParams);
        return $this->render('mysql-info-grid',[
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
            'servers' => $this->getServersForDrop()
        ]);
    }
    /**
     * Nginx数据
     */
    public function actionNginxGrid(){
        $filterModel = new NginxSearch();
        $dataProvider = $filterModel->search(Yii::$app->request->queryParams);
        return $this->render('nginx-grid',[
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
            'servers' => $this->getServersForDrop()
        ]);
    }
    /**
     * NginxInfo数据
     */
    public function actionNginxInfoGrid($serverName){
        $filterModel = new NginxInfoSearch();
        $dataProvider = $filterModel->search(Yii::$app->request->queryParams);
        return $this->render('nginx-info-grid',[
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
            'servers' => $this->getServersForDrop()
        ]);
    }
    /**
     * 将串流进程的总利用率和内存利用率数据传回
     * @param string $serverName 服务器名
     */
    public function actionStreams($serverName, $streams)
    {
        $server = new Server();
        $startTime = time()-24*3600;
        $endTime = time();
        $range = $this->getDateRange($startTime, $endTime);
        $minDate = Timezone::date(CPU::find()->where(['server'=>$serverName])->min('recordTime'));
        $streamArr = explode(',', $streams);
        $data = [];
        for($i=0;$i<count($streamArr);$i++){
            array_push($data, [
                'name'=>$streamArr[$i],
                'data'=>[]
            ]);
        }
        return $this->render('streams', [
            'totalData' => $data,
            'memoryData' => $data,
            'range' => $range,
            'minDate' => $minDate
        ]);
    }
    
    /**
     * 传回表格中的数据
     */
    public function actionStreamsGrid(){
        $searchModel = new StreamSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('streams-grid',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'servers' => $this->getServersForDrop()
        ]);
    }
    
    public function actionStreamInfoGrid($serverName, $streamName, $streams){
        $filterModel = new StreamInfoSearch();
        $dataProvider = null;
        if($streams===''){
            $dataProvider = $filterModel->search(Yii::$app->request->queryParams, null, null);
        }else{
            $dataProvider = $filterModel->search(Yii::$app->request->queryParams, $streams, $serverName);
        }
        return $this->render('streams-info-grid',[
            'filterModel' => $filterModel,
            'dataProvider' => $dataProvider,
            'servers' => $this->getServersForDrop()
        ]);
    }
    
    /**
     * 传回不同服务器的相关数据
     */
    public function actionServers($servers){
        $startTime = time()-24*3600;
        $endTime = time();
        $range = $this->getDateRange($startTime, $endTime);
        $minDate = Timezone::date(CPU::find()->min('recordTime'));
        $realTimes = RealTime::find()->asArray()->all();
        $serverArr = explode(',', $servers);
        $data = [];
        for($i=0;$i<count($serverArr);$i++){
            array_push($data, [
                'name'=>$serverArr[$i],
                'data'=>[]
            ]);
        }
        return $this->render('servers', [
            'cpuData' => $data,
            'ramData' => $data,
            'diskData' => $data,
            'loadData' => $data,
            'range' => $range,
            'minDate' => $minDate,
        ]);
    }
    /**
     * 获取服务器详细信息
     * @param string $serverName
     */
    public function actionServerDetail($serverName){
        $model = new Server();
        $model->serverName = $serverName;
        return $this->render('server-detail',[
            'model' => $model,
            'servers' => $this->getServersForDrop()
        ]);
    }
    /**
     * 获取串流详细信息
     * @param string $serverName
     * @param string $streamName
     */
    public function actionStreamDetail($serverName, $streamName){
        $model = Stream::findStreamByKey($streamName, $serverName);
        $startTime = time()-24*3600;
        $endTime = time();
        $range = $this->getDateRange($startTime, $endTime);
        $minDate = Timezone::date(CPU::find()->where(['server'=>$serverName])->min('recordTime'));
        $cpuData = [
            ['name'=>'Server CPU','data'=>[]],
            ['name'=>'Stream CPU','data'=>[]]
        ];
        $ramData = [
            ['name'=>'Server RAM','data'=>[]],
            ['name'=>'Stream RAM','data'=>[]]
        ];
        return $this->render('stream-detail', [
            'model' => $model,
            'range' =>  $range,
            'minDate' => $minDate,
            'cpuData' => $cpuData,
            'ramData' => $ramData
        ]);
    }
    
    public function actionMysqlChart($serverName){
        $startTime = time()-24*3600;
        $endTime = time();
        $range = $this->getDateRange($startTime, $endTime);
        $minDate = Timezone::date(MysqlInfo::find()->where(['server'=>$serverName])->min('recordTime'));
        $data = [
            [
                'name' => 'totalConnections',
                'data' => []
            ],
            [
                'name' => 'activeConnections',
                'data' => []
            ],
            [
                'name' => 'qps',
                'data' => []
            ],
            [
                'name' => 'tps',
                'data' => []
            ],
        ];
        $model = new Server();
        $model->serverName = $serverName;
        $status = MySql::find()->where(['server'=>$serverName])->one()->status;
        return $this->render('mysql-chart', [
            'data' => $data,
            'range' =>  $range,
            'minDate' => $minDate,
            'servers' => $this->getServersForDrop(),
            'model' => $model,
            'status' => $status,
        ]);
    }
    
    public function actionNginxChart($serverName){
        $startTime = time()-24*3600;
        $endTime = time();
        $range = $this->getDateRange($startTime, $endTime);
        $minDate = Timezone::date(NginxInfo::find()->where(['server'=>$serverName])->min('recordTime'));
        $data = [
            [
                'name' => 'accept',
                'data' => []
            ],
            [
                'name' => 'handle',
                'data' => []
            ],
            [
                'name' => 'request',
                'data' => []
            ],
            [
                'name' => 'active',
                'data' => []
            ],
        ];
        $model = new Server();
        $model->serverName = $serverName;
        $status = Nginx::find()->where(['server'=>$serverName])->one()->status;
        return $this->render('nginx-chart', [
            'data' => $data,
            'range' =>  $range,
            'minDate' => $minDate,
            'servers' => $this->getServersForDrop(),
            'model' => $model,
            'status' => $status
        ]);
    }
    
    /**
     * 将最新的仪表盘数据传回
     * @param string $serverName 服务器名
     */
    public function actionUpdateGaugeInfo($serverName){
        $updatedInfo = RealTime::findOne(['server' => $serverName]);
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = [
            'cpuInfo' => $updatedInfo['cpuUtilize']+0,
            'ramInfo' => $updatedInfo['memoryUtilize']+0,
            'diskInfo' => $updatedInfo['diskUtilize']+0,
            'loadInfo' => $updatedInfo['load1']+0
        ];
    }
    
    /**
     * 将最新的服务器数据传回
     * @param string $serverName 服务器名
     */
    public function actionUpdateServerGridInfo($serverName){
        $updatedInfo = RealTime::findOne(['server' => $serverName]);
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = [
            'cpuInfo' => $updatedInfo['cpuUtilize']+0,
            'ramInfo' => $updatedInfo['memoryUtilize']+0,
            'diskInfo' => $updatedInfo['diskUtilize']+0,
            'loadInfo' => $updatedInfo['load1']+0
        ];
    }
    /**
     * 获得某个stream的实时cpu和ram数据
     * @param string $serverName
     * @param string $streamName
     */
    public function actionUpdateStreamGridInfo($serverName, $streamName){
        $info = (new Query())
        ->select(['total', 'memory'])
        ->from('stream_info')
        ->where("recordTime=(select MAX(recordTime) from stream_info where server='$serverName' and streamName='$streamName')
            and server='$serverName' and streamName='$streamName'")
        ->one();
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data =[
            'cpuInfo' => $info['total'],
            'ramInfo' => $info['memory']
        ];
    }
    /**
     * 返回相应折线图数据
     * @param string $serverName
     * @param string $type
     * @param string $startTime
     * @param string $endTime
     */
    public function actionUpdateLineInfo($serverName, $type, $startTime, $endTime){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        switch ($type){
            case 'CPU':
                $response->data = $this->getCpuData($serverName, $startTime, $endTime);
                break;
            case 'RAM':
                $response->data = $this->getRamData($serverName, $startTime, $endTime);
                break;
            case 'DISK':
                $response->data = $this->getDiskData($serverName, $startTime, $endTime);
                break;
            case 'LOAD':
                $response->data = $this->getLoadData($serverName, $startTime, $endTime);
                break;
            case 'Streams':
                $response->data = $this->getStreamsData($serverName, $startTime, $endTime);
                break;
            case 'Nginx':
                $response->data = $this->getNginxData($serverName, $startTime, $endTime);
                break;
            case 'Mysql':
                $response->data = $this->getMysqlData($serverName, $startTime, $endTime);
                break;
        }
    }
    /**
     * 更新服务器相关历史数据
     * @param string $servers
     * @param string $startTime
     * @param string $endTime
     */
    public function actionUpdateServersData($servers, $startTime, $endTime){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $this->getServersData($servers, $startTime, $endTime);
    }
    /**
     * 更新服务器上串流相关历史数据
     * @param string $servers
     * @param string $streams
     * @param string $startTime
     * @param string $endTime
     */
    public function actionUpdateStreamsData($serverName, $streams, $startTime, $endTime){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $this->getStreamsData($serverName, $streams, $startTime, $endTime);
    }
    /**
     * 更新服务器上串流相关历史数据
     * @param string $servers
     * @param string $streams
     * @param string $startTime
     * @param string $endTime
     */
    public function actionUpdateStreamData($serverName, $streamName, $startTime, $endTime){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $this->getStreamData($serverName, $streamName, $startTime, $endTime);
    }
    
    public function actionUpdateWarningLine($type, $startTime, $endTime){
        $threshold = Threshold::find()->one();
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        switch ($type){
            case 'CPU':$response->data = $this->getCpuWarningData($startTime, $endTime, $threshold->cpu);break;
            case 'RAM':$response->data = $this->getRamWarningData($startTime, $endTime, $threshold->memory);break;
            case 'DISK':$response->data = $this->getDiskWarningData($startTime, $endTime, $threshold->disk);break;
            case 'LOAD':$response->data = $this->getLoadWarningData($startTime, $endTime, $threshold->loads);break;
            case 'Stream':
                $response->data = [$this->getStreamWarningData($startTime, $endTime), $this->getStreamNames($startTime, $endTime)];
                break;
            case 'MySql':
                $response->data = [$this->getMySqlWarningData($startTime, $endTime), $this->getMySqlServers($startTime, $endTime)];
                break;
            case 'Nginx':
                $response->data = [$this->getNginxWarningData($startTime, $endTime), $this->getNginxServers($startTime, $endTime)];
                break;
        }
    }
    /**
     * 更新服务器表格内的相关数据
     */
    public function actionUpdateServerStatus(){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $this->getRealTimeStatus();
    }
    
    /**
     * 在线用户监控
     * @return \app\models\OnlineClientSearch[]|\yii\data\ActiveDataProvider[]
     */
    public function actionClientMonitor(){
        $searchModel = new OnlineClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('client-monitor', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * 获取所有server的实时状态信息
     * @return \app\models\ServerSearch[]|\yii\data\ActiveDataProvider[]
     */
    private function getRealtimeServerStatus(){
        $searchModel = new ServerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ];
    }
    
    /**
     * 获取所有stream的实时状态信息
     * @return \app\models\ServerSearch[]|\yii\data\ActiveDataProvider[]
     */
    private function getRealtimeStreamStatus(){
        $searchModel = new StreamSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ];
    }
    
    /**
     * 获取所有MySQL的实时状态信息
     * @return \app\models\ServerSearch[]|\yii\data\ActiveDataProvider[]
     */
    private function getRealtimeMysqlStatus(){
        $searchModel = new MySqlSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ];
    }
    
    
    /**
     * 获取所有Nginx的实时状态信息
     * @return \app\models\ServerSearch[]|\yii\data\ActiveDataProvider[]
     */
    private function getRealtimeNginxStatus(){
        $searchModel = new NginxSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ];
    }
    
    /**
     * 获取所有在线用户信息
     * @return \app\models\ServerSearch[]|\yii\data\ActiveDataProvider[]
     */
    private function getOnlineClients(){
        $searchModel = new OnlineClientSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ];
    }
    
    /**
     * 整理折线图横轴和纵轴的数据
     * @param array $allData 相应表中的所用数据
     * @param string $time 时间属性
     * @param string $property 与时间对应的数值属性
     */
    private function getChartDataByProperty($allData, $time, $property){
        $xCategories = ArrayHelper::getColumn($allData, $time);
        for ($i=0;$i<count($xCategories);$i++){
            $xCategories[$i] = date('Y-m-d H:i:00', $xCategories[$i]);
            $xCategories[$i] = strtotime($xCategories[$i])*1000;
        }
        $data = array();
        $column = ArrayHelper::getColumn($allData, function ($element) use($property){
                    return $element[$property] + 0;
        });
        
        for($i=0;$i<count($xCategories);$i++){
            $d = array($xCategories[$i],$column[$i]);
            array_push($data, $d);
        }
        return $data;
    }
    
    /**
     * 获取应用于CPU折线图的数据
     * @param string $serverName
     * @param string $startTime
     * @param string $endTime
     */
    private function getCpuData($serverName, $startTime, $endTime){
        $cpuData = CPU::find()
            ->where('server="'.$serverName.'" and recordTime between "'.$startTime.'" and "'.$endTime.'"')
            ->asArray()
            ->all();
        return [
            [
                'name' => 'utilize',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'utilize')
            ],
            [
                'name' => 'user',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'user')
            ],
            [
                'name' => 'system',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'system')
            ],
            [
                'name' => 'wait',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'wait')
            ],
            [
                'name' => 'hardIrq',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'hardIrq')
            ],
            [
                'name' => 'softIrq',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'softIrq')
            ],
            [
                'name' => 'nice',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'nice')
            ],
            [
                'name' => 'steal',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'steal')
            ],
            [
                'name' => 'guest',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'guest')
            ],
            [
                'name' => 'idle',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'idle')
            ]
        ];
    }
    
    /**
     * 获取应用于RAM折线图的数据
     * @param string $serverName
     * @param string $startTime
     * @param string $endTime
     */
    private function getRamData($serverName, $startTime, $endTime){
        $ramData = RAM::find()
        ->where('server="'.$serverName.'" and recordTime between "'.$startTime.'" and "'.$endTime.'"')
        ->asArray()
        ->all();
        return [
            [
                'name' => 'utilize',
                'data' => $this->getChartDataByProperty($ramData, 'recordTime', 'utilize')
            ]
        ];
    }
    /**
     * 获取应用于DISK折线图的数据
     * @param string $serverName
     * @param string $startTime
     * @param string $endTime
     */
    private function getDiskData($serverName, $startTime, $endTime){
        $diskData = Disk::find()
        ->where('server="'.$serverName.'" and recordTime between "'.$startTime.'" and "'.$endTime.'"')
        ->asArray()
        ->all();
        return [
            [
                'name' => 'used',
                'data' => $this->getChartDataByProperty($diskData, 'recordTime', 'usedPercent')
            ],
            [
                'name' => 'free',
                'data' => $this->getChartDataByProperty($diskData, 'recordTime', 'freePercent')
            ]
        ];
    }
    /**
     * 获取应用于LOAD折线图的数据
     * @param string $serverName
     * @param string $startTime
     * @param string $endTime
     */
    private function getLoadData($serverName, $startTime, $endTime){
        $loadData = Load::find()
        ->where('server="'.$serverName.'" and recordTime between "'.$startTime.'" and "'.$endTime.'"')
        ->asArray()
        ->all();
        
        return [
            [
                'name' => 'load of 1 minute',
                'data' => $this->getChartDataByProperty($loadData, 'recordTime', 'load1')
            ],
            [
                'name' => 'load of 5 minute',
                'data' => $this->getChartDataByProperty($loadData, 'recordTime', 'load5')
            ],
            [
                'name' => 'load of 15 minute',
                'data' => $this->getChartDataByProperty($loadData, 'recordTime', 'load15')
            ]
        ];
    }
    /**
     * 获取应用于Nginx折线图的数据
     * @param string $serverName
     * @param string $startTime
     * @param string $endTime
     */
    private function getNginxData($serverName, $startTime, $endTime){
        $nginxData = NginxInfo::find()
        ->where('server="'.$serverName.'" and recordTime between "'.$startTime.'" and "'.$endTime.'"')
        ->asArray()
        ->all();
        return [
            [
                'name' => 'accept',
                'data' => $this->getChartDataByProperty($nginxData, 'recordTime', 'accept')
            ],
            [
                'name' => 'handle',
                'data' => $this->getChartDataByProperty($nginxData, 'recordTime', 'handle')
            ],
            [
                'name' => 'request',
                'data' => $this->getChartDataByProperty($nginxData, 'recordTime', 'request')
            ],
            [
                'name' => 'active',
                'data' => $this->getChartDataByProperty($nginxData, 'recordTime', 'active')
            ],
        ];
    }
    /**
     * 获取应用于Mysql折线图的数据
     * @param string $serverName
     * @param string $startTime
     * @param string $endTime
     */
    private function getMysqlData($serverName, $startTime, $endTime){
        $mysqlData = MysqlInfo::find()
        ->where('server="'.$serverName.'" and recordTime between "'.$startTime.'" and "'.$endTime.'"')
        ->asArray()
        ->all();
        return [
            [
                'name' => 'totalConnections',
                'data' => $this->getChartDataByProperty($mysqlData, 'recordTime', 'totalConnections')
            ],
            [
                'name' => 'activeConnections',
                'data' => $this->getChartDataByProperty($mysqlData, 'recordTime', 'activeConnections')
            ],
            [
                'name' => 'qps',
                'data' => $this->getChartDataByProperty($mysqlData, 'recordTime', 'qps')
            ],
            [
                'name' => 'tps',
                'data' => $this->getChartDataByProperty($mysqlData, 'recordTime', 'tps')
            ],
        ];
    }
    
    private function getStreamData($serverName, $streamName, $startTime, $endTime){
        $cpuData = CPU::find()
        ->where('server="'.$serverName.'" and recordTime between "'.$startTime.'" and "'.$endTime.'"')
        ->asArray()
        ->all();
        $ramData = RAM::find()
        ->where('server="'.$serverName.'" and recordTime between "'.$startTime.'" and "'.$endTime.'"')
        ->asArray()
        ->all();
        $streamData = StreamInfo::find()
        ->where('server="'.$serverName.'" and streamName="'.$streamName.'" and recordTime between "'.$startTime.'" and "'.$endTime.'"')
        ->asArray()
        ->all();
        $cpu = [
            [
                'name' => 'Server CPU',
                'data' => $this->getChartDataByProperty($cpuData, 'recordTime', 'utilize')
            ],
            [
                'name' => 'Stream CPU',
                'data' => $this->getChartDataByProperty($streamData, 'recordTime', 'total')
            ],
        ];
        $ram = [
            [
                'name' => 'Server RAM',
                'data' => $this->getChartDataByProperty($ramData, 'recordTime', 'utilize')
            ],
            [
                'name' => 'Stream RAM',
                'data' => $this->getChartDataByProperty($streamData, 'recordTime', 'memory')
            ],
        ];
        return [$cpu, $ram];
    }
    
    /**
     * 获取应用于Server Monitor折线图的数据
     * @param string $startTime
     * @param string $endTime
     */
    private function getServersData($servers, $startTime, $endTime){
        $serverArr = explode(',', $servers);
        $where = implode('","', $serverArr);
        $servers = Server::find()->where('serverName in ("'.$where.'")')->all();
        $cpuData = array();
        $ramData = array();
        $diskData = array();
        $loadData = array();
        for($i=0;$i<count($servers);$i++){
            $cpuInfo = $servers[$i]->getCpuInfo($startTime, $endTime)->asArray()->all();
            $cpu = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($cpuInfo, 'recordTime', 'utilize')
            ];
            array_push($cpuData, $cpu);
            $ramInfo = $servers[$i]->getRamInfo($startTime, $endTime)->asArray()->all();
            $ram = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($ramInfo, 'recordTime', 'utilize')
            ];
            array_push($ramData, $ram);
            $diskInfo = $servers[$i]->getDiskInfo($startTime, $endTime)->asArray()->all();
            $disk = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($diskInfo, 'recordTime', 'freePercent')
            ];
            array_push($diskData, $disk);
            $loadInfo = $servers[$i]->getLoadInfo($startTime, $endTime)->asArray()->all();
            $load = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($loadInfo, 'recordTime', 'load1')
            ];
            array_push($loadData, $load);
        }
        return array($cpuData, $ramData, $diskData, $loadData);
    }
    /**
     * 获取应用于Streams Monitor折线图的数据
     * @param string $serverName
     * @param string $startTime
     * @param string $endTime
     */
    private function getStreamsData($serverName, $streams, $startTime, $endTime){
        $streamArr = explode(',', $streams);
        $where = implode('","', $streamArr);
        $where = 'streamName in ("'.$where.'") and server="'.$serverName.'"';
        $streamName = Stream::find()
            ->where($where)
            ->all();
        $totalData = array();
        $memoryData = array();
        for($i=0;$i<count($streamName);$i++){
            $stream = $streamName[$i]
            ->getStreams($startTime, $endTime)
            ->asArray()
            ->all();
            $streamTotal = [
                'name' => $streamName[$i]['streamName'],
                'data' => $this -> getChartDataByProperty($stream, 'recordTime', 'total')
            ];
            array_push($totalData, $streamTotal);
            $processMemory = [
                'name' => $streamName[$i]['streamName'],
                'data' => $this -> getChartDataByProperty($stream, 'recordTime', 'memory')
            ];
            array_push($memoryData, $processMemory);
        }
        return array($totalData, $memoryData);
    }
    /**
     * 得到阈值以上的cpu值
     * @param string $startTime
     * @param string $endTime
     * @param string $threshold
     */
    private function getCpuWarningData($startTime, $endTime, $threshold){
        $servers = Server::find()->all();
        $cpuData = [];
        for($i=0;$i<count($servers);$i++){
            $cpuInfo = $servers[$i]->getCpuInfo($startTime, $endTime)
            ->andWhere("utilize>=$threshold")
            ->asArray()
            ->all();
            $cpu = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($cpuInfo, 'recordTime', 'utilize')
            ];
            array_push($cpuData, $cpu);
        }
        return $cpuData;
    }
    /**
     * 得到阈值以上的ram值
     * @param string $startTime
     * @param string $endTime
     * @param string $threshold
     */
    private function getRamWarningData($startTime, $endTime, $threshold){
        $servers = Server::find()->all();
        $ramData = [];
        for($i=0;$i<count($servers);$i++){
            $ramInfo = $servers[$i]->getRamInfo($startTime, $endTime)
            ->andWhere("utilize>=$threshold")
            ->asArray()
            ->all();
            $ram = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($ramInfo, 'recordTime', 'utilize')
            ];
            array_push($ramData, $ram);
        }
        return $ramData;
    }
    /**
     * 得到阈值以下的disk值
     * @param string $startTime
     * @param string $endTime
     * @param string $threshold
     */
    private function getDiskWarningData($startTime, $endTime, $threshold){
        $servers = Server::find()->all();
        $diskData = [];
        for($i=0;$i<count($servers);$i++){
            $diskInfo = $servers[$i]->getDiskInfo($startTime, $endTime)
            ->andWhere("usedPercent>=$threshold")
            ->asArray()
            ->all();
            $disk = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($diskInfo, 'recordTime', 'usedPercent')
            ];
            array_push($diskData, $disk);
        }
        return $diskData;
    }
    /**
     * 得到阈值以上的load值
     * @param string $startTime
     * @param string $endTime
     * @param string $threshold
     */
    private function getLoadWarningData($startTime, $endTime, $threshold){
        $servers = Server::find()->all();
        $loadData = [];
        for($i=0;$i<count($servers);$i++){
            $loadInfo = $servers[$i]->getLoadInfo($startTime, $endTime)
            ->andWhere("load1>=$threshold")
            ->orderBy('recordTime')
            ->asArray()
            ->all();
            $load = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($loadInfo, 'recordTime', 'load1')
            ];
            array_push($loadData, $load);
        }
        return $loadData;
    }
    /**
     * 各个时间点串流断的个数
     * @param string $startTime
     * @param string $endTime
     */
    private function getStreamWarningData($startTime, $endTime){
        $servers = Server::find()->asArray()->all();
        $streamData=[];
        for($i=0;$i<count($servers);$i++){
            $rows = (new Query())
            ->select(['UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(recordTime),"%Y-%m-%d %H:%i")) as time', 'count(if(si.status=0,true,null )) as count'])
            ->from('stream as s, stream_info as si')
            ->where('s.server=si.server and s.streamName=si.streamName and s.server="'.$servers[$i]['serverName'].'" and recordTime between "'.$startTime.'" and "'.$endTime.'"')
            ->groupBy('time,s.server')
            ->all();
            $data = [];
            for($j=0;$j<count($rows);$j++){
                $time = $rows[$j]['time']*1000;
                array_push($data, [$time, $rows[$j]['count']+0]);
            }
            $streams = [
                'name' => $servers[$i]['serverName'],
                'data' => $data
            ];
            array_push($streamData, $streams);
        }
        return $streamData;
    }
    /**
     * 某一时间点断开的串流
     * @param string $startTime
     * @param string $endTime
     */
    private function getStreamNames($startTime, $endTime){
        $deadStreams = (new Query())
        ->select(['UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(recordTime),"%Y-%m-%d %H:%i")) as time', 's.server','s.streamName as sName'])
        ->from('stream as s, stream_info as si')
        ->where("si.status=0 and s.server=si.server and s.streamName=si.streamName and recordTime between '$startTime' and '$endTime'")
        ->orderBy(['recordTime' => SORT_ASC, 'server' => SORT_ASC, 'sName'=> SORT_ASC])
        ->all();
        $streams = [];
        $time = null;
        $server = null;
        for($i=0;$i<count($deadStreams);$i++){
            $recordTime = ''.$deadStreams[$i]['time']*1000;
            $newServer = $deadStreams[$i]['server'];
            $streamName = $deadStreams[$i]['sName'];
            if($newServer==$server && $recordTime==$time){
                array_push($streams[$newServer][$recordTime], $streamName);
            }else{
                $streams[$newServer][$recordTime] = [$streamName];
                $time = $recordTime;
                $server = $newServer;
            }
        }
        return $streams;
    }
    /**
     * 某一时间点断开的mysql数
     * @param string $startTime
     * @param string $endTime
     */
    private function getMySqlWarningData($startTime, $endTime){
        $rows = (new Query())
        ->select(['UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(recordTime),"%Y-%m-%d %H:%i")) as time', 'count(if(status=0,true,null )) as count'])
        ->from(['mysql_info'])
        ->where('recordTime BETWEEN "'.$startTime.'" and "'.$endTime.'"')
        ->groupBy('time')
        ->all();
        $data = [];
        for($j=0;$j<count($rows);$j++){
            $time = $rows[$j]['time']*1000;
            array_push($data, [$time, $rows[$j]['count']+0]);
        }
        $mysql = [[
            'name' => 'count',
            'data' => $data
        ]];
        return $mysql;
    }
    /**
     * 某一时间点断开的mysql所属的服务器
     * @param string $startTime
     * @param string $endTime
     */
    private function getMySqlServers($startTime, $endTime){
        $rows = (new Query())
        ->select(['UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(recordTime),"%Y-%m-%d %H:%i")) as time', 'server'])
        ->from(['mysql_info'])
        ->where('status=0 and recordTime BETWEEN "'.$startTime.'" and "'.$endTime.'"')
        ->orderBy('time')
        ->all();
        $servers=[];
        $time=null;
        for($i=0;$i<count($rows);$i++){
            $recordTime = ''.$rows[$i]['time']*1000;
            $serverName = $rows[$i]['server'];
            if($recordTime==$time){
                array_push($servers[$recordTime], $serverName);
            }else{
                $servers[$recordTime]=[$serverName];
                $time=$recordTime;
            }
        }
        return $servers;
    }
    
    /**
     * 某一时间点断开的nginx数
     * @param string $startTime
     * @param string $endTime
     */
    private function getNginxWarningData($startTime, $endTime){
        $rows = (new Query())
        ->select(['UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(recordTime),"%Y-%m-%d %H:%i")) as time', 'count(if(status=0,true,null )) as count'])
        ->from(['nginx_info'])
        ->where('recordTime BETWEEN "'.$startTime.'" and "'.$endTime.'"')
        ->groupBy('time')
        ->all();
        $data = [];
        for($j=0;$j<count($rows);$j++){
            $time = $rows[$j]['time']*1000;
            array_push($data, [$time, $rows[$j]['count']+0]);
        }
        $nginx = [[
            'name' => 'count',
            'data' => $data
        ]];
        return $nginx;
    }
    /**
     * 某一时间点断开的nginx所属的服务器
     * @param string $startTime
     * @param string $endTime
     */
    private function getNginxServers($startTime, $endTime){
        $rows = (new Query())
        ->select(['UNIX_TIMESTAMP(DATE_FORMAT(FROM_UNIXTIME(recordTime),"%Y-%m-%d %H:%i")) as time', 'server'])
        ->from(['nginx_info'])
        ->where('status=0 and recordTime BETWEEN "'.$startTime.'" and "'.$endTime.'"')
        ->orderBy('time')
        ->all();
        $servers=[];
        $time=null;
        for($i=0;$i<count($rows);$i++){
            $recordTime = ''.$rows[$i]['time']*1000;
            $serverName = $rows[$i]['server'];
            if($recordTime==$time){
                array_push($servers[$recordTime], $serverName);
            }else{
                $servers[$recordTime]=[$serverName];
                $time=$recordTime;
            }
        }
        return $servers;
    }
    
    /**
     * 获取应用于下拉框的server数据
     */
    private function getServersForDrop(){
        $allServers = Server::find()->asArray()->all();
        return ArrayHelper::map($allServers, 'serverName', 'serverName');
    }
    
    private function getDateRange($startTime, $endTime){
        $startTime = Timezone::date($startTime);
        $endTime = Timezone::date($endTime);
        return $startTime.' - '.$endTime;
    }
}