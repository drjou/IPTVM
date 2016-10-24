<?php
namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use yii\data\ArrayDataProvider;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

class Menu extends ActiveRecord{
    //父menu名称
    public $parentName;
    //在分级获取菜单是需要（左侧菜单栏的显示）
    public $children;
    /**
     * 设置模型对应的表名
     * @return string
     */
    public static function tableName(){
        return 'menu';
    }
    
    /**
     * 自动更新创建时间和修改时间
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors(){
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createTime',
                'updatedAtAttribute' => 'updateTime',
                'value' => new Expression('NOW()'),
            ],
        ];
    }
    
    /**
     * 设置验证规则
     * {@inheritDoc}
     * @see \yii\base\Model::rules()
     */
    public function rules(){
        return [
            [['menuName', 'route', 'showLevel', 'showOrder', 'icon'], 'required'],
            ['parentName', 'safe'],
            ['route', 'match', 'pattern'=>'/^(\/\S+\/\S+|javascript:void\(0\))$/', 'message' => 'please input with the prompt'],
            ['showLevel', 'integer', 'min' => 1, 'max' => 3, 'integerOnly' => true],
            ['showOrder', 'integer'],
        ];
    }
    /**
     * 根据id获取menu信息
     * @param int $id
     * @throws NotFoundHttpException
     * @return \app\models\Menu|NULL
     */
    public static function findMenuById($id){
        if(($model = self::findOne($id)) !== null){
            return $model;
        }else {
            throw new NotFoundHttpException("The menu whose id is $id don't exist, please try the right way to access menu.");
        }
    }
    
    /**
     * 与自身进行关联，查询父级的名称
     */
    public function getParentMenu(){
        return $this->hasOne(Menu::className(), ['id' => 'parentId'])
                ->from(Menu::tableName().' parentMenu');
    }
    /**
     * 获取menu下的所有子menu
     */
    public function getChildrenMenus(){
        return $this->hasMany(Menu::className(), ['parentId' => 'id'])
                ->from(Menu::tableName().' childrenMenus');
    }
    
    public function findChildrenMenus(){
        $childrenProvider = new ArrayDataProvider([
            'allModels' => $this->childrenMenus,
            'pagination' => [
                'pageSize' => 10,
            ],
            'sort' => [
                'attributes' => [
                    'menuName',
                    'showLevel',
                    'showOrder',
                ],
            ],
        ]);
        return $childrenProvider;
    }
    
    /**
     * 获取menu表中所有的非第三级菜单名称，新增修改时用到
     */
    public function getMenuItems(){
        $items = $this->find()->select(['id','menuName'])->where('showLevel in (1,2)')->all();
        return ArrayHelper::map($items, 'id', 'menuName');
    }
    /**
     * 进行赋值操作
     * {@inheritDoc}
     * @see \yii\db\BaseActiveRecord::beforeSave($insert)
     */
    public function beforeSave($insert){
        if(empty($this->parentName)){
            $this->parentId = null;
        }else{
            $this->parentId = $this->parentName;
        }
        return parent::beforeSave($insert);
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