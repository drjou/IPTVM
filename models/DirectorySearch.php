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
            [['directoryName', 'parentName', 'showOrder'], 'safe'],
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
    /**
     * 检索过滤
     * @param string $params
     * @return \yii\data\ActiveDataProvider
     */
    public function search($params){
        $query = Directory::find()->joinWith(['parentDirectory']);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'directoryName',
                    'parentName' => [
                        'asc' => ['parentDirectory.directoryName' => SORT_ASC],
                        'desc' => ['parentDirectory.directoryName' => SORT_DESC],
                    ],
                    'showOrder',
                    'createTime',
                ],
            ],
        ]);
        $this->load($params);
        if(!$this->validate()){
            return $dataProvider;
        }
        
        $query->andFilterWhere(['like', 'directory.directoryName', $this->directoryName])
        ->andFilterWhere(['like', 'parentDirectory.directoryName', $this->parentName])
        ->andFilterWhere(['=', 'directory.showOrder', $this->showOrder]);
        return $dataProvider;        
    }
}