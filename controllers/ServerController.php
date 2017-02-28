<?php
namespace app\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\ServerSearch;
use app\models\Server;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

class ServerController extends Controller{
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
                    'enable' => ['get'],
                    'disable' => ['get'],
                    'update' => ['get', 'post'],
                    'view' => ['get']
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
     * Index Action 显示所有的server信息
     * @return string
     */
    public function actionIndex(){
        $searchModel = new ServerSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider
        ]);
    }
    /**
     * 多选删除操作
     * @param string $keys
     */
    public function actionDeleteAll($keys){
        $serverNames = explode(',', $keys);
        $servers = implode('","', $serverNames);
        $servers = '"'.$servers.'"';
        $model = new Server();
        $model -> deleteAll("serverName in ($servers)");
        Yii::info("delete selected " . count($servers) . " servers, they are $keys", 'server');
        return $this->redirect(['index']);
    }
    
    /**
     * 添加新的服务器
     */
    public function actionCreate(){
        $model = new Server();
        $model->scenario = Server::SCENARIO_SAVE;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::info("create a server named $model->serverName", 'server');
            $this->redirect(['view', 'serverName' => $model->serverName]);
        }
        return $this->render('create',[
            'model' => $model
        ]);
    }
    
    /**
     * 根据服务器名删除服务器
     * @param string $serverName
     */
    public function actionDelete($serverName){
        $model = Server::findServerByName($serverName);
        $model->delete();
        Yii::info("delete a server named $model->serverName", 'server');
        return $this->redirect(['index']);
    }
    
    /**
     * 批量导入server
     * @return string
     */
    public function actionImport(){
        $model = new Server();
        $model->scenario = Server::SCENARIO_IMPORT;
        $state = [
            'message' => 'Info:please import a xml file. Format as below:</br>'
                        . '&lt;?xml version="1.0" encoding="UTF-8"?&gt;</br>'
                        . '&lt;message&gt;</br>'
                        . '&nbsp;&nbsp;&lt;Server&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;serverName&gt;server2&lt;/serverName&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;serverIp&gt;1.1.1.1&lt;/serverIp&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;status&gt;1&lt;/status&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;operatingSystem&gt;1&lt;/operatingSystem&gt;</br>'
                        . '&nbsp;&nbsp;&lt;/Server&gt;</br>'
                        . '&nbsp;&nbsp;&lt;Server&gt;</br>'
                        . '&nbsp;&nbsp;&nbsp;&nbsp;······</br>'
                        . '&nbsp;&nbsp;&lt;/Server&gt;</br>'
                        . '&lt;/message&gt;</br>',
            'class' => 'alert-info',
            'percent' => 0,
            'label' => '0%',
        ];
        if($model->load(Yii::$app->request->post())){
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            //$xmlStr = file_get_contents($model->importFile->tempName);
            try {
                $xmlArray = simplexml_load_file($model->importFile->tempName);
                $servers = json_decode(json_encode($xmlArray), true);
                $columns = ['serverName', 'serverIp', 'status', 'operatingSystem', 'createTime', 'updateTime'];
                $allServers = null;
                if(ArrayHelper::isIndexed($servers['Server'])){
                    $allServers = $servers['Server'];
                }else{
                    $allServers = [$servers['Server']];
                }
                $rows = ArrayHelper::getColumn($allServers, function($element){
                    $now = date('Y-m-d H:i:s', time());
                    return [$element['serverName'], $element['serverIp'], $element['status'], $element['operatingSystem'], $now, $now];
                });
                    $db = Yii::$app->db;
                    $db->createCommand()->batchInsert('server', $columns, $rows)->execute();
                    $serverStr = implode(',', ArrayHelper::getColumn($allServers, 'serverName'));
                    Yii::info("import " . count($rows) . " servers, they are $serverStr", 'server');
                    $state['message'] = 'Success:import success, there are totally ' . count($rows) .' servers added to DB, they are ' . $serverStr;
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
     * 启用server
     * @param string $serverName
     */
    public function actionEnable($serverName){
        $model = Server::findServerByName($serverName);
        $model->scenario = Server::SCENARIO_CHANGE_STATUS;
        $model->status = 1;
        $model->save();
        Yii::info("Enable server $serverName",'server');
        return $this->redirect(['index']);
    }
    /**
     * 禁用server
     * @param string $serverName
     */
    public function actionDisable($serverName){
        $model = Server::findServerByName($serverName);
        $model->scenario = Server::SCENARIO_CHANGE_STATUS;
        $model->status=0;
        $model->save();
        Yii::info("Disable server $serverName", 'server');
        return $this->redirect(['index']);
    }
    /**
     * 更改一个server
     * @param string $serverName
     */
    public function actionUpdate($serverName){
        $model = Server::findServerByName($serverName);
        $model->scenario = Server::SCENARIO_SAVE;
        if($model->load(Yii::$app->request->post()) && $model->save()){
            Yii::info("Update the server named $serverName", 'server');
            return $this->redirect(['view', 'serverName' => $model->serverName]);
        }
        return $this->render('update', [
            'model' => $model
        ]);
    }
    /**
     * 查看server详情
     * @param string $serverName
     */
    public function actionView($serverName){
        $model = Server::findServerByName($serverName);
        return $this->render('view', [
            'model' => $model
        ]);
    }
}