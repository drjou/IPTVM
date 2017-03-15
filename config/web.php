<?php


$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    //默认的路由
    'defaultRoute' => 'admin/login',
    //'timeZone' => 'PRC',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'kuanhongiptvm',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'loginUrl' => ['admin/login'],//修改默认的登录url，通过goHome()方法可以跳回该url
            'identityClass' => 'app\models\Admin',
            'enableAutoLogin' => true,
            'enableSession' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'logTable' => 'admin_log',
                    'levels' => ['info'],
                    'categories' => [
                        'administrator',
                    ],
                    'logVars' => [],
                    'prefix' => function ($message) {
                        $userName = Yii::$app->user->identity->userName;
                        return "$userName";
                    }
                ],
                [
                    'class' => 'yii\log\DbTarget',
                    'logTable' => 'stb_log',
                    'levels' => ['info'],
                    'categories' => [
                        'stb',
                    ],
                    'logVars' => [],
                    'prefix' => function ($message) {
                        $accountId = Yii::$app->request->get('accountId');
                        return "$accountId";
                    }
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        /*
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        */
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
