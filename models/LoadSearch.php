<?php
namespace app\models;
use yii\data\ActiveDataProvider;
use yii\base\Model;


class LoadSearch extends Load{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [[ 'server', 'load1', 'load5', 'load15', 'processRun', 'processTotal'], 'safe'],
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
     * 检索过滤
     * @param string $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params){
        $query = Load::find()
        ->orderBy(['recordTime'=>SORT_DESC]);
        return $this->searchProvider($query, $params);
    }
    
    public function searchWarning($params){
        $threshold = Threshold::find()->one();
        $query = Load::find()->join('INNER JOIN', 'server', 'server=serverName')
        ->where('load1>='.$threshold->loads)
        ->orderBy(['recordTime'=>SORT_DESC]);
        return $this->searchProvider($query, $params);
    }
    
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
        ->andFilterWhere(['=', 'load1', $this->load1])
        ->andFilterWhere(['=', 'load5', $this->load5])
        ->andFilterWhere(['=', 'load15', $this->load15])
        ->andFilterWhere(['=', 'processRun', $this->processRun])
        ->andFilterWhere(['=', 'processTotal', $this->processTotal]);
        return $dataProvider;
    }
}
