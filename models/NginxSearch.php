<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
class NginxSearch extends Nginx{
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
        $query = Nginx::find()
        ->orderBy(['recordTime'=>SORT_DESC]);
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
        ->andFilterWhere(['like', 'recordTime', $this->recordTime])
        ->andFilterWhere(['=', 'status', $this->status])
        ->andFilterWhere(['=', 'accept', $this->accept])
        ->andFilterWhere(['=', 'handle', $this->handle])
        ->andFilterWhere(['=', 'request', $this->request])
        ->andFilterWhere(['=', 'active', $this->active])
        ->andFilterWhere(['=', 'readRequest', $this->readRequest])
        ->andFilterWhere(['=', 'writeRequest', $this->writeRequest])
        ->andFilterWhere(['=', 'wait', $this->wait])
        ->andFilterWhere(['=', 'qps', $this->qps])
        ->andFilterWhere(['=', 'responseTime', $this->responseTime]);
        return $dataProvider;
    }
}