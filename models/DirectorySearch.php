<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

class DirectorySearch extends Directory{
    /**
     * 搜索验证规则，所有属性必须safe
     * {@inheritDoc}
     * @see \app\models\Directory::rules()
     */
    public function rules(){
        return [
            [['directoryName', 'parentId', 'showOrder'], 'safe'],
        ];
    }
    /**
     * 设置不同场景下要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return Model::scenarios();
    }
    
    public function search($params){
        $query = Directory::find();
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
        
        $query->andFilterWhere(['like', 'directoryName', $this->directoryName])
        ->andFilterWhere(['=', 'parentId', $this->parentId])
        ->andFilterWhere(['=', 'showOrder', $this->showOrder]);
        return $dataProvider;        
    }
}