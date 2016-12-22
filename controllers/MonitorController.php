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
    public function actionIndex($serverName='localcent')
    {
        $server = new Server();
        $data = Server::find()->all();
        return $this->render('index', [
            'serverName' => $serverName,
            'server' => $server,
            'data' => $data
        ]);
    }

    /**
     * 绘制cpu折线图
     * @param string $serverName
     */
    public function actionCpuChart($serverName)
    {
        $cpuData = CPU::find()->where([
            'server' => $serverName
        ])
            ->asArray()
            ->all();
        $xCatagories = ArrayHelper::getColumn($cpuData, 'recordTime');
        for ($i=0;$i<count($xCatagories);$i++){
            $xCatagories[$i] = strtotime($xCatagories[$i])*1000+8*3600*1000;
        }
        $data = [
            [
                'name' => 'utilize',
                'data' => $this->getChartDataByProperty($cpuData, $xCatagories, 'utilize')
            ],
            [
                'name' => 'user',
                'data' => $this->getChartDataByProperty($cpuData, $xCatagories, 'user')
            ],
            [
                'name' => 'system',
                'data' => $this->getChartDataByProperty($cpuData, $xCatagories, 'system')
            ],
            [
                'name' => 'wait',
                'data' => $this->getChartDataByProperty($cpuData, $xCatagories, 'wait')
            ],
            [
                'name' => 'hardIrq',
                'data' => $this->getChartDataByProperty($cpuData, $xCatagories, 'hardIrq')
            ],
            [
                'name' => 'softIrq',
                'data' => $this->getChartDataByProperty($cpuData, $xCatagories, 'softIrq')
            ],
            [
                'name' => 'nice',
                'data' => $this->getChartDataByProperty($cpuData, $xCatagories, 'nice')
            ],
            [
                'name' => 'steal',
                'data' => $this->getChartDataByProperty($cpuData, $xCatagories, 'steal')
            ],
            [
                'name' => 'guest',
                'data' => $this->getChartDataByProperty($cpuData, $xCatagories, 'guest')
            ],
            [
                'name' => 'idle',
                'data' => $this->getChartDataByProperty($cpuData, $xCatagories, 'idle')
            ]
        ];
        return $this->render('cpu-chart', [
            'data' => $data
        ]);
    }
    
    public function actionNewInfo($serverName){
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
     * 显示CPU表格
     */
    public function actionCpuGrid($serverName)
    {
        $searchModel = new CPUSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $serverName);
        return $this->render('cpu-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 显示RAM折线图
     */
    public function actionRamChart()
    {
        return $this->render('ram-chart', []);
    }
    
    /**
     * 整理折线图横轴和纵轴的数据
     * @param array $allData 相应表中的所用数据
     * @param array $xCatagories 一维索引数组，表示横轴时间数据
     * @param string $property 属性
     */
    private function getChartDataByProperty($allData, $xCatagories, $property){
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