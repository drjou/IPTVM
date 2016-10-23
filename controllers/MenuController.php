<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\MenuSearch;
use app\models\Menu;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\HttpException;

class MenuController extends Controller{
    
    /**
     * 设置访问权限
     * {@inheritDoc}
     * @see \yii\base\Component::behaviors()
     */
    public function behaviors(){
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'view' => ['get'],
                    'create' => ['get', 'post'],
                    'update' => ['get', 'post'],
                    'delete' => ['get'],
                ]
            ],
        ];
    }
    
    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    /**
     * Index Action
     * 显示所有的菜单
     * @return string
     */
    public function actionIndex(){
        $searchModel = new MenuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        return $this->render('index',[
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    /**
     * View Action
     * 查看指定菜单信息
     * @param int $id
     */
    public function actionView($id){
        $model = Menu::findMenuById($id);
        return $this->render('view',[
            'model' => $model,
        ]);
    }
    
    /**
     * Create Action
     * 创建一个新的菜单
     * @return string
     */
    public function actionCreate(){
        $model = new Menu();
        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $menu_items = $model->getMenuItems();
        return $this->render('create',[
            'model' => $model,
            'menu_items' => $menu_items,
        ]);
    }
    /**
     * Update Action
     * 修改菜单的信息
     * @param int $id
     * @return string
     */
    public function actionUpdate($id){
        $model = Menu::findMenuById($id);
        if($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()){
            return $this->redirect(['view', 'id' => $model->id]);
        }
        $menu_items = array_diff($model->getMenuItems(), $model->childrenMenus, [$model->id => $model->menuName]);
        $model->parentName = $model->parentId;
        return $this->render('update',[
            'model' => $model,
            'menu_items' => $menu_items,
        ]);
    }
    /**
     * 删除指定的菜单
     * @param int $id
     */
    public function actionDelete($id){
        $model = Menu::findMenuById($id);
        if(!empty($model->childrenMenus)){
            throw new HttpException(500, "you can't delete the menu that has sub menus.");
        }
        $model->delete();
        return $this->redirect(['index']);
    }
}