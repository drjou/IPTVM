<?php
namespace app\models;

use yii;
use yii\db\ActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\web\HttpException;

class Timezone extends ActiveRecord{
    
    public static function tableName(){
        return 'timezone';
    }
    
    /**
     * 自动更新创建时间和修改时间
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createTime',
                'updatedAtAttribute' => 'updateTime',
            ],
        ];
    }
    
    /**
     * 设置验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['timezone', 'status', 'continent', 'country', 'icon', 'chinese'], 'required'],
            [['timezone', 'continent', 'country', 'icon', 'chinese'], 'trim'],
            ['timezone', 'unique'],
            ['timezone', 'validateTimezone'],
        ];
    }
    /**
     * 验证时区是否有效
     * @param string $attribute
     * @param array $params
     */
    public function validateTimezone($attribute, $params){
        try{
            \Yii::$app->setTimeZone($this->timezone);
        }catch(\Exception $e){
            $this->addError($attribute, "Timezone is invalid");
        }
    }
    /**
     * 创建前给isCurrent属性赋值
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::beforeSave()
     */
    public function beforeSave($insert){
        if($this->isNewRecord){
            $this->isCurrent = 0;
        }
        return parent::beforeSave($insert);
    }
    
    /**
     * 获取当前设置的时区
     */
    public static function getCurrentTimezone(){
        $model = new Timezone();
        return $model->find()->where(['status' => 1, 'isCurrent' => 1])->one();
    }
    
    /**
     * 获取所有可以使用的时区
     */
    public static function getAvailableTimezone(){
        $model = new Timezone();
        return $model->find()->where(['status' => 1, 'isCurrent' => 0])->all();
    }
    
    /**
     * 根据当前设置的时区转换时间
     * @param int $time
     */
    public static function date($time){
        if(empty($time)){
            return '(not set)';
        }
        $model = new Timezone();
        $current = $model->getCurrentTimezone();
        if(!empty($current)){
            try{
                Yii::$app->setTimeZone($current->timezone);
                return date("Y-m-d H:i:s", $time);
            }catch (\Exception $e){
                throw new HttpException(500, "Timezone ID $current->timezone is invalid");               
            }
        }else{
            return date("Y-m-d H:i:s", $time);
        }
    }
}