<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ArrayDataProvider;
use yii\web\NotFoundHttpException;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Channel extends ActiveRecord{
    //批量导入的文件
    public $importFile;
    //语言名
    public $languageName;
    //channel对应的图片
    public $thumbnail;
    
    const SCENARIO_ADD = 'add';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_IMPORT = 'import';
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'channel';
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
     * 设置表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['channelName', 'channelIp', 'channelUrl', 'urlType', 'channelType', 'languageName'], 'required'],
            ['importFile', 'file', 'skipOnEmpty' => false, 'mimeTypes' => ['application/xml', 'text/xml'],'extensions' => ['xml'], 'maxSize' => 50*1024*1024],
            ['channelPic', 'required', 'on' => self::SCENARIO_UPDATE],
            [['channelName', 'channelIp', 'channelUrl'], 'trim'],
            ['channelName', 'string', 'length' => [3, 10]],
            ['channelName', 'unique'],
            ['channelIp', 'ip'],
            ['thumbnail', 'file', 'mimeTypes' => 'image/*', 'extensions' => ['jpg', 'png', 'gif'], 'maxSize' => 1024*1024],
            ['thumbnail', 'file', 'skipOnEmpty' => false, 'on' => self::SCENARIO_ADD],
        ];
    }
    /**
     * 设置不同场景下要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return [
            self::SCENARIO_ADD => ['channelName', 'channelIp', 'thumbnail', 'channelUrl', 'urlType', 'channelType', 'languageName'],
            self::SCENARIO_UPDATE => ['channelName', 'channelIp', 'channelPic', 'thumbnail', 'channelUrl', 'urlType', 'channelType', 'languageName'],
            self::SCENARIO_IMPORT => ['importFile'],
        ];
    }
    
    /**
     * 设置表单显示的名称
     * {@inheritDoc}
     * @see \yii\base\Model::attributeLabels()
     */
    public function attributeLabels(){
        return [
            'channelIp' => 'Channel IP Address',
            'channelPic' => 'Channel Picture Path'
        ];
    }
    /**
     * 根据channelId获取channel信息
     * @param int $channelId
     * @return \app\models\Channel|NULL
     */
    public static function findChannelById($channelId){
        if(($model = self::findOne($channelId)) !== null){
            return $model;
        }else{
            throw new NotFoundHttpException("The channel whose channelId is $channelId don't exist, please try the right way to access channel.");
        }
    }
    
    /**
     * 获取该channels对应的account（API实现getDirectoryChannel时用到）
     */
    public function getAccounts(){
        return $this->hasMany(AccountProduct::className(), ['productId' => 'productId'])
                ->via('products');
    }
    
    /**
     * 获取Channel的语言
     * @return ActiveQuery
     */
    public function getLanguage(){
        return $this->hasOne(Language::className(), ['languageId' => 'languageId']);
    }
    /**
     *  获取channel所在的产品包
     */
    public function getProducts(){
        return $this->hasMany(Product::className(), ['productId' => 'productId'])
                ->viaTable('product_channel', ['channelId' => 'channelId']);
    }
    /**
     * 获取channel所在的目录
     */
    public function getDirectories(){
        return $this->hasMany(Directory::className(), ['directoryId' => 'directoryId'])
                ->viaTable('channel_directory', ['channelId' => 'channelId']);
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
                ],
            ],
        ]);
        return $productProvider;
    }
    /**
     * 根据getDirectories方法构建dataProvider
     * @return \yii\data\ArrayDataProvider
     */
    public function findDirectories(){
        $directoryProvider = new ArrayDataProvider([
            'allModels' => $this->directories,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort'=>[
                'attributes'=>[
                    'directoryName',
                ],
            ]
        ]);
        return $directoryProvider;
    }
    /**
     * 设置在save前要进行的操作
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::beforeSave()
     */
    public function beforeSave($insert){
        if(!empty($this->thumbnail)){
            $this->channelPic = '/images/channels' . '/' . $this->thumbnail->baseName . '.' . $this->thumbnail->extension;
        }
        $this->languageId = $this->languageName;
        return parent::beforeSave($insert);
    }
}