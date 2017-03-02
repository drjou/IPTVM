<?php
namespace app\models;

use yii\db\ActiveRecord;
class Nginx extends ActiveRecord{
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'nginx';
    }
    
    /**
     * 获取Nginx所在服务器的信息
     * @return ActiveQuery
     */
    public function getServerInfo(){
        return $this->hasOne(Server::className(), ['serverName' => 'server']);
    }
}