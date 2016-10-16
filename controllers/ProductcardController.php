<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\ProductcardSearch;
use app\models\Productcard;

class ProductcardController extends Controller{
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
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                    'delete-all' => ['get'],
                ]
            ]
        ];
    }
    /**
     * 独立操作
     * {@inheritDoc}
     * @see \yii\base\Controller::actions()
     */
    public function actions(){
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    /**
     * Index Action 显示所有的充值卡信息
     * @return string
     */
    public function actionIndex(){
        $searchModel = new ProductcardSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
    
    /**
     * 删除选中的一些产品充值卡
     * @param string $keys
     * @return \yii\web\Response
     */
    public function actionDeleteAll($keys){
        //将得到的字符串转为php数组
        $cardNumbers = explode(',', $keys);
        //使用","作为分隔符将数组转为字符串
        $cards = implode('","', $cardNumbers);
        //在最终的字符串前后各加一个"
        $cards = '"' . $cards . '"';
        $model = new Productcard();
        //调用model的deleteAll方法删除数据
        $model->deleteAll("cardNumber in($cards)");
        return $this->redirect(['index']);
    }
}