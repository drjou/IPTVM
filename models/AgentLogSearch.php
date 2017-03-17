<?php
namespace app\models;

use yii\data\ActiveDataProvider;
use yii\base\Model;
class AgentLogSearch extends AgentLog{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['id', 'moduleName', 'server', 'status', 'detail'], 'safe']
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
        $query = AgentLog::find();
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
        //'id', 'moduleName', 'server', 'status', 'detail', 'recordTime'
        $query->andFilterWhere(['like', 'id', $this->id])
        ->andFilterWhere(['like', 'moduleName', $this->moduleName])
        ->andFilterWhere(['like', 'server', $this->server])
        ->andFilterWhere(['=', 'status', $this->status])
        ->andFilterWhere(['like', 'detail', $this->detail]);
        return $dataProvider;
    }
}