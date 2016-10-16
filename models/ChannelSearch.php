<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ChannelSearch extends Channel{
    /**
     * 表单验证规则
     * {@inheritDoc}
     * @see \app\models\Channel::rules()
     */
    public function rules(){
        return [
            [['channelName', 'channelIp', 'channelPic', 'channelUrl', 'urlType', 'channelType', 'languageId'], 'safe'],
        ];
    }
    /**
     * 设置每个情景下需要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return Model::scenarios();
    }
    
    public function search($params){
        $query = Channel::find();
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
        
        $query->andFilterWhere(['like', 'channelName', $this->channelName])
        ->andFilterWhere(['like', 'channelIp', $this->channelIp])
        ->andFilterWhere(['like', 'channelPic', $this->channelPic])
        ->andFilterWhere(['like', 'channelUrl', $this->channelUrl])
        ->andFilterWhere(['like', 'urlType', $this->urlType])
        ->andFilterWhere(['like', 'channelType', $this->channelType])
        ->andFilterWhere(['=', 'languageId', $this->languageId]);
        return $dataProvider;
    }
}