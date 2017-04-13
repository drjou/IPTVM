<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
class Nginx extends ActiveRecord{
    const SCENARIO_CHANGE_STATUS = 'changeStatus';
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'nginx';
    }
    /**
     * 设置不同场景下的验证属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return [
            self::SCENARIO_CHANGE_STATUS => ['status']
        ];
    }
    /**
     * 获取Nginx所在服务器的信息
     * @return ActiveQuery
     */
    public function getServerInfo(){
        return $this->hasOne(Server::className(), ['serverName' => 'server']);
    }
    
    public static function findNginxByName($serverName){
        if(($model = self::findOne($serverName)) !== null){
            return $model;
        }else{
            throw new NotFoundHttpException("The server whose serverName is $serverName doesn't exist, please try the right way to access server.");
        }
    }
}