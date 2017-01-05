<?php
namespace app\controllers;

use yii\web\Controller;
use app\models\ProcessSearch;
use app\models\Server;
use yii\helpers\ArrayHelper;
use Yii;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use app\models\Process;
use yii\web\UploadedFile;

class ProcessController extends Controller{
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
     * 显示所有process信息
     */
    public function actionIndex(){
        $filterModel = new ProcessSearch();
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
     * 查看process详情
     * @param string $processName
     * @param string $server
     */
    public function actionView($processName, $server){
        $model = Process::findProcessByKey($processName, $server);
        return $this->render('view',[
            'model' => $model
        ]);
    }
    /**
     * 创建新的process
     */
    public function actionCreate(){
        $model = new Process();
        $model->scenario = Process::SCENARIO_SAVE;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::info("create a stream named $model->processName on the server $model->server", 'stream');
            $this->redirect(['view', 'processName' => $model->processName, 'server' => $model->server]);
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
     * @param string $processName
     * @param string $server
     * @return \yii\web\Response
     */
    public function actionDelete($processName, $server){
        $model = Process::findProcessByKey($processName, $server);
        $model->delete();
        Yii::info("delete $model->processName on $model->server", 'Process');
        return $this->redirect(['index']);
    }
    /**
     * 批量删除process
     * @param string $keys
     */
    public function actionDeleteAll($keys){
        $process = json_decode($keys);
        $processes = [];
        for($i=0;$i<count($process);$i++){
            $p = '("' . $process[$i]->processName . '","' . $process[$i]->server . '")';
            array_push($processes, $p);
        }
        $pr = implode(',', $processes);
        $model = new Process();
        $model->deleteAll("(processName,server) in ($pr)");
        Yii::info("delete selected " . count($process) . " streams", 'Process');
        return $this->redirect(['index']);
    }
    /**
     * 批量导入stream
     * @return string
     */
    public function actionImport(){
        $model = new Process();
        $model->scenario = Process::SCENARIO_IMPORT;
        $state = [
            'message' => 'Info:please import a xml file. Format as below:</br>'
                        . '&lt;?xml version="1.0" encoding="UTF-8"?&gt;</br>'
                        . '&lt;message&gt;</br>'
                        . '&nbsp;&nbsp;&lt;Process&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;processName&gt;nirvana6&lt;/processName&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;server&gt;server1&lt;/server&gt;</br>'
                        . '&nbsp;&nbsp;&lt;/Process&gt;</br>'
                        . '&nbsp;&nbsp;&lt;Process&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;······</br>'
                        . '&nbsp;&nbsp;&lt;/Process&gt;</br>'
                        . '&lt;/message&gt;</br>',
            'class' => 'alert-info',
            'percent' => 0,
            'label' => '0%',
        ];
        if($model->load(Yii::$app->request->post())){
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            try {
                $xmlArray = simplexml_load_file($model->importFile->tempName);
                $processes = json_decode(json_encode($xmlArray), true);
                $columns = ['processName', 'server'];
                $allStreams = null;
                if(ArrayHelper::isIndexed($processes['Process'])){
                    $allStreams = $processes['Process'];
                }else{
                    $allStreams = [$processes['Process']];
                }
                $rows = ArrayHelper::getColumn($allStreams, function($element){
                    return [$element['processName'], $element['server']];
                });
                    $db = Yii::$app->db;
                    $db->createCommand()->batchInsert('process', $columns, $rows)->execute();
                    $streamStr = implode(',', ArrayHelper::getColumn($allStreams, 'processName'));
                    $serverStr = implode(',', ArrayHelper::getColumn($allStreams, 'server'));
                    Yii::info("import " . count($rows) . " streams, they are $streamStr on $serverStr respectively", 'Process');
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
    public function actionUpdate($processName, $server){
        $model = Process::findProcessByKey($processName, $server);
        $model->scenario = Process::SCENARIO_SAVE;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::info("Update $model->processName on $model->server", 'Process');
            return $this->redirect(['view', 'processName' => $model->processName, 'server' => $model->server]);
        }
        $allServers = Server::find()->asArray()->all();
        $servers = ArrayHelper::map($allServers, 'serverName', 'serverName');
        return $this->render('update',[
            'model' => $model,
            'servers' => $servers
        ]);
    }
    
}