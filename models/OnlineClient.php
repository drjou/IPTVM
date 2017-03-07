<?php
namespace app\models;
use yii\db\ActiveRecord;
use app\models\Server;

class OnlineClient extends ActiveRecord{
    
    public $status;
    
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'online_client';
    }
    
    /**
     * 获取对应server的详情
     * @return ActiveQuery
     */
    public function getServerInfo(){
        return $this->hasOne(Server::className(), ['serverName' => 'server']);
    }
}