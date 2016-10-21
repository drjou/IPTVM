<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;

class Account extends ActiveRecord{
    //产品包
    //public $products;
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
            [['accountId', 'state', 'enable'], 'required'],
            ['accountId', 'unique'],
            ['accountId', 'trim'],
            ['products', 'required', 'when' => function($model){
                return $model->state == '1002';
            }, 'whenClient' => "function (attribute, value) {
                return $('#account-state').val() == '1002';
            }"],
        ];
    }
    /**
     * 设置表单里显示的名称
     * {@inheritDoc}
     * @see \yii\base\Model::attributeLabels()
     */
    public function attributeLabels(){
        return [
            'products' => 'Pre Bind Products',
        ];
    }
    /**
     * 设置属性products
     * @param array $products
     */
    public function setProducts($products){
        $this->products = $products;
    }
    /**
     * 获取属性products
     */
    public function getProducts(){
        return $this->hasMany(Product::className(), ['productId' => 'productId'])
        ->viaTable('stbbind', ['accountId' => 'accountId']);
    }
    
    /**
     * 根据accountId获取account
     * @param string $accountId
     * @return \app\models\Account|NULL
     */
    public static function findAccountById($accountId){
        if(($model = self::findOne($accountId)) !== null){
            return $model;
        }else{
            throw new NotFoundHttpException("The account whose accountId is $accountId don't exist, please try the right way to access account.");
        }
    }
    /**
     * 获取账户初始预绑定的产品
     * @return ActiveQuery
     */
    public function getBindProducts(){
        return $this->hasMany(Stbbind::className(), ['accountId' => 'accountId']);
    }
    /**
     * 获取账户下的产品及过期时间
     * @return ActiveQuery
     */
    public function getAccountProducts(){
        return $this->hasMany(AccountProduct::className(), ['accountId' => 'accountId']);
    }
    /**
     * 获取账户使用过的产品充值卡
     * @return ActiveQuery
     */
    public function getProductcards(){
        return $this->hasMany(Productcard::className(), ['accountId' => 'accountId']);
    }
    /**
     * 根据getBindProducts构建dataProvider
     * @return \yii\data\ArrayDataProvider
     */
    public function findBindProducts(){
        $bindProvider = new ArrayDataProvider([
            'allModels' => $this->bindProducts,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'productName' => [
                        'asc' => ['product.productName' => SORT_ASC],
                        'desc' => ['product.productName' => SORT_DESC],
                    ],
                    'activeDate',
                ],
            ]
        ]);
        return $bindProvider;
    }
    /**
     * 根据getAccountProducts方法构建dataProvider
     * @return \yii\data\ArrayDataProvider
     */
    public function findAccountProducts(){
        $productProvider = new ArrayDataProvider([
            'allModels' => $this->accountProducts,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'productName' => [
                        'asc' => ['product.productName' => SORT_ASC],
                        'desc' => ['product.productName' => SORT_DESC],
                    ],
                    'endDate',
                ],
            ]
        ]);
        return $productProvider;
    }
    
    /**
     * 根据getProducts方法构建dataProvider
     * @return \yii\data\ArrayDataProvider
     */
    public function findProducts($accountId){
        $productProvider = new ArrayDataProvider([
            'allModels' => $this->getProducts()->joinWith('accountProduct')->where(['accountId' => $accountId])->all(),//自动调用getProducts方法
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