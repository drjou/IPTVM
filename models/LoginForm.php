<?php

namespace app\models;

use Yii;
use yii\base\Model;

/**
 * LoginForm is the model behind the login form.
 *
 */
class LoginForm extends Model
{
    public $userName;
    public $password;
    public $captcha;
    public $rememberMe = true;

    private $_user = false;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['userName', 'password', 'captcha'], 'required'],
            // the length of userName must between 3 and 20
            ['userName', 'string', 'min' => 3, 'max' => 20],
            // the length of password must between 6 and 20
            ['password', 'string', 'min' => 6, 'max' => 20],
            // verify the captcha
            ['captcha', 'captcha', 'captchaAction' => 'admin/captcha'],
            // drop the space of the attributes
            [['userName', 'password', 'captcha'], 'trim'],
            // rememberMe must be a boolean value
            ['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            ['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        if ($this->validate()) {
            $user = $this->getUser();
            $state = Yii::$app->user->login($user, $this->rememberMe ? 3600*24*30 : 0);
            if($state){
                //更新最后一次登录时间
                $user->lastLoginTime = date('Y-m-d H:i:s',time());
                $user->scenario = Admin::SCENARIO_UPDATE;
                $user->save();
            }
            return $state;
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = Admin::findByUsername($this->userName);
        }
        return $this->_user;
    }
}
