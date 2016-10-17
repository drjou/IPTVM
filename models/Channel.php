<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ArrayDataProvider;

class Channel extends ActiveRecord{
    //语言名
    public $languageName;
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'channel';
    }
    /**
     * 设置表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['channelName', 'channelIp', 'channelPic', 'channelUrl', 'urlType', 'channelType', 'languageName'], 'required'],
        ];
    }
    /**
     * 根据channelId获取channel信息
     * @param int $channelId
     * @return \app\models\Channel|NULL
     */
    public static function findChannelById($channelId){
        return self::findOne($channelId);
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
                ]
            ]
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
                ]
            ]
        ]);
        return $directoryProvider;
    }
}