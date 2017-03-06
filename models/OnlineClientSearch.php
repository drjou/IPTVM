<?php
namespace app\models;
use yii\base\Model;
use yii\data\ActiveDataProvider;

class OnlineClientSearch extends OnlineClient{
    
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['accountId', 'server', 'stream', 'Ip', 'startTime', 'totalTime'], 'safe']
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
     * 过滤
     * @param unknown $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params){
        $query = OnlineClient::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);
        
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'accountId', $this->accountId])
        ->andFilterWhere(['like', 'server', $this->server])
        ->andFilterWhere(['like', 'stream', $this->stream])
        ->andFilterWhere(['like', 'Ip', $this->Ip])
        ->andFilterWhere(['like', 'startTime', $this->startTime])
        ->andFilterWhere(['like', 'totalTime', $this->totalTime]);
        
        return $dataProvider;
    }
}