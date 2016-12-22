<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
class RAMSearch extends RAM{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules()
    {
        return [
            [['recordTime', 'utilize', 'free', 'used', 'total', 'buffer', 'cache'], 'safe'],
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
        $query = RAM::find()->where(['server'=>$serverName]);
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
        ->andFilterWhere(['=', 'utilize', $this->utilize])
        ->andFilterWhere(['=', 'free', $this->free])
        ->andFilterWhere(['=', 'used', $this->used])
        ->andFilterWhere(['=', 'total', $this->total])
        ->andFilterWhere(['=', 'buffer', $this->buffer])
        ->andFilterWhere(['=', 'cache', $this->cache]);
        return $dataProvider;
    }
}