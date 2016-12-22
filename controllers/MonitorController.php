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
                    'index' => [
                        'get'
                    ]
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
    public function actionIndex($serverName=null)
    {
        if($serverName==null){
            $firstServer = Server::find()->one();
            $serverName = $firstServer->serverName;
        }
        $server = new Server();
        $data = Server::find()->all();
        return $this->render('index', [
            'serverName' => $serverName,
            'server' => $server,
            'data' => $data
        ]);
    }
    
    public function actionIndexChart($serverName)
    {
        $cpu = CPU::find()->asArray()->all();
        $ram = RAM::find()->asArray()->all();
        $disk = Disk::find()->asArray()->all();
        $load = Load::find()->asArray()->all();
        $data = [
            [
                'name' => 'CPU',
                'data' => $this->getChartDataByProperty($cpu, 'recordTime', 'utilize')
            ],
            [
                'name' => 'RAM',
                'data' => $this->getChartDataByProperty($ram, 'recordTime', 'utilize')
            ],
            [
                'name' => 'Disk(free)',
                'data' => $this->getChartDataByProperty($disk, 'recordTime', 'freePercent')
            ],
            [
                'name' => 'Load',
                'data' => $this->getChartDataByProperty($load, 'recordTime', 'load1')
            ],
        ];
        return $this->render('index-chart', [
            'data' => $data
        ]);
    }

    /**
     * 传回cpu折线图相关数据
     * @param string $serverName
     */
    public function actionCpuChart($serverName)
    {
        $cpuData = CPU::find()->where([
            'server' => $serverName
        ])
            ->asArray()
            ->all();
        $data = [
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
        return $this->render('cpu-chart', [
            'data' => $data
        ]);
    }
    /**
     * 将最新的数据传回
     * @param string $serverName 服务器名
     */
    public function actionUpdateInfo($serverName){
        $cpuInfo = CPU::find()
        ->where(['server' => $serverName])
        ->orderBy(['recordTime' => SORT_DESC])
        ->one();
        $ramInfo = RAM::find()
        ->where(['server' => $serverName])
        ->orderBy(['recordTime' => SORT_DESC])
        ->one();
        $diskInfo = Disk::find()
        ->where(['server' => $serverName])
        ->orderBy(['recordTime' => SORT_DESC])
        ->one();
        $loadInfo = Load::find()
        ->where(['server' => $serverName])
        ->orderBy(['recordTime' => SORT_DESC])
        ->one();
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = [
            'cpuInfo' => $cpuInfo['utilize']+0,
            'ramInfo' => $ramInfo['utilize']+0,
            'diskInfo' => 100 - $diskInfo['freePercent'],
            'loadInfo' => $loadInfo['load1']+0
        ];
    }

    /**
     * 传回CPU表格数据
     */
    public function actionCpuGrid($serverName=null)
    {
        if($serverName==null){
            $firstServer = Server::find()->one();
            $serverName = $firstServer->serverName;
        }
        $searchModel = new CPUSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $serverName);
        return $this->render('cpu-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 传回RAM折线图数据
     */
    public function actionRamChart()
    {
        return $this->render('ram-chart', []);
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
        $allServer = Server::find()->all();
        $processName = Process::find()
            ->where(['server'=>$serverName])
            ->all();
        $totalData = array();
        $memoryData = array();
        for($i=0;$i<count($processName);$i++){
            $process = $processName[$i]
                ->getProcesses()
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
        return $this->render('streams', [
            'server' => $server,
            'allServer' => $allServer,
            'serverName' => $serverName,
            'totalData' => $totalData,
            'memoryData' => $memoryData
        ]);
    }
    
    public function actionStreamsGrid(){
        $searchModel = new ProcessInfoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('streams-grid',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
    
    
    /**
     * 整理折线图横轴和纵轴的数据
     * @param array $allData 相应表中的所用数据
     * @param string $time 时间属性
     * @param string $property 与时间对应的数值属性
     */
    private function getChartDataByProperty($allData, $time, $property){
        $xCatagories = ArrayHelper::getColumn($allData, $time);
        for ($i=0;$i<count($xCatagories);$i++){
            $xCatagories[$i] = strtotime($xCatagories[$i])*1000+8*3600*1000;
        }
        $data = array();
        $column = ArrayHelper::getColumn($allData, function ($element) use($property){
                    return $element[$property] + 0;
        });
        for($i=0;$i<count($xCatagories);$i++){
            $d = array($xCatagories[$i],$column[$i]);
            array_push($data, $d);
        }
        return $data;
    }
}