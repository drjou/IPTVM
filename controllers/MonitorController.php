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
use app\models\Process;
use app\models\ProcessInfoSearch;
use app\models\RAMSearch;
use app\models\DiskSearch;
use app\models\LoadSearch;
use app\models\RealTime;
use app\models\ProcessInfo;
use app\models\Threshold;
use yii\db\Query;
use app\models\MySqlSearch;
use app\models\NginxSearch;

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
                    'detail' => ['get'],
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
        $startTime = date('Y-m-d H:i:s',time()-24*3600);
        $endTime = date('Y-m-d H:i:s',time());
        $range = $startTime.' - '.$endTime;
        $minDate = CPU::find()->min('recordTime');
        $threshold = Threshold::find()->one();
        $cpuData = $this->getCpuWarningData($startTime, $endTime, $threshold->cpu);
        $ramData = $this->getRamWarningData($startTime, $endTime, $threshold->memory);
        $diskData = $this->getDiskWarningData($startTime, $endTime, $threshold->disk);
        $loadData = $this->getLoadWarningData($startTime, $endTime, $threshold->loads);
        $processData = $this->getProcessWarningData($startTime, $endTime);
        $processData2 = $this->getProcessNames($startTime, $endTime);
        $mySqlData = $this->getMySqlWarningData($startTime, $endTime);
        $mySqlData2 = $this->getMySqlServers($startTime, $endTime);
        $nginxData = $this->getNginxWarningData($startTime, $endTime);
        $nginxData2 = $this->getNginxServers($startTime, $endTime);
        return $this->render('index', [
            'cpuData' => $cpuData,
            'ramData' => $ramData,
            'diskData' => $diskData,
            'loadData' => $loadData,
            'processData' => $processData,
            'processData2' => json_encode($processData2),
            'mySqlData' => $mySqlData,
            'mySqlData2' => json_encode($mySqlData2),
            'nginxData' => $nginxData,
            'nginxData2' => json_encode($nginxData2),
            'range' =>  $range,
            'minDate' => $minDate,
            'cpuThreshold' => $threshold->cpu+0,
            'memoryThreshold' => $threshold->memory+0,
            'diskThreshold' => $threshold->disk+0,
            'loadsThreshold' => $threshold->loads+0
        ]);
    }

    /**
     * 传回cpu折线图相关数据
     * @param string $serverName
     */
    public function actionCpuChart($serverName)
    {
        $startTime = date('Y-m-d H:i:s',time()-24*3600);
        $endTime = date('Y-m-d H:i:s',time());
        $data = $this->getCpuData($serverName, $startTime, $endTime);
        $range = $startTime.' - '.$endTime;
        $minDate = CPU::find()->where(['server'=>$serverName])->min('recordTime');
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
        $startTime = date('Y-m-d H:i:s',time()-24*3600);
        $endTime = date('Y-m-d H:i:s',time());
        $data = $this->getRamData($serverName, $startTime, $endTime);
        $range = $startTime.' - '.$endTime;
        $minDate = RAM::find()->where(['server'=>$serverName])->min('recordTime');
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
        $startTime = date('Y-m-d H:i:s',time()-24*3600);
        $endTime = date('Y-m-d H:i:s',time());
        $data = $this->getDiskData($serverName, $startTime, $endTime);
        $range = $startTime.' - '.$endTime;
        $minDate = Disk::find()->where(['server'=>$serverName])->min('recordTime');
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
        $startTime = date('Y-m-d H:i:s',time()-24*3600);
        $endTime = date('Y-m-d H:i:s',time());
        $data = $this->getLoadData($serverName, $startTime, $endTime);
        $range = $startTime.' - '.$endTime;
        $minDate = Load::find()->where(['server'=>$serverName])->min('recordTime');
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
     * 将进程的总利用率和内存利用率数据传回
     * @param string $serverName 服务器名
     */
    public function actionStreams($serverName=null)
    {
        if($serverName==null){
            $firstServer = Server::find()->one();
            $serverName = $firstServer->serverName;
        }
        $server = new Server();
        $startTime = date('Y-m-d H:i:s',time()-3600);
        $endTime = date('Y-m-d H:i:s',time());
        $data = $this->getStreamsData($serverName, $startTime, $endTime);
        $range = $startTime.' - '.$endTime;
        $minDate = ProcessInfo::find()->where(['server'=>$serverName])->min('recordTime');
        return $this->render('streams', [
            'server' => $server,
            'servers' => $this->getServersForDrop(),
            'serverName' => $serverName,
            'totalData' => $data[0],
            'memoryData' => $data[1],
            'range' => $range,
            'minDate' => $minDate
        ]);
    }
    
    /**
     * 传回表格中的数据
     */
    public function actionStreamsGrid($type){
        $searchModel = new ProcessInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('streams-grid',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'servers' => $this->getServersForDrop()
        ]);
    }
    
    /**
     * 传回不同服务器的相关数据
     */
    public function actionServers(){
        $startTime = date('Y-m-d H:i:s',time()-24*3600);
        $endTime = date('Y-m-d H:i:s',time());
        $data = $this->getServersData($startTime, $endTime);
        $range = $startTime.' - '.$endTime;
        $minDate = CPU::find()->min('recordTime');
        $heatData = $this->getHeatMapData();
        $realTimes = RealTime::find()->asArray()->all();
        $xCategories = ArrayHelper::getColumn($realTimes, 'server');
        return $this->render('servers', [
            'cpuData' => $data[0],
            'ramData' => $data[1],
            'diskData' => $data[2],
            'loadData' => $data[3],
            'range' => $range,
            'minDate' => $minDate,
            'xCategories' => $xCategories,
            'heatData' => $heatData
        ]);
    }
    /**
     * 获取服务器详细信息
     * @param string $serverName
     */
    public function actionDetail($serverName){
        return $this->render('detail',[]);
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
     * 返回相应折线图数据
     * @param string $serverName
     * @param string $type
     * @param string $startTime
     * @param string $endTime
     */
    public function actionUpdateLineInfo($serverName, $type, $startTime, $endTime){
        $startTime = date('Y-m-d H:i:s',$startTime/1000);
        $endTime = date('Y-m-d H:i:s',$endTime/1000);
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
            case 'Servers':
                $response->data = $this->getServersData($startTime, $endTime);
                break;
            case 'Streams':
                $response->data = $this->getStreamsData($serverName, $startTime, $endTime);
                break;
        }
    }
    
    public function actionUpdateWarningLine($type, $startTime, $endTime){
        $startTime = date('Y-m-d H:i:s',$startTime/1000);
        $endTime = date('Y-m-d H:i:s',$endTime/1000);
        $threshold = Threshold::find()->one();
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        switch ($type){
            case 'CPU':$response->data = $this->getCpuWarningData($startTime, $endTime, $threshold->cpu);break;
            case 'RAM':$response->data = $this->getRamWarningData($startTime, $endTime, $threshold->memory);break;
            case 'DISK':$response->data = $this->getDiskWarningData($startTime, $endTime, $threshold->disk);break;
            case 'LOAD':$response->data = $this->getLoadWarningData($startTime, $endTime, $threshold->loads);break;
            case 'Process':
                $response->data = [$this->getProcessWarningData($startTime, $endTime), $this->getProcessNames($startTime, $endTime)];
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
     * 传回最新的热力图数据
     */
    public function actionUpdateHeatMap(){
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $this->getHeatMapData();
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
            $xCategories[$i] = strtotime($xCategories[$i]);
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
     * 获取应用于Server Monitor折线图的数据
     * @param string $startTime
     * @param string $endTime
     */
    private function getServersData($startTime, $endTime){
        $servers = Server::find()->all();
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
    private function getStreamsData($serverName, $startTime, $endTime){
        $processName = Process::find()
            ->where(['server'=>$serverName])
            ->all();
        $totalData = array();
        $memoryData = array();
        for($i=0;$i<count($processName);$i++){
            $process = $processName[$i]
            ->getProcesses($startTime, $endTime)
            ->asArray()
            ->all();
            $processTotal = [
                'name' => $processName[$i]['processName'],
                'data' => $this -> getChartDataByProperty($process, 'recordTime', 'total')
            ];
            array_push($totalData, $processTotal);
            $processMemory = [
                'name' => $processName[$i]['processName'],
                'data' => $this -> getChartDataByProperty($process, 'recordTime', 'memory')
            ];
            array_push($memoryData, $processMemory);
        }
        return array($totalData, $memoryData);
    }
    
    /**
     * 获取热力图数据
     */
    private function getHeatMapData(){
         $data = RealTime::find()->asArray()->all();
         $heatData = array();
         for($i=0;$i<count($data);$i++){
             $cpuData = [$i,0,$data[$i]['cpuUtilize']];
             $ramData = [$i,1,$data[$i]['memoryUtilize']];
             $diskData = [$i,2,$data[$i]['diskUtilize']];
             $loadData = [$i,3,$data[$i]['load1']];
             array_push($heatData, $cpuData, $ramData, $diskData, $loadData);
         }
         return $heatData;
    }
    /**
     * 获取应用于下拉框的server数据
     */
    private function getServersForDrop(){
        $allServers = Server::find()->asArray()->all();
        return ArrayHelper::map($allServers, 'serverName', 'serverName');
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
            ->andWhere("freePercent<=$threshold")
            ->asArray()
            ->all();
            $disk = [
                'name' => $servers[$i]['serverName'],
                'data' => $this->getChartDataByProperty($diskInfo, 'recordTime', 'freePercent')
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
     * 各个时间点进程断的个数
     * @param string $startTime
     * @param string $endTime
     */
    private function getProcessWarningData($startTime, $endTime){
        $servers = Server::find()->asArray()->all();
        $processData=[];
        for($i=0;$i<count($servers);$i++){
            $rows = (new Query())
            ->select(['DATE_FORMAT(DATE_FORMAT(recordTime,"%Y-%m-%d %H:%i"),"%Y-%m-%d %H:%i:%s") as time', 'count(if(status=0,true,null )) as count'])
            ->from('process as p, process_info as pi')
            ->where('p.server=pi.server and p.processName=pi.processName and p.server="'.$servers[$i]['serverName'].'" and recordTime between "'.$startTime.'" and "'.$endTime.'"')
            ->groupBy('time,p.server')
            ->all();
            $data = [];
            for($j=0;$j<count($rows);$j++){
                $time = strtotime($rows[$j]['time'])*1000;
                array_push($data, [$time, $rows[$j]['count']+0]);
            }
            $processes = [
                'name' => $servers[$i]['serverName'],
                'data' => $data
            ];
            array_push($processData, $processes);
        }
        return $processData;
    }
    /**
     * 某一时间点断开的进程
     * @param string $startTime
     * @param string $endTime
     */
    private function getProcessNames($startTime, $endTime){
        $deadProcess = (new Query())
        ->select(['DATE_FORMAT(DATE_FORMAT(recordTime,"%Y-%m-%d %H:%i"),"%Y-%m-%d %H:%i:%s") as time', 'p.server','p.processName as pName'])
        ->from('process as p, process_info as pi')
        ->where("status=0 and p.server=pi.server and p.processName=pi.processName and recordTime between '$startTime' and '$endTime'")
        ->orderBy(['recordTime' => SORT_ASC, 'server' => SORT_ASC, 'pName'=> SORT_ASC])
        ->all();
        $processes = [];
        $time = null;
        $server = null;
        for($i=0;$i<count($deadProcess);$i++){
            $recordTime = ''.strtotime($deadProcess[$i]['time'])*1000;
            $newServer = $deadProcess[$i]['server'];
            $processName = $deadProcess[$i]['pName'];
            if($newServer==$server && $recordTime==$time){
                array_push($processes[$newServer][$recordTime], $processName);
            }else{
                $processes[$newServer][$recordTime] = [$processName];
                $time = $recordTime;
                $server = $newServer;
            }
        }
        return $processes;
    }
    /**
     * 某一时间点断开的mysql数
     * @param string $startTime
     * @param string $endTime
     */
    private function getMySqlWarningData($startTime, $endTime){
        $rows = (new Query())
        ->select(['DATE_FORMAT(DATE_FORMAT(recordTime,"%Y-%m-%d %H:%i"),"%Y-%m-%d %H:%i:%s") as time', 'count(if(status=0,true,null )) as count'])
        ->from(['mysql_info'])
        ->where('recordTime BETWEEN "'.$startTime.'" and "'.$endTime.'"')
        ->groupBy('time')
        ->all();
        $data = [];
        for($j=0;$j<count($rows);$j++){
            $time = strtotime($rows[$j]['time'])*1000;
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
        ->select(['DATE_FORMAT(DATE_FORMAT(recordTime,"%Y-%m-%d %H:%i"),"%Y-%m-%d %H:%i:%s") as time', 'server'])
        ->from(['mysql_info'])
        ->where('status=0 and recordTime BETWEEN "'.$startTime.'" and "'.$endTime.'"')
        ->orderBy('time')
        ->all();
        $servers=[];
        $time=null;
        for($i=0;$i<count($rows);$i++){
            $recordTime = ''.strtotime($rows[$i]['time'])*1000;
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
        ->select(['DATE_FORMAT(DATE_FORMAT(recordTime,"%Y-%m-%d %H:%i"),"%Y-%m-%d %H:%i:%s") as time', 'count(if(status=0,true,null )) as count'])
        ->from(['nginx'])
        ->where('recordTime BETWEEN "'.$startTime.'" and "'.$endTime.'"')
        ->groupBy('time')
        ->all();
        $data = [];
        for($j=0;$j<count($rows);$j++){
            $time = strtotime($rows[$j]['time'])*1000;
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
        ->select(['DATE_FORMAT(DATE_FORMAT(recordTime,"%Y-%m-%d %H:%i"),"%Y-%m-%d %H:%i:%s") as time', 'server'])
        ->from(['nginx'])
        ->where('status=0 and recordTime BETWEEN "'.$startTime.'" and "'.$endTime.'"')
        ->orderBy('time')
        ->all();
        $servers=[];
        $time=null;
        for($i=0;$i<count($rows);$i++){
            $recordTime = ''.strtotime($rows[$i]['time'])*1000;
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
}