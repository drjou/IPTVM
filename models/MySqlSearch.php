<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
class MySqlSearch extends MySql{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [['server', 'status'], 'safe'],
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
        $query = MySql::find();
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
        //'server', 'recordTime', 'status', 'totalConnections', 'activeConnections', 'qps', 'tps', 'receiveTraffic', 'sendTraffic'
        $query->andFilterWhere(['=', 'server', $this->server])
        ->andFilterWhere(['=', 'status', $this->status]);
        return $dataProvider;
    }
}