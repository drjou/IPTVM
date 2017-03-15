<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
class MysqlInfoSearch extends MysqlInfo{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [['status', 'totalConnections', 'activeConnections', 'qps', 'tps', 'receiveTraffic', 'sendTraffic', 'server'], 'safe'],
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
     * 检索过滤条件
     * @param string $params
     */
    public function search($params){
        $query = MysqlInfo::find();
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
        //'status', 'totalConnections', 'activeConnections', 'qps', 'tps', 'receiveTraffic', 'sendTraffic', 'server', 'recordTime'
        $query->andFilterWhere(['=', 'server', $this->server])
        ->andFilterWhere(['=', 'status', $this->status])
        ->andFilterWhere(['like', 'totalConnections', $this->totalConnections])
        ->andFilterWhere(['like', 'activeConnections', $this->activeConnections])
        ->andFilterWhere(['like', 'qps', $this->qps])
        ->andFilterWhere(['like', 'tps', $this->tps])
        ->andFilterWhere(['like', 'receiveTraffic', $this->receiveTraffic])
        ->andFilterWhere(['like', 'sendTraffic', $this->sendTraffic]);
        return $dataProvider;
    }
}