<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\Admin;
use app\models\Account;
use app\models\Product;
use app\models\Channel;
use app\models\Directory;
use app\models\Productcard;
use app\models\Language;
use app\models\AdminLog;
use app\models\StbLog;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
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
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $counts = [
            'admin' => 0,
            'account' => 0,
            'product' => 0,
            'channel' => 0,
            'directory' => 0,
            'productcard' => 0,
            'language' => 0,
            'log' => 0,
        ];
        $counts['admin'] = count(Admin::find()->all());
        $counts['account'] = count(Account::find()->all());
        $counts['product'] = count(Product::find()->all());
        $counts['channel'] = count(Channel::find()->all());
        $counts['directory'] = count(Directory::find()->all());
        $counts['productcard'] = count(Productcard::find()->all());
        $counts['language'] = count(Language::find()->all());
        $counts['log'] = count(AdminLog::find()->all()) + count(StbLog::find()->all());
        return $this->render('index', [
            'counts' => $counts,
        ]);
    }
}
