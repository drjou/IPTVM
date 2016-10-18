<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ArrayDataProvider;

class Product extends ActiveRecord{
    /**
     * 设置模型对应的表名
     * @return string
     */
    public static function tableName(){
        return 'product';
    }
    /**
     * 设置表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            ['productName', 'required'],
        ];
    }
    /**
     * 根据productId获取product信息
     * @param int $productId
     * @return \app\models\Product|NULL
     */
    public static function findProductById($productId){
        return self::findOne($productId);
    }
    /**
     * 获取产品包所属账户列表
     */
    public function getAccounts(){
        return $this->hasMany(Account::className(), ['accountId' => 'accountId'])
                ->viaTable('account_product', ['productId' => 'productId']);
    }
    /**
     * 获取产品包下的channel列表
     */
    public function getChannels(){
        return $this->hasMany(Channel::className(), ['channelId' => 'channelId'])
                ->viaTable('product_channel', ['productId' => 'productId']);
    }
    /**
     * 根据getAccount构建dataProvider
     * @return \yii\data\ArrayDataProvider
     */
    public function findAccounts(){
        $accountProvider = new ArrayDataProvider([
            'allModels' => $this->accounts,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'accountId',
                    'state',
                ],
            ]
        ]);
        return $accountProvider;
    }
    /**
     * 根据getChannels构建dataProvider
     * @return \yii\data\ArrayDataProvider
     */
    public function findChannels(){
        $channelProvider = new ArrayDataProvider([
            'allModels' => $this->channels,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'channelName',
                    'channelIp',
                    'channelType',
                    'languageName' => [
                        'asc' => ['language.languageName' => SORT_ASC],
                        'desc' => ['language.languageName' => SORT_DESC],
                    ],
                ]
            ]
        ]);
        return $channelProvider;
    }
}