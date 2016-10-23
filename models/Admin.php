<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;

class Admin extends ActiveRecord implements \yii\web\IdentityInterface{
    //修改个人信息时用到
    public $oldPassword;
    //新建admin时重新输入密码
    public $rePassword;
    const SCENARIO_ADD = 'add';
    const SCENARIO_UPDATE = 'update';
    const SCENARIO_PASSWORD = 'password'; 
    /**
     * 设置模型对应的表名
     * @return string
     */
    public static function tableName(){
        return 'administrator';
    }
    /**
     * 设置验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['userName', 'realName', 'oldPassword','password', 'rePassword','email'], 'required'],
            [['userName', 'realName', 'oldPassword','password', 'rePassword', 'email'], 'trim'],
            ['email', 'email'],
            ['userName', 'unique'],
            ['userName', 'string', 'length' => [3, 20]],
            ['realName', 'string', 'length' => [3, 20]],
            ['oldPassword', 'string', 'length' => [6, 20]],
            ['oldPassword', 'validateOldPassword'],
            ['password', 'string', 'length' => [6, 20]],
            ['rePassword', 'string', 'length' => [6, 20]],
            ['rePassword', 'compare', 'compareAttribute' => 'password'],
        ];
    }
    /**
     * 设置不同场景要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return [
            self::SCENARIO_ADD => ['userName', 'realName', 'password', 'rePassword', 'email'],
            self::SCENARIO_UPDATE => ['realName', 'email'],
            self::SCENARIO_PASSWORD => ['oldPassword', 'password', 'rePassword'],
        ];
    }
    
    public function validateOldPassword($attribute, $params){
        $user = Admin::findAdminById(Yii::$app->user->identity->id);
        if(!Yii::$app->getSecurity()->validatePassword($this->oldPassword, $user->password)){
            $this->addError($attribute, "Old Password is wrong.");
        }
    }
    
    /**
     * 根据id去获取到admin对象信息
     * @param int $id
     * @throws NotFoundHttpException
     * @return \app\models\Admin|NULL
     */
    public static function findAdminById($id){
        if(($model = Admin::findOne($id)) !== null){
            return $model;
        }else {
            throw new NotFoundHttpException("The administrator whose id is $id doesn't exist, please try the right way to access administrator.");
        }
    }
    
    /**
     * 在save前的操作
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::beforeSave()
     */
    public function beforeSave($insert){
        if($this->scenario == self::SCENARIO_ADD){//新创建administrator在存入数据库前做如下操作
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
            $this->type = 0;
            $now = date('Y-m-d H:i:s', time());
            $this->lastLoginTime = $now;
            $this->createTime = $now;
            $this->authKey = $this->randStr();
        }
        if($this->scenario == self::SCENARIO_PASSWORD){
            $this->password = Yii::$app->getSecurity()->generatePasswordHash($this->password);
        }
        return parent::beforeSave($insert);
    }
    
    private function randStr($len = 6) {
        $str = "";
        $data = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJIKLMNOPQRSTUVWXYZ";
        for($i = 0 ; $i < $len; $i++){
            $num = $data[rand(0,strlen($data)-1)];
            $str .=  $num;
        }
        return $str;
    }
    
    /**
     * 根据给定的ID查询身份
     * @param int $id 身份ID
     * @return \app\models\User|NULL 根据ID匹配的对象
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    
    /**
     * 根据token查询身份（数据库中未存储，此方法未用到）
     * @param string $token 被查询的token
     * @param unknown $type
     * @return \app\models\User|NULL 通过token得到的身份对象
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }
    
    /**
     * 根据用户名查用户
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['userName' => $username]);
    }
    
    /**
     * 当前用户ID
     * {@inheritDoc}
     * @see \yii\web\IdentityInterface::getId()
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * 当前用户的（cookie）认证密钥
     * {@inheritDoc}
     * @see \yii\web\IdentityInterface::getAuthKey()
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }
    
    /**
     * 验证cookie中密钥与持有的密钥是否一致
     * {@inheritDoc}
     * @see \yii\web\IdentityInterface::validateAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }
    
    /**
     * 验证输入的密码是否正确
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->getSecurity()->validatePassword($password, $this->password);
    }
}