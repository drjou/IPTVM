<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\data\ArrayDataProvider;

class Language extends ActiveRecord{
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'language';
    }
    /**
     * 设置表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            ['languageName', 'required'],
            ['languageName', 'unique'],
        ];
    }
    
    /**
     * 根据languageId获取language信息
     * @param int $languageId
     * @throws NotFoundHttpException
     * @return \app\models\Language|NULL
     */
    public static function findLanguageById($languageId){
        if(($model = self::findOne($languageId)) !== null){
            return $model;
        }else{
            throw new NotFoundHttpException("The language whose languageId is $languageId don't exist, please try the right way to access language");
        }
    }
    /**
     * 获取为该语言类型的所有channels
     * @return ActiveQuery
     */
    public function getChannels(){
        return $this->hasMany(Channel::className(), ['languageId' => 'languageId']);
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
                ],
            ],
        ]);
        return $channelProvider;
    }
}