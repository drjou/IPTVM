<?php
namespace app\models;

use yii\db\ActiveRecord;

class Productcard extends ActiveRecord{
    /**
     * 设置模型对应的表名
     * @return string
     */
    public static function tableName(){
        return 'productcard';
    }
    /**
     * 设置表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['cardNumber', 'cardValue', 'productId', 'cardState', 'useDate', 'accountId'], 'required'],
        ];
    }
}