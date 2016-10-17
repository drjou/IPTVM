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
            [['channelName', 'channelIp', 'channelPic', 'channelUrl', 'urlType', 'channelType', 'languageName'], 'safe'],
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
    /**
     * 检索过滤
     * @param string $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params){
        $query = Channel::find()->joinWith(['language']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'channelName',
                    'channelIp',
                    'channelPic',
                    'channelUrl',
                    'urlType',
                    'channelType',
                    'languageName' => [
                        'asc' => ['language.languageName' => SORT_ASC],
                        'desc' => ['language.languageName' => SORT_DESC],
                    ]
                ],
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
        ->andFilterWhere(['like', 'language.languageName', $this->languageName]);
        return $dataProvider;
    }
}