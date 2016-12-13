<?php
namespace app\models;

use yii\db\ActiveRecord;

class CPU extends ActiveRecord{
    /**
     * 设置模型对应表明
     * @return string
     */
    public static function tableName(){
        return 'cpu';
    }
}