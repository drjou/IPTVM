<?php
namespace app\models;

use yii\db\ActiveRecord;

class Productcard extends ActiveRecord{
    public $productName;
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
    /**
     * 根据cardNumber获取充值卡信息
     * @param string $cardNumber
     * @return \app\models\Productcard|NULL
     */
    public static function findProductcardById($cardNumber){
        return self::findOne($cardNumber);
    }
    
    /**
     * 获取充值卡对应的产品
     * @return ActiveQuery
     */
    public function getProduct(){
        return $this->hasOne(Product::className(), ['productId' => 'productId']);
    }
}