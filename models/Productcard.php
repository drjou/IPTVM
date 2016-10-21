<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

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
        if(($model = self::findOne($cardNumber)) !== null){
            return $model;
        }else{
            throw new NotFoundHttpException("The productcard whose cardNumber is $cardNumber don't exist, please try the right way to access productcard.");
        }
    }
    
    /**
     * 获取充值卡对应的产品
     * @return ActiveQuery
     */
    public function getProduct(){
        return $this->hasOne(Product::className(), ['productId' => 'productId']);
    }
}