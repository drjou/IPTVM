<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class LanguageSearch extends Language{
    /**
     * 设置搜索验证规则，必须为safe
     * {@inheritDoc}
     * @see \app\models\Language::rules()
     */
    public function rules(){
        return [
            [['languageName'], 'safe'],
        ];
    }
    /**
     * 设置每个场景要验证的属性
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
        $query = Language::find();
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
        
        $query->andFilterWhere(['like', 'languageName', $this->languageName]);
        return $dataProvider;
    }
}