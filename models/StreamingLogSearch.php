<?php
namespace app\models;

use yii\data\ActiveDataProvider;
use yii\base\Model;
class StreamingLogSearch extends StreamingLog{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['id', 'streamName', 'server', 'status', 'detail', 'recordTime'], 'safe']
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
        $query = StreamingLog::find();
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
        //'id', 'streamName', 'server', 'status', 'detail', 'recordTime'
        $query->andFilterWhere(['like', 'id', $this->id])
        ->andFilterWhere(['like', 'streamName', $this->streamName])
        ->andFilterWhere(['like', 'server', $this->server])
        ->andFilterWhere(['=', 'status', $this->status])
        ->andFilterWhere(['like', 'detail', $this->detail])
        ->andFilterWhere(['like', 'recordTime', $this->recordTime]);
        return $dataProvider;
    }
}