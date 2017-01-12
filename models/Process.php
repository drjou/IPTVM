<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Process extends ActiveRecord{
    public $importFile;
    const SCENARIO_SAVE = 'save';
    const SCENARIO_IMPORT = 'import';
    /**
     * 设置模型对应表名
     * @return string
     */
    public static function tableName(){
        return 'process';
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
                'value' => new Expression('NOW()')
            ]
        ];
    }
    /**
     * 设置验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['processName', 'source', 'server'], 'required'],
            ['importFile', 'file', 'skipOnEmpty' => false, 'mimeTypes' => ['application/xml', 'text/xml'], 'extensions' => ['xml'], 'maxSize' => 50*1024*1024],
            [['processName', 'source', 'server'], 'trim'],
            [['processName', 'source', 'server'], 'string', 'length' => [1, 20]],
            ['processName', 'validateProcessName']
        ];
    }
    
    /**
     * 设置不同场景下的验证属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return [
            self::SCENARIO_SAVE => ['processName', 'source', 'server'],
            self::SCENARIO_IMPORT => ['importFile'],
        ];
    }
    
    /**
     * 返回所有服务器的所有进程信息
     */
    public function getProcesses($startTime, $endTime){
        return $this->hasMany(ProcessInfo::className(), ['processName' => 'processName','server' => 'server'])
        ->where('recordTime between "'.$startTime.'" and "'.$endTime.'"');
    }
    /**
     * 通过主键寻找process
     * @param string $processName
     * @param string $server
     * @throws NotFoundHttpException
     */
    public static function findProcessByKey($processName, $server){
        if(($model = self::findOne(['processName' => $processName, 'server' => $server])) !== null){
            return $model;
        }else{
            throw new NotFoundHttpException("The stream $processName on $server doesn't exist, please try the right way to access stream.");
        }
    }
    
    /**
     * 验证服务器上是否有该进程
     * @param string $attribute
     * @param string $params
     */
    public function validateProcessName($attribute, $params)
    {
        $processes = $this->find()->asArray()->all();
        for($i=0;$i<count($processes);$i++){
            if($this->$attribute==$processes[$i][$attribute] && $this->server==$processes[$i]['server']){
                $this->addError($attribute, "The process name has already existed on $this->server");
            }
        }
    }
}