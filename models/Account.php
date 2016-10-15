<?php
namespace app\models;

use yii\db\ActiveRecord;

class Account extends ActiveRecord{
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'account';
    }
    /**
     * 设置验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['accountId', 'state'], 'required'],
        ];
    }
}