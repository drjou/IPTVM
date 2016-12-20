<?php
namespace app\models;
use yii\data\ActiveDataProvider;
use yii\base\Model;


class CPUSearch extends CPU{
    public function rules()
    {
        return [
            [['recordTime', 'utilize', 'user', 'system', 'wait', 'hardIrq', 'softIrq', 'nice', 'steal', 'guest'], 'safe'],
        ];
    }
    
    public function scenarios()
    {
        return Model::scenarios();
    }
    public function search($params){
        $query = CPU::find();
        $dataProvider  = new ActiveDataProvider([
           'query' => $query,
           'pagination' => [
               'pageSize' => 10
           ]
        ]);
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'recordTime', $this->recordTime])
        ->andFilterWhere(['=', 'ncpu', $this->ncpu])
        ->andFilterWhere(['=', 'utilize', $this->utilize])
        ->andFilterWhere(['=', 'user', $this->user])
        ->andFilterWhere(['=', 'system', $this->system])
        ->andFilterWhere(['=', 'wait', $this->wait])
        ->andFilterWhere(['=', 'hardIrq', $this->hardIrq])
        ->andFilterWhere(['=', 'softIrq', $this->softIrq])
        ->andFilterWhere(['=', 'nice', $this->nice])
        ->andFilterWhere(['=', 'steal', $this->steal])
        ->andFilterWhere(['=', 'guest', $this->guest]);
        return $dataProvider;
    }
}
