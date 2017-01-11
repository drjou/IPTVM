<?php
namespace app\models;
use yii\data\ActiveDataProvider;
use yii\base\Model;


class DiskSearch extends Disk{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [['server', 'recordTime', 'freePercent', 'free', 'used', 'total'], 'safe'],
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
        $query = Disk::find()
        ->orderBy(['recordTime'=>SORT_DESC]);
        return $this->searchProvider($query, $params);
    }
    /**
     * 所有低于阈值的数据
     * @param string $params
     * @return \yii\data\ActiveDataProvider
     */
    public function searchWarning($params){
        $threshold = Threshold::find()->one();
        $query = Disk::find()->join('INNER JOIN', 'server', 'server=serverName')
        ->where('freePercent<='.$threshold->disk)
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
        $query->andFilterWhere(['like', 'recordTime', $this->recordTime])
        ->andFilterWhere(['=', 'freePercent', $this->freePercent])
        ->andFilterWhere(['=', 'free', $this->free])
        ->andFilterWhere(['=', 'used', $this->used])
        ->andFilterWhere(['=', 'total', $this->total]);
        return $dataProvider;
    }
}
