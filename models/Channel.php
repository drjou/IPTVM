<?php
namespace app\models;

use yii\db\ActiveRecord;

class Channel extends ActiveRecord{
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
            [['channelName', 'channelIp', 'channelPic', 'channelUrl', 'urlType', 'channelType', 'languageId'], 'required'],
        ];
    }
}