<?php
namespace app\models;

use yii\db\ActiveRecord;

class AccountProduct extends ActiveRecord{
    //产品名
    public $productName;
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'account_product';
    }
    /**
     * 获取产品的详情
     * @return ActiveQuery
     */
    public function getProduct(){
        return $this->hasOne(Product::className(), ['productId' => 'productId']);
    }
    /**
     * 获取过期属性
     * @return string
     */
    public function getExpire(){
        if($this->timeCompare($this->endDate)){
            return 'not expired';
        }
        return 'expired';
    }
    /**
     * 判断产品是否过期
     * @param string $date
     * @return number
     * 1表示未过期，0表示已过期
     */
    private function timeCompare($date){
        $now = date("Y-m-d", time());
        if(strtotime($now) < strtotime($date)){
            return 1;
        }
        return 0;
    }
}