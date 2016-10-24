<?php
namespace app\models;

use yii\data\ActiveDataProvider;

class StbLogSearch extends StbLog{
    /**
     * 设置搜索验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['prefix', 'log_time', 'message'], 'safe'],
        ];
    }
    /**
     * 搜索
     * @param array $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params){
        $query = StbLog::find()->where(['category' => 'stb']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        $query->andFilterWhere(['like', 'prefix', $this->prefix])
        ->andFilterWhere(['like', 'log_time', $this->log_time])
        ->andFilterWhere(['like', 'message', $this->message]);
        return $dataProvider;
    }
}