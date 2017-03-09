<?php
namespace app\models;

use yii\data\ActiveDataProvider;
use yii\base\Model;
class StreamAccessLogSearch extends StreamAccessLog{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['id', 'accountId', 'server', 'stream', 'Ip', 'startTime', 'endTime', 'totalTime'], 'safe']
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
        $query = StreamAccessLog::find();
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
        //'id', 'accountId', 'server', 'stream', 'Ip', 'startTime', 'endTime', 'totalTime'
        $query->andFilterWhere(['like', 'id', $this->id])
        ->andFilterWhere(['like', 'accountId', $this->accountId])
        ->andFilterWhere(['like', 'server', $this->server])
        ->andFilterWhere(['like', 'stream', $this->stream])
        ->andFilterWhere(['like', 'Ip', $this->Ip])
        ->andFilterWhere(['like', 'startTime', $this->startTime])
        ->andFilterWhere(['like', 'endTime', $this->endTime])
        ->andFilterWhere(['like', 'totalTime', $this->totalTime]);
        return $dataProvider;
    }
}