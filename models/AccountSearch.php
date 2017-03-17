<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class AccountSearch extends Account{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \app\models\Account::rules()
     */
    public function rules(){
        return [
            [['accountId', 'state', 'enable'], 'safe'],
        ];
    }
    /**
     * 每个场景要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return Model::scenarios();
    }
    /**
     * 检索过滤
     * @param string $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params){
        $query = Account::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'accountId', $this->accountId])
        ->andFilterWhere(['=', 'state', $this->state])
        ->andFilterWhere(['=', 'enable', $this->enable]);
        return $dataProvider;
    }
}