<?php
namespace app\models;
use yii\data\ActiveDataProvider;
use yii\base\Model;


class ProcessInfoSearch extends ProcessInfo{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [['server', 'processName', 'status', 'total','user', 'system', 'memory', 'rss', 'readByte', 'writeByte', 'recordTime'], 'safe'],
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
        $query = ProcessInfo::find()
        ->innerJoin('process','process.processName=process_info.processName and process.server=process_info.server');
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
        $query->andFilterWhere(['like', 'process_info.server', $this->server])
        ->andFilterWhere(['like', 'process_info.processName', $this->processName])
        ->andFilterWhere(['=', 'status', $this->status])
        ->andFilterWhere(['=', 'total', $this->total])
        ->andFilterWhere(['=', 'user', $this->user])
        ->andFilterWhere(['=', 'system', $this->system])
        ->andFilterWhere(['=', 'memory', $this->memory])
        ->andFilterWhere(['=', 'rss', $this->rss])
        ->andFilterWhere(['=', 'readByte', $this->readByte])
        ->andFilterWhere(['=', 'writeByte', $this->writeByte])
        ->andFilterWhere(['like', 'recordTime', $this->recordTime]);
        return $dataProvider;
    }
}
