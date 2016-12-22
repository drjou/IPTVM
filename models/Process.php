<?php
namespace app\models;

use yii\db\ActiveRecord;

class Process extends ActiveRecord{
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'process';
    }
    
    /**
     * 返回所有服务器的所有进程信息
     */
    public function getProcesses(){
        return $this->hasMany(ProcessInfo::className(), ['processName' => 'processName','server' => 'server']);
    }
}