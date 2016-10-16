<?php
namespace app\models;

use yii\db\ActiveRecord;

class Directory extends ActiveRecord{
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
            [['directoryName', 'ParentId', 'showOrder'], 'required'],
        ];
    }
}