<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
class NginxInfoSearch extends NginxInfo{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [['server', 'recordTime', 'status', 'accept', 'handle', 'request', 'active', 'readRequest', 'writeRequest', 'wait', 'qps', 'responseTime'], 'safe'],
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
        $query = NginxInfo::find();
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
        //'server', 'recordTime', 'status', 'accept', 'handle', 'request', 'active', 'readRequest', 'writeRequest', 'wait', 'qps', 'responseTime'
        $query->andFilterWhere(['=', 'server', $this->server])
        ->andFilterWhere(['=', 'status', $this->status])
        ->andFilterWhere(['like', 'accept', $this->accept])
        ->andFilterWhere(['like', 'handle', $this->handle])
        ->andFilterWhere(['like', 'request', $this->request])
        ->andFilterWhere(['like', 'active', $this->active])
        ->andFilterWhere(['like', 'readRequest', $this->readRequest])
        ->andFilterWhere(['like', 'writeRequest', $this->writeRequest])
        ->andFilterWhere(['like', 'wait', $this->wait])
        ->andFilterWhere(['like', 'qps', $this->qps])
        ->andFilterWhere(['like', 'responseTime', $this->responseTime])
        ->andFilterWhere(['like', 'recordTime', $this->recordTime]);
        return $dataProvider;
    }
}