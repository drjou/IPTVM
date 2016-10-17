<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ArrayDataProvider;

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
    /**
     * 根据accountId获取account
     * @param string $accountId
     * @return \app\models\Account|NULL
     */
    public static function findAccountById($accountId){
        return self::findOne($accountId);
    }
    /**
     * 获取账户拥有的所有产品
     */
    public function getProducts(){
        return $this->hasMany(Product::className(), ['productId' => 'productId'])
                ->viaTable('account_product', ['accountId' => 'accountId']);
    }
    /**
     * 获取账户使用过的产品充值卡
     * @return ActiveQuery
     */
    public function getProductcards(){
        return $this->hasMany(Productcard::className(), ['accountId' => 'accountId']);
    }
    /**
     * 根据getProducts方法构建dataProvider
     * @return \yii\data\ArrayDataProvider
     */
    public function findProducts(){
        $productProvider = new ArrayDataProvider([
            'allModels' => $this->products,//自动调用getProducts方法
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort'=>[
                'attributes'=>[
                    'productName',
                ]
            ]
        ]);
        return $productProvider;
    }
    /**
     * 根据getProductcards方法构建dataProvider
     * @return \yii\data\ArrayDataProvider
     */
    public function findProductcards(){
        $productcardProvider = new ArrayDataProvider([
            'allModels' => $this->productcards,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort'=>[
                'attributes'=>[
                    'cardNumber',
                    'cardValue',
                    'useDate',
                ]
            ]
        ]);
        return $productcardProvider;
    }
}