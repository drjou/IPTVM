<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\web\NotFoundHttpException;
class Server extends ActiveRecord{
    public $importFile;
    public $servers;
    const SCENARIO_SAVE = 'save';
    const SCENARIO_IMPORT = 'import';
    const SCENARIO_CHANGE_STATUS = 'changeStatus';
    const SCENARIO_SELECT_SERVERS = 'selectServers';
    const SCENARIO_CHANGE_SERVER = 'changeServer';
    const SCENARIO_SELECT_STREAMS = 'selectStreams';
    
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'server';
    }
    
    /**
     * 自动更新创建时间和修改时间
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createTime',
                'updatedAtAttribute' => 'updateTime',
                'value' => new Expression('NOW()')
            ]
        ];
    }
    
    /**
     * 设置验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['serverName', 'serverIp', 'status', 'operatingSystem', 'servers', 'streams'], 'required'],
            ['importFile', 'file', 'skipOnEmpty' => false, 'mimeTypes' => ['application/xml', 'text/xml'], 'extensions' => ['xml'], 'maxSize' => 50*1024*1024],
            ['serverName', 'trim'],
            ['serverName', 'string', 'length' => [1, 20]],
            ['serverName', 'unique']
        ];
    }
    
    /**
     * 设置不同场景下的验证属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return [
            self::SCENARIO_SAVE => ['serverName', 'serverIp', 'status', 'operatingSystem'],
            self::SCENARIO_IMPORT => ['importFile'],
            self::SCENARIO_CHANGE_STATUS => ['status'],
            self::SCENARIO_SELECT_SERVERS => ['servers'],
            self::SCENARIO_CHANGE_SERVER => ['serverName'],
            self::SCENARIO_SELECT_STREAMS => ['streams']
        ];
    }
    
    /**
     * 用serverName获取server
     * @param string $serverName
     * @throws NotFoundHttpException
     * @return boolean
     */
    public static function findServerByName($serverName){
        if(($model = self::findOne($serverName)) !== null){
            return $model;
        }else{
            throw new NotFoundHttpException("The server whose serverName is $serverName doesn't exist, please try the right way to access server.");
        }
    }
    /**
     * 获得相应时间段内的CPU信息
     * @param string $startTime
     * @param string $endTime
     */
    public function getCpuInfo($startTime, $endTime){
        return $this->hasMany(CPU::className(), ['server' => 'serverName'])
        ->where('recordTime between "'.$startTime.'" and "'.$endTime.'"');
    }
    /**
     * 获得相应时间段内的RAM信息
     * @param string $startTime
     * @param string $endTime
     */
    public function getRamInfo($startTime, $endTime){
        return $this->hasMany(RAM::className(), ['server' => 'serverName'])
        ->where('recordTime between "'.$startTime.'" and "'.$endTime.'"');
    }
    /**
     * 获得相应时间段内的DISK信息
     * @param string $startTime
     * @param string $endTime
     */
    public function getDiskInfo($startTime, $endTime){
        return $this->hasMany(Disk::className(), ['server' => 'serverName'])
        ->where('recordTime between "'.$startTime.'" and "'.$endTime.'"');
    }
    /**
     * 获得相应时间段内的LOAD信息
     * @param string $startTime
     * @param string $endTime
     */
    public function getLoadInfo($startTime, $endTime){
        return $this->hasMany(Load::className(), ['server' => 'serverName'])
        ->where('recordTime between "'.$startTime.'" and "'.$endTime.'"');
    }
    /**
     * 设置属性$streams
     * @param array $streams
     */
    public function setStreams($streams){
        $this->streams = $streams;
    }
    /**
     * 将server表与stream表关联
     * @return ActiveQuery
     */
    public function getStreams(){
        return $this->hasMany(Stream::className(), ['server' => 'serverName']);
    }
}