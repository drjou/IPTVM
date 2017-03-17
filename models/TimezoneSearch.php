<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class TimezoneSearch extends Timezone{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \app\models\Account::rules()
     */
    public function rules(){
        return [
            [['timezone', 'isCurrent', 'status', 'continent', 'country', 'chinese'], 'safe'],
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
        $query = Timezone::find();
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
    
        $query->andFilterWhere(['like', 'timezone', $this->timezone])
        ->andFilterWhere(['=', 'isCurrent', $this->isCurrent])
        ->andFilterWhere(['=', 'status', $this->status])
        ->andFilterWhere(['like', 'continent', $this->continent])
        ->andFilterWhere(['like', 'country', $this->country])
        ->andFilterWhere(['like', 'chinese', $this->chinese]);
        
        return $dataProvider;
    }
}