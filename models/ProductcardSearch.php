<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class ProductcardSearch extends Productcard{
    /**
     * 设置搜索验证规则，所有属性必须safe才能进行搜索
     * {@inheritDoc}
     * @see \app\models\Productcard::rules()
     */
    public function rules(){
        return [
            [['cardNumber', 'cardValue', 'productId', 'cardState', 'useDate', 'accountId'], 'safe'],
        ];
    }
    /**
     * 设置对应场景验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return Model::scenarios();
    }
    
    public function search($params){
        $query = Productcard::find();
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
        
        $query->andFilterWhere(['like', 'cardNumber', $this->cardNumber])
        ->andFilterWhere(['like', 'cardValue', $this->cardValue])
        ->andFilterWhere(['=', 'productId', $this->productId])
        ->andFilterWhere(['=', 'cardState', $this->cardState])
        ->andFilterWhere(['like', 'useDate', $this->useDate])
        ->andFilterWhere(['=', 'accountId', $this->accountId]);
        return $dataProvider;
        
    }
}