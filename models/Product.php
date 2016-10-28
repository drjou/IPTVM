<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Product extends ActiveRecord{
    public $importFile;
    const SCENARIO_SAVE = 'save';
    const SCENARIO_IMPORT = 'import';
    /**
     * 设置模型对应的表名
     * @return string
     */
    public static function tableName(){
        return 'product';
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
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    
    /**
     * 设置表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            ['productName', 'required'],
            ['importFile', 'file', 'skipOnEmpty' => false, 'mimeTypes' => ['application/xml', 'text/xml'],'extensions' => ['xml'], 'maxSize' => 50*1024*1024],
            ['productName', 'trim'],
            ['productName', 'string', 'length' => [4, 20]],
            ['productName', 'unique'],
            ['channels', 'safe'],
        ];
    }
    /**
     * 设置不同场景要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return [
            self::SCENARIO_SAVE => ['productName', 'channels'],
            self::SCENARIO_IMPORT => ['importFile'],
        ];
    }
    
    /**
     * 根据productId获取product信息
     * @param int $productId
     * @return \app\models\Product|NULL
     */
    public static function findProductById($productId){
        if(($model = self::findOne($productId)) !== null){
            return $model;
        }else{
            throw new NotFoundHttpException("The product whose productId is $productId don't exist, please try the right way to access product.");
        }
    }
    /**
     * 获取该产品的产品充值卡信息
     * @return ActiveQuery
     */
    public function getProductcards(){
        return $this->hasMany(Productcard::className(), ['productId' => 'productId']);
    }
    
    /**
     * 获取产品包的预绑定信息
     * @return ActiveQuery
     */
    public function getBindAccounts(){
        return $this->hasMany(Stbbind::className(), ['productId' => 'productId']);
    }
    
    /**
     * 获取产品包所属账户列表
     */
    public function getAccounts(){
        return $this->hasMany(Account::className(), ['accountId' => 'accountId'])
                ->viaTable('account_product', ['productId' => 'productId']);
    }
    /**
     * 设置表单传入的channels值
     * @param array $channels
     */
    public function setChannels($channels){
        $this->channels = $channels;
    }
    
    /**
     * 获取产品包下的channel列表
     */
    public function getChannels(){
        return $this->hasMany(Channel::className(), ['channelId' => 'channelId'])
                ->viaTable('product_channel', ['productId' => 'productId']);
    }
    
    public function findProductcards(){
        $cardProvider = new ArrayDataProvider([
            'allModels' => $this->productcards,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'cardNumber',
                    'cardValue',
                    'cardState',
                    'useDate',
                    'accountId',
                ],
            ]
        ]);
        return $cardProvider;
    }
    
    /**
     * 根据getBindAccounts构建dataProvider
     * @return \yii\data\ArrayDataProvider
     */
    public function findBindAccounts(){
        $bindProvider = new ArrayDataProvider([
            'allModels' => $this->bindAccounts,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'accountId',
                    'bindDay',
                    'isActive',
                    'activeDate',
                ],
            ]
        ]);
        return $bindProvider;
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