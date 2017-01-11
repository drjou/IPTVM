<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
class RAMSearch extends RAM{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [['server' ,'recordTime', 'utilize', 'free', 'used', 'total', 'buffer', 'cache'], 'safe'],
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
        $query = RAM::find()
        ->orderBy(['recordTime'=>SORT_DESC]);
        return $this->searchProvider($query, $params);
    }
    /**
     * 超过阈值的数据
     * @param string $params
     */
    public function searchWarning($params){
        $threshold = Threshold::find()->one();
        $query = RAM::find()->join('INNER JOIN', 'server', 'server=serverName')
        ->where('utilize>='.$threshold->memory)
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
        ->andFilterWhere(['like', 'recordTime', $this->recordTime])
        ->andFilterWhere(['=', 'utilize', $this->utilize])
        ->andFilterWhere(['=', 'free', $this->free])
        ->andFilterWhere(['=', 'used', $this->used])
        ->andFilterWhere(['=', 'total', $this->total])
        ->andFilterWhere(['=', 'buffer', $this->buffer])
        ->andFilterWhere(['=', 'cache', $this->cache]);
        return $dataProvider;
    }
}