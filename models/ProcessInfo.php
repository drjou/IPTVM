<?php
namespace app\models;

use yii\db\ActiveRecord;

class ProcessInfo extends ActiveRecord{
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'process_info';
    }
}