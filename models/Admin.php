<?php
namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Admin extends ActiveRecord implements \yii\web\IdentityInterface{
    
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
            [['userName', 'password'], 'required'],
            ['userName', 'string', 'min' => 3, 'max' => 20],
            ['password', 'string', 'min' => 6, 'max' => 20],
        ];
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