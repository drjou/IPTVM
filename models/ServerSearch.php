<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
class ServerSearch extends Server{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \app\models\Server::rules()
     */
    public function rules(){
        return [
            [['serverName', 'serverIp', 'state', 'operatingSystem'], 'safe']
        ];
    }
    /**
     * 每个场景要验证的属性
     * {@inheritDoc}
     * @see \app\models\Model::scenarios()
     */
    public function scenarios(){
        return Model::scenarios();
    }
    /**
     * 检索过滤
     * @param array $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params){
        $query = Server::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10
            ]
        ]);
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'serverName', $this->serverName])
        ->andFilterWhere(['like', 'serverIp', $this->serverIp])
        ->andFilterWhere(['=', 'state', $this->state])
        ->andFilterWhere(['like', 'operatingSystem', $this->operatingSystem]);
        
        return $dataProvider;
    }
}