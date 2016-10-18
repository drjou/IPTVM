<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\data\ArrayDataProvider;

class Directory extends ActiveRecord{
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
            [['directoryName', 'ParentName', 'showOrder'], 'required'],
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
     * 获取目录下的channel
     * @return ActiveQuery
     */
    public function getChannels(){
        return $this->hasMany(Channel::className(), ['channelId' => 'channelId'])
                ->viaTable('channel_directory', ['directoryId' => 'directoryId']);
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
}