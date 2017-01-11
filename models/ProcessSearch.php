<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
class ProcessSearch extends Process{
    
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['processName', 'server'], 'safe']
        ];
    }
    /**
     * 每个场景要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return Model::scenarios();
    }
    
    /**
     * 检索过滤
     * @param string $params
     */
    public function search($params){
        $query = Process::find()
        ->orderBy('server');
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
        
        $query->andFilterWhere(['like', 'processName', $this->processName])
        ->andFilterWhere(['=', 'server', $this->server]);
        
        return $dataProvider;
    }
}