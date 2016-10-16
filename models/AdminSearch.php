<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class AdminSearch extends Admin{
    /**
     * 设置搜索的规则，能进行搜索的属性必须safe
     * {@inheritDoc}
     * @see \app\models\Admin::rules()
     */
    public function rules(){
        return [
            [['userName', 'realName', 'email', 'type', 'lastLoginTime', 'createTime'], 'safe'],
        ];
    }
    /**
     * 设置不同场景下要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return Model::scenarios();
    }
    
    public function search($params){
        $query = Admin::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'userName', $this->userName])
        ->andFilterWhere(['like', 'realName', $this->realName])
        ->andFilterWhere(['like', 'email', $this->email])
        ->andFilterWhere(['=', 'type', $this->type])
        ->andFilterWhere(['like', 'lastLoginTime', $this->lastLoginTime])
        ->andFilterWhere(['like', 'createTime', $this->createTime]);
        return $dataProvider;
    }
}