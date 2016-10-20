<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class Directory extends ActiveRecord{
    //父目录名称
    public $parentName;
    /**
     * 设置模型对应的表名
     * @return string
     */
    public static function tableName(){
        return 'directory';
    }
    /**
     * 设置表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['directoryName', 'showOrder'], 'required'],
            ['directoryName', 'unique'],
            ['directoryName', 'trim'],
            ['showOrder', 'integer'],
            ['channels', 'safe'],
        ];
    }
    /**
     * 根据directoryId获取目录信息
     * @param int $directoryId
     * @return \app\models\Directory|NULL
     */
    public static function findDirectoryById($directoryId){
        return self::findOne($directoryId);
    }
    
    /**
     * 获取父目录
     * @return ActiveQuery
     */
    public function getParentDirectory(){
        return $this->hasOne(Directory::className(), ['directoryId' => 'parentId'])
                ->from(Directory::tableName().' parentDirectory');
    }
    /**
     * 获取该目录的所有子目录
     * @return ActiveQuery
     */
    public function getChildrenDirectories(){
        return $this->hasMany(Directory::className(), ['parentId' => 'directoryId'])
                ->from(Directory::tableName().' childrenDirectories');
    }
    /**
     * 设置channels为从表单获取的值
     * @param array $channels
     */
    public function setChannels($channels){
        $this->channels = $channels;
    }
    
    /**
     * 获取目录下的channel
     * @return ActiveQuery
     */
    public function getChannels(){
        return $this->hasMany(Channel::className(), ['channelId' => 'channelId'])
                ->viaTable('channel_directory', ['directoryId' => 'directoryId']);
    }
    /**
     * 根据getChildrenDirectories方法构建dataProvider
     * @return \yii\data\ArrayDataProvider
     */
    public function findChildrenDirectories(){
        $childrenProvider = new ArrayDataProvider([
            'allModels' => $this->childrenDirectories,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'directoryName',
                    'showOrder',
                ],
            ],
        ]);
        return $childrenProvider;
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
                ],
            ],
        ]);
        return $channelProvider;
    }
    /**
     * 获取所有目录信息
     */
    public function getAllDirectories(){
        $directories = self::find()->select(['directoryId', 'directoryName'])->all();
        return ArrayHelper::map($directories, 'directoryId', 'directoryName');
    }
    /**
     * 在保存之前对parentId赋值
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::beforeSave()
     */
    public function beforeSave($insert){
        if(empty($this->parentName)){
            $this->parentId = null;
        }else{
            $this->parentId = $this->parentName;
        }
        return parent::beforeSave($insert);
    }
}