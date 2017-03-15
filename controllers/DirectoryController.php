<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\DirectorySearch;
use app\models\Directory;
use yii\helpers\ArrayHelper;
use app\models\Channel;
use yii\web\HttpException;
use yii\db\Exception;
use yii\web\UploadedFile;

class DirectoryController extends Controller{
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
     * Index Action 显示所有的目录
     * @return string
     */
    public function actionIndex(){
        $searchModel = new DirectorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * 删除选中的一些目录
     * @param string $keys
     * @return \yii\web\Response
     */
    public function actionDeleteAll($keys){
        //将得到的字符串转为php数组
        $directoryIds = explode(',', $keys);
        //防止用户在url中手动输入参数执行删除操作
        $directoryNames = [];
        foreach ($directoryIds as $directoryId){
            $dir = Directory::findDirectoryById($directoryId);
            if(!empty($dir->childrenDirectories)){
                throw new HttpException(500, 'these directories contain directory that has children, you can\'t delete it');
            }
            array_push($directoryNames, $dir->directoryName);
        }
        //使用","作为分隔符将数组转为字符串
        $directories = implode('","', $directoryIds);
        //在最终的字符串前后各加一个"
        $directories = '"' . $directories . '"';
        $model =new Directory();
        //调用model的deleteAll方法删除数据
        $model->deleteAll("directoryId in($directories)");
        Yii::info('delete selected ' . count($directoryNames) . ' directories, they are ' . implode(',', $directoryNames), 'administrator');
        return $this->redirect(['index']);
    }
    
    /**
     * import directories
     */
    public function actionImport(){
        $model = new Directory();
        $model->scenario = Directory::SCENARIO_IMPORT;
        $state = [
            'message' => 'Info:please import a xml file. Format as below:</br>'
            . '&lt;?xml version="1.0" encoding="UTF-8"?&gt;</br>'
            . '&lt;message&gt;</br>'
            . '&nbsp;&nbsp;&lt;Directory&gt;</br>'
            . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;directoryName&gt;MiddleEastVIP&lt;/directoryName&gt;</br>'
            . '&nbsp;&nbsp;&nbsp;&nbsp;&lt;showOrder&gt;1&lt;/showOrder&gt;</br>'
            . '&nbsp;&nbsp;&lt;/Directory&gt;</br>'
            . '&nbsp;&nbsp;&lt;Directory&gt;</br>'
            . '&nbsp;&nbsp;&nbsp;&nbsp;······</br>'
            . '&nbsp;&nbsp;&lt;/Directory&gt;</br>'
            . '&lt;/message&gt;</br>',
            'class' => 'alert-info',
            'percent' => 0,
            'label' => '0%',
        ];
        if($model->load(Yii::$app->request->post())){
            $model->importFile = UploadedFile::getInstance($model, 'importFile');
            try {
                $xmlArray = simplexml_load_file($model->importFile->tempName);
                $directories = json_decode(json_encode($xmlArray), true);
                $columns = ['directoryName', 'showOrder', 'createTime', 'updateTime'];
                $rows = ArrayHelper::getColumn($directories['Directory'], function($element){
                    $now = time();
                    return [$element['directoryName'], $element['showOrder'], $now, $now];
                });
                $db = Yii::$app->db;
                $db->createCommand()->batchInsert('directory', $columns, $rows)->execute();
                $directoryStr = implode(',', ArrayHelper::getColumn($directories['Directory'], 'directoryName'));
                Yii::info("import " . count($rows) . " directories, they are $directoryStr", 'administrator');
                $state['message'] = 'Success:import success, there are totally ' . count($rows) .' directories added to DB, they are ' . $directoryStr;
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
     * 导出所有的directories
     */
    public function actionExport(){
        $model = new Directory();
        $directories = $model->find()->all();
        $response = Yii::createObject([
            'class' => 'yii\web\Response',
            'format' => \yii\web\Response::FORMAT_XML,
            'formatters' => [
                \yii\web\Response::FORMAT_XML => [
                    'class' => 'yii\web\XmlResponseFormatter',
                    'rootTag' => 'message', //根节点
                    'itemTag' => 'directory',
                ],
            ],
            'data' => $directories,
        ]);
        $formatter = new \yii\web\XmlResponseFormatter();
        $formatter->rootTag = 'message';
        $formatter->format($response);
        Yii::$app->response->sendContentAsFile($response->content, 'directories.xml')->send();
        Yii::info('export all directories', 'administrator');
    }
    
    /**
     * 查看directory的详细信息
     * @param int $directoryId
     * @return string
     */
    public function actionView($directoryId){
        $model = Directory::findDirectoryById($directoryId);
        $childrenProvider = $model->findChildrenDirectories();
        $channelProvider = $model->findChannels();
        return $this->render('view', [
            'model' => $model,
            'childrenProvider' => $childrenProvider,
            'channelProvider' => $channelProvider,
        ]);
    }
    /**
     * 创建新的目录
     * @return string
     */
    public function actionCreate(){
        $model = new Directory();
        $model->scenario = Directory::SCENARIO_SAVE;
        if($model->load(Yii::$app->request->post())){
            if(!empty($model->channels)){//添加的channels不为空
                //将channels信息添加到channel_directory表中
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();
                try {
                    if($model->save()){
                        $columns = ['directoryId', 'channelId'];
                        $rows = [];
                        foreach ($model->channels as $channel){
                            $row = [$model->directoryId, $channel];
                            array_push($rows, $row);
                        }
                        $db->createCommand()->batchInsert('channel_directory', $columns, $rows)->execute();
                        $transaction->commit();
                        Yii::info("create directory $model->directoryName", 'administrator');
                        return $this->redirect(['view', 'directoryId' => $model->directoryId]);
                    }else{
                        $transaction->rollBack();
                        $model->addError('directoryName', "add directory $model->directoryName failed! please try again.");
                    }
                }catch(Exception $e){
                    $transaction->rollBack();
                    $model->addError('directoryName', "add directory $model->directoryName failed! please try again.");
                }
            }else{
                if($model->save()){
                    Yii::info("create directory $model->directoryName", 'administrator');
                    return $this->redirect(['view', 'directoryId' => $model->directoryId]);
                }
            }
        }
        $directories = $model->getAllDirectories();
        $channels = ArrayHelper::map(Channel::find()->select(['channelId', 'channelName'])->all(), 'channelId', 'channelName');
        return $this->render('create', [
            'model' => $model,
            'directories' => $directories,
            'channels' => $channels,
        ]);
    }
    /**
     * 更新目录的信息
     * @param int $directoryId
     * @return string
     */
    public function actionUpdate($directoryId){
        $model = Directory::findDirectoryById($directoryId);
        $model->scenario = Directory::SCENARIO_SAVE;
        $oldChannels = ArrayHelper::getColumn($model->channels, 'channelId');
        if($model->load(Yii::$app->request->post())){
            //计算channels的差别，然后进行差量同步到数据库
            $newChannels = $model->channels;
            if(empty($newChannels)){//默认为空字符串，赋值为空数组防止后面array_diff出错
                $newChannels = [];
            }
            //增加的channels
            $addChannels = array_diff($newChannels, $oldChannels);
            //删除的channels
            $delChannels = array_diff($oldChannels, $newChannels);
            if(!empty($addChannels) || !empty($delChannels)){//channels相比之前发生了变化
                $db = Yii::$app->db;
                $transaction = $db->beginTransaction();
                try{
                    if($model->save()){
                        if(!empty($addChannels)){//增加的channels不为空，则向channel_directory表中添加
                            $columns = ['directoryId', 'channelId'];
                            $rows = [];
                            foreach ($addChannels as $channel){
                                $row = [$model->directoryId, $channel];
                                array_push($rows, $row);
                            }
                            $db->createCommand()->batchInsert('channel_directory', $columns, $rows)->execute();
                        }
                        if(!empty($delChannels)){//删除的channels不为空，则从channel_directory表中删除
                            $db->createCommand()->delete('channel_directory', ['directoryId' => $model->directoryId, 'channelId' => $delChannels])->execute();
                        }
                        $transaction->commit();
                        Yii::info("update directory $model->directoryName", 'administrator');
                        return $this->redirect(['view', 'directoryId' => $model->directoryId]);
                    }else{
                        $transaction->rollBack();
                        $model->addError('directoryName', "update directory $model->directoryName failed! please try again.");
                    }
                }catch (Exception $e){
                    $transaction->rollBack();
                    $model->addError('directoryName', "update directory $model->directoryName failed! please try again.");
                }
            }else{
                if($model->save()){
                    Yii::info("update directory $model->directoryName", 'administrator');
                    return $this->redirect(['view', 'directoryId' => $model->directoryId]);
                }
            }
        }
        //directories中去掉子节点及本身作为父节点的选项
        $directories = array_diff($model->getAllDirectories(), ArrayHelper::map($model->childrenDirectories, 'directoryId', 'directoryName'), [$model->directoryId => $model->directoryName]);
        $channels = ArrayHelper::map(Channel::find()->select(['channelId', 'channelName'])->all(), 'channelId', 'channelName');
        return $this->render('update', [
            'model' => $model,
            'directories' => $directories,
            'channels' => $channels,
        ]);
    }
    
    /**
     * 删除子目录不为空的目录
     * @param int $directoryId
     * @return \yii\web\Response
     */
    public function actionDelete($directoryId){
        $model = Directory::findDirectoryById($directoryId);
        if(!empty($model->childrenDirectories)){
            throw new HttpException(500, 'you can\'t delete the directory that has children.');
        }
        $model->delete();
        Yii::info("delete directory $model->directoryName", 'administrator');
        return $this->redirect(['index']);
    }
}