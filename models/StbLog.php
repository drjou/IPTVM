<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

class StbLog extends ActiveRecord{
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'stb_log';
    }
    /**
     * 设置表单中 显示的对应字段名称
     * {@inheritDoc}
     * @see \yii\base\Model::attributeLabels()
     */
    public function attributeLabels(){
        return [
            'prefix' => 'STB Account',
            'log_time' => 'Time'
        ];
    }
    
    /**
     * 获取日志信息
     * @param int $id
     * @throws NotFoundHttpException
     * @return \app\models\AdminLog|NULL
     */
    public static function findStbLogById($id){
        if(($model = self::findOne($id)) !== null){
            return $model;
        }else{
            throw new NotFoundHttpException("the stb log whose id is $id don't exist, please try the right way to access.");
        }
    }
}