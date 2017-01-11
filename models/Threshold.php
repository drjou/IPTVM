<?php
namespace app\models;

use yii\db\ActiveRecord;
class Threshold extends ActiveRecord{
    const SCENARIO_UPDATE = 'update';
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'threshold';
    }
    
    /**
     * 设置验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['cpu', 'memory', 'disk', 'loads'], 'required'],
            [['cpu', 'memory', 'disk', 'loads'], 'trim'],
            [['cpu', 'memory', 'disk', 'loads'], 'double'],
        ];
    }
    
    /**
     * 设置不同场景下的验证属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return [
            self::SCENARIO_UPDATE => ['cpu', 'memory', 'disk', 'loads']
        ];
    }
    /**
     * 此表中没有主键
     * {@inheritDoc}
     * @see \yii\db\ActiveRecord::primaryKey()
     */
    public static function primaryKey(){
        return ['cpu', 'memory', 'disk', 'loads'];
    }
}