<?php
namespace app\models;
use yii\data\ActiveDataProvider;
use yii\base\Model;


class CPUSearch extends CPU{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [['server','ncpu', 'utilize', 'user', 'system', 'wait', 'hardIrq', 'softIrq', 'nice', 'steal', 'guest', 'idle'], 'safe'],
        ];
    }
    /**
     * 每个场景要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios()
    {
        return Model::scenarios();
    }
    /**
     * 所有数据
     * @param string $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params){
        $query = CPU::find()
        ->orderBy(['recordTime'=>SORT_DESC]);
        return $this->searchProvider($query, $params);
    }
    /**
     * 所有高于阈值的数据
     * @param string $params
     * @return \yii\data\ActiveDataProvider
     */
    public function searchWarning($params){
        $threshold = Threshold::find()->one();
        $query = CPU::find()->join('INNER JOIN', 'server', 'server=serverName')
        ->where('utilize>='.$threshold->cpu)
        ->orderBy(['recordTime'=>SORT_DESC]);
        return $this->searchProvider($query, $params);
    }
    /**
     * 检索过滤
     * @param ActiveQuery $query
     * @param string $params
     */
    private function searchProvider($query, $params){
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
        $query->andFilterWhere(['=', 'server', $this->server])
        ->andFilterWhere(['=', 'ncpu', $this->ncpu])
        ->andFilterWhere(['=', 'utilize', $this->utilize])
        ->andFilterWhere(['=', 'user', $this->user])
        ->andFilterWhere(['=', 'system', $this->system])
        ->andFilterWhere(['=', 'wait', $this->wait])
        ->andFilterWhere(['=', 'hardIrq', $this->hardIrq])
        ->andFilterWhere(['=', 'softIrq', $this->softIrq])
        ->andFilterWhere(['=', 'nice', $this->nice])
        ->andFilterWhere(['=', 'steal', $this->steal])
        ->andFilterWhere(['=', 'guest', $this->guest])
        ->andFilterWhere(['=', 'idle', $this->idle]);
        return $dataProvider;
    }
}
