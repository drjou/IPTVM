<?php
namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
class MenuSearch extends Menu{
    /**
     * 设置验证规则，只有safe的元素才能进行搜索
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['menuName', 'parentName', 'route', 'showLevel'], 'safe'],
            [['menuName', 'parentName', 'route'], 'string'],
        ];
    }
    /**
     * 具体场景要验证的属性
     * {@inheritDoc}
     * @see \yii\base\Model::scenarios()
     */
    public function scenarios(){
        return Model::scenarios();
    }
    
    public function search($params){
        $query = Menu::find()->joinWith(['parentMenu']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination'=> [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'menuName',
                    'parentName' => [
                        'asc' => ['parentMenu.menuName' => SORT_ASC],
                        'desc' => ['parentMenu.menuName' => SORT_DESC],
                    ],
                    'route',
                    'showLevel',
                ],
            ],
        ]);
        
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'menu.menuName', $this->menuName])
        ->andFilterWhere(['like', 'parentMenu.menuName', $this->parentName])
        ->andFilterWhere(['like', 'menu.route', $this->route])
        ->andFilterWhere(['=', 'menu.showLevel', $this->showLevel]);
        
        return $dataProvider;
    }
}