<?php
namespace app\models;

use yii\db\ActiveRecord;
class Server extends ActiveRecord{
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'server';
    }
    
    public function getCpuInfo(){
        return $this->hasMany(CPU::className(), ['server' => 'serverName']);
    }
    
    public function getRamInfo(){
        return $this->hasMany(RAM::className(), ['server' => 'serverName']);
    }
    
    public function getDiskInfo(){
        return $this->hasMany(Disk::className(), ['server' => 'serverName']);
    }
    
    public function getLoadInfo(){
        return $this->hasMany(Load::className(), ['server' => 'serverName']);
    }
}