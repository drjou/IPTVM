<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Productcard extends ActiveRecord{
    public $importFile;
    public $productName;
    const SCENARIO_SAVE = 'save';
    const SCENARIO_IMPORT = 'import';
    const SCENARIO_API = 'api';
    /**
     * 设置模型对应的表名
     * @return string
     */
    public static function tableName(){
        return 'productcard';
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
            [['cardNumber', 'cardValue', 'productName', 'cardState'], 'required'],
            ['importFile', 'file', 'skipOnEmpty' => false, 'mimeTypes' => ['application/xml', 'text/xml'],'extensions' => ['xml'], 'maxSize' => 50*1024*1024],
            ['cardNumber', 'trim'],
            ['cardNumber', 'string', 'length' => [4,20]],
            ['cardNumber', 'unique'],
        ];
    }
    
    /**
     * 设置不同场景要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return [
            self::SCENARIO_SAVE => ['cardNumber', 'cardValue', 'productName', 'cardState'],
            self::SCENARIO_IMPORT => ['importFile'],
            self::SCENARIO_API => ['cardNumber', 'cardValue', 'productId', 'cardState'],
        ];
    }
    
    /**
     * 设置表单中显示对应的名称
     * {@inheritDoc}
     * @see \yii\base\Model::attributeLabels()
     */
    public function attributeLabels(){
        return [
            'cardValue' => 'Days',
            'productName' => 'For Product',
            'cardState' => 'Used',
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
    
    public function beforeSave($insert){
        if($this->scenario == self::SCENARIO_SAVE){
            $this->productId = $this->productName;
        }
        return parent::beforeSave($insert);
    }
}