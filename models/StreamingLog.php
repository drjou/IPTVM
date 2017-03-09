<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
class StreamingLog extends ActiveRecord{
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'streaming_log';
    }
    
    /**
     * 用id获取log
     * @param string $id
     * @throws NotFoundHttpException
     * @return boolean
     */
    public static function findLogById($id){
        if(($model = self::findOne($id)) !== null){
            return $model;
        }else{
            throw new NotFoundHttpException("The log whose id is $id doesn't exist, please try the right way to access server.");
        }
    }
}