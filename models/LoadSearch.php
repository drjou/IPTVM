<?php
namespace app\models;
use yii\data\ActiveDataProvider;
use yii\base\Model;


class LoadSearch extends Load{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [['recordTime', 'load1', 'load5', 'load15', 'processRun', 'processTotal'], 'safe'],
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
    public function search($params, $serverName){
        $query = Load::find()->where(['server'=>$serverName]);
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
        //'load1', 'load5', 'load15', 'processRun', 'processTotal'
        $query->andFilterWhere(['like', 'recordTime', $this->recordTime])
        ->andFilterWhere(['=', 'load1', $this->load1])
        ->andFilterWhere(['=', 'load5', $this->load5])
        ->andFilterWhere(['=', 'load15', $this->load15])
        ->andFilterWhere(['=', 'processRun', $this->processRun])
        ->andFilterWhere(['=', 'processTotal', $this->processTotal]);
        return $dataProvider;
    }
}
