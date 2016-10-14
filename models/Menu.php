<?php
namespace app\models;

use yii\db\ActiveRecord;

class Menu extends ActiveRecord{
    /**
     * 设置模型对应的表名
     * @return string
     */
    public static function tableName(){
        return 'menu';
    }
    
    /**
     * 按级获取所有的菜单
     */
    public function getAllMenus(){
        $first_level = $this->find()->where(['showLevel'=>1])->orderBy(['showOrder'=>SORT_ASC])->all();
        foreach ($first_level as $fl){
            $second_level = $this->find()->where(['showLevel'=>2, 'parentId'=>$fl->id])->orderBy(['showOrder'=>SORT_ASC])->all();
            if(!empty($second_level)){
                foreach ($second_level as $sl){
                    $third_level = $this->find()->where(['showLevel'=>3, 'parentId'=>$sl->id])->orderBy(['showOrder'=>SORT_ASC])->all();
                    if(!empty($third_level)){
                        $sl['children'] = $third_level;
                    }
                }
                $fl['children'] = $second_level;
            }
        }
        return $first_level;
    }
}