<?php
namespace app\models;

use yii\db\ActiveRecord;

class Stbbind extends ActiveRecord{
    //产品名
    public $productName;
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'stbbind';
    }
    /**
     * 获取产品的详情
     * @return ActiveQuery
     */
    public function getProduct(){
        return $this->hasOne(Product::className(), ['productId' => 'productId']);
    }
}