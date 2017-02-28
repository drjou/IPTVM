<?php
namespace app\models;

use yii\db\ActiveRecord;
class MySql extends ActiveRecord{
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'mysql';
    }
    
    /**
     * 获取Mysql所属服务器的信息
     * @return ActiveQuery
     */
    public function getServerInfo(){
        return $this->hasOne(Server::className(), ['serverName' => 'server']);
    }
}