<?php
namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Account;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\API;
use app\models\Product;
use yii\helpers\ArrayHelper;
use app\models\Directory;
use app\models\Channel;
use yii\db\Exception;
use app\models\Stbbind;
use app\models\Productcard;
use app\models\AccountProduct;

class ApiController extends Controller{
    /**
     * 访问权限设置
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
                        'roles' => ['?'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'get-product' => ['get'],
                    'get-product-channel' => ['get'],
                ],
            ],
        ];
    }
    /**
     * 认证 
     * @param string $accountId
     * @return string[]|string
     */
    protected function auth($accountId){
        if(empty($accountId)){//缺少accountId参数
            return [
                'error' => API::ERR0001,
            ];
        }
        $model = Account::findOne($accountId);
        if(empty($model)){//数据库中无此account
            return [
                'info' => API::INFO0001,
            ];
        }
        if($model->enable == 0){
            return [
                'info' => API::INFO0002,
            ];
        }
        if($model->state == 1001 || $model->state == 1004){//可以进行后续的操作
            return 'success';
        }elseif($model->state == 1002){//stb绑定了产品，需要进行激活操作
            return [
                'account' => [
                    'accountId' => $model->accountId,
                    'state' => $model->state,
                ],
                'info' => API::INFO0003,
            ];
        }elseif ($model->state == 1003){//stb未绑定产品，需要购买充值卡进行激活
            return [
                'account' => [
                    'accountId' => $model->accountId,
                    'state' => $model->state,
                ],
                'info' => API::INFO0004,
            ];
        }else {//错误的stb状态
            return [
                'error' => API::ERR0002,
            ];
        }
    }
    /**
     * 根据accountId获取Stb对应的所有产品
     * @param string $accountId
     * @return \app\controllers\string[]|string
     */
    public function actionGetProduct($accountId = null){
        Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        if(($state = $this->auth($accountId)) === 'success'){//认证成功
            $model = Account::findAccountById($accountId);
            //获取表account_product中的数据
            $accountProducts = $model->accountProducts;
            //对象转数组，并给productName属性赋值
            $products = ArrayHelper::toArray($accountProducts, [
                'app\models\AccountProduct' => [
                    'productId',
                    'productName' => function($accountProduct){
                        return $accountProduct->product->productName;
                    },
                    'endDate',
                ],
            ]);
            //返回xml响应（将itemTag置为Product）
            return Yii::createObject([
                'class' => 'yii\web\Response',
                'format' => \yii\web\Response::FORMAT_XML,
                'formatters' => [
                    \yii\web\Response::FORMAT_XML => [
                        'class' => 'yii\web\XmlResponseFormatter',
                        'rootTag' => 'reponse', //根节点
                        'itemTag' => 'product',
                    ],
                ],
                'data' => $products,
            ]);
        }else {//认证失败
            return $state;
        }
    }
    /**
     * 根据productId获取其包含的所有channels信息
     * @param string $accountId
     * @param int $productId
     * @return \app\controllers\string[]|string
     */
    public function actionGetProductChannel($accountId = null, $productId = null){
        Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        if(($state = $this->auth($accountId)) === 'success'){//认证成功
            if(empty($productId)){//缺少productId参数
                return [
                    'error' => API::ERR0003,
                ];
            }
            //获取product对象
            $model = Account::findOne($accountId)->getApiProducts()->where(['productId' => $productId])->one();
            if(empty($model)){//机顶盒未购买此产品
                return [
                    'error' => API::ERR0004,
                ];
            }
            //获取channels转为数组，并给languageName赋值
            $channels = ArrayHelper::toArray($model->channels, [
                'app\models\Channel' => [
                    'channelId',
                    'channelName',
                    'channelIp',
                    'channelPic',
                    'channelUrl',
                    'urlType',
                    'channelType',
                    'languageName' => function($channel){
                        return $channel->language->languageName;
                    },
                ]
            ]);
            //返回xml响应（将itemTag置为Product）
            return Yii::createObject([
                'class' => 'yii\web\Response',
                'format' => \yii\web\Response::FORMAT_XML,
                'formatters' => [
                    \yii\web\Response::FORMAT_XML => [
                        'class' => 'yii\web\XmlResponseFormatter',
                        'rootTag' => 'reponse', //根节点
                        'itemTag' => 'channel',
                    ],
                ],
                'data' => $channels,
            ]);
        }else{
            return $state;
        }
    }
    /**
     * 获取所有用来显示的directory
     * @param int $accountId
     * @return \app\controllers\string[]|string
     */
    public function actionGetDirectory($accountId = null){
        Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        if(($state = $this->auth($accountId)) === 'success'){//认证成功
            $model = new Directory();
            $directories = $model->find()->where(['parentId' => null])->all();
            $directories = ArrayHelper::toArray($directories, [
                'app\models\Directory' => [
                    'directoryId',
                    'directoryName',
                    'parentName' => function($directory){
                        return empty($directory->parentDirectory) ? null : $directory->parentDirectory->directoryName;
                    },
                    'children' => function($directory){
                        return $directory->childrenDirectories;
                    },
                ],
            ]);
            //返回xml响应（将itemTag置为Product）
            return Yii::createObject([
                'class' => 'yii\web\Response',
                'format' => \yii\web\Response::FORMAT_XML,
                'formatters' => [
                    \yii\web\Response::FORMAT_XML => [
                        'class' => 'yii\web\XmlResponseFormatter',
                        'rootTag' => 'reponse', //根节点
                        'itemTag' => 'directory',
                    ],
                ],
                'data' => $directories,
            ]);
        }else{
            return $state;
        }
    }
    /**
     * 获取目录下的所有channels信息
     * @param string $accountId
     * @param int $directoryId
     * @return string[]|object|mixed|\app\controllers\string[]|string
     */
    public function actionGetDirectoryChannel($accountId = null, $directoryId = null){
        Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        if(($state = $this->auth($accountId)) === 'success'){//认证成功
            if(empty($directoryId)){//缺少directoryId参数
                return [
                    'error' => API::ERR0005,
                ];
            }
            //获取product对象
            $model = Directory::findOne($directoryId);
            if(empty($model)){//数据库中无此目录
                return [
                    'Error' => API::ERR0006,
                ];
            }
            //获取channels转为数组，并给languageName赋值
            $channels = [];
            foreach ($model->channels as $channel){
                $accountIds = ArrayHelper::getColumn($channel->accounts, 'accountId');
                $tmp = [
                    'channelId' => $channel->channelId,
                    'channelName' => $channel->channelName,
                    'channelIp' => $channel->channelIp,
                    'channelPic' => $channel->channelPic,
                    'channelUrl' => $channel->channelUrl,
                    'urlType' => $channel->urlType,
                    'channelType' => $channel->channelType,
                    'languageName' => $channel->language->languageName,
                    'purchased' => ArrayHelper::isIn($accountId, $accountIds) ? 'yes' : 'no',
                ];
                array_push($channels, $tmp);
            }
            /* $channels = ArrayHelper::toArray($model->channels, [
                'app\models\Channel' => [
                    'channelId',
                    'channelName',
                    'channelIp',
                    'channelPic',
                    'channelUrl',
                    'urlType',
                    'channelType',
                    'languageName' => function($channel){
                        return $channel->language->languageName;
                    },
                    'account' => function($channel){
                        return $channel->accounts;
                    }
                ]
            ]); */
            //返回xml响应（将itemTag置为Product）
            return Yii::createObject([
                'class' => 'yii\web\Response',
                'format' => \yii\web\Response::FORMAT_XML,
                'formatters' => [
                    \yii\web\Response::FORMAT_XML => [
                        'class' => 'yii\web\XmlResponseFormatter',
                        'rootTag' => 'reponse', //根节点
                        'itemTag' => 'channel',
                    ],
                ],
                'data' => $channels,
            ]);
        }else{
            return $state;
        }
    }
    /**
     * 获取节目的详细信息
     * @param string $accountId
     * @param int $channelId
     * @return string[]
     */
    public function actionGetEpg($accountId = null, $channelId = null){
        Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        if(($state = $this->auth($accountId)) === 'success'){//认证成功
            if(empty($channelId)){//缺少channelId参数
                return [
                    'error' => API::ERR0007,
                ];
            }
            $model = Channel::findOne($channelId);
            if(empty($model)){//数据库中无此channel
                return [
                    'error' => API::ERR0008,
                ];
            }
            $accountIds = ArrayHelper::getColumn($model->accounts, 'accountId');
            $channel = [
                'channelId' => $model->channelId,
                'channelName' => $model->channelName,
                'channelIp' => $model->channelIp,
                'channelPic' => $model->channelPic,
                'channelUrl' => $model->channelUrl,
                'urlType' => $model->urlType,
                'channelType' => $model->channelType,
                'languageName' => $model->language->languageName,
                'purchased' => ArrayHelper::isIn($accountId, $accountIds) ? 'yes' : 'no',
            ];
            return $channel;
        }else {
            return $state;
        }
    }
    /**
     * stb账户激活
     * @param string $accountId
     * @return string[]
     */
    public function actionActive($accountId = null){
        Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        if(empty($accountId)){//缺少accountId参数
            return [
                'error' => API::ERR0001,
            ];
        }
        $model = Account::findOne($accountId);
        if(empty($model)){//数据库中无此account
            return [
                'info' => API::INFO0001,
            ];
        }
        if($model->enable == 0){//该stb已被禁用
            return [
                'info' => API::INFO0002,
            ];
        }
        if($model->state == 1001 || $model->state == 1004){//已经被激活无需再次激活
            return [
                'info' => API::INFO0005,
            ];
        }elseif($model->state == 1002){//需要激活，执行激活操作
            $db = Yii::$app->db;
            $transaction = $db->beginTransaction();
            try{
                $model->state = 1001;
                $model->scenario = Account::SCENARIO_SAVE;
                if($model->save()){//首先更新stb状态为1001
                    $bindProducts = Stbbind::find()->where(['accountId' => $accountId])->all();
                    $now = date('Y-m-d H:i:s', time());
                    foreach ($bindProducts as $bindProduct){
                        $bindProduct->isActive = 1;
                        $bindProduct->activeDate = $now;
                        if($bindProduct->save()){//将stbbind表中相关行置为已激活状态并记录激活时间
                            $db->createCommand()->insert('account_product', [
                                'accountId' => $accountId,
                                'productId' => $bindProduct->productId,
                                'endDate' => date('Y-m-d', strtotime("+$bindProduct->bindDay day")),
                            ])->execute();
                            
                        }else {//保存失败就rollback
                            $transaction->rollBack();
                            return [
                                'info' => API::INFO0007,
                            ];
                        }
                    }
                    $transaction->commit();
                    return [
                        'info' => API::INFO0008,
                    ];
                }else{
                    $transaction->rollBack();
                    return [
                        'info' => API::INFO0007,
                    ];
                }
            }catch (Exception $e){
                $transaction->rollBack();
            }
        }elseif($model->state == 1003){//不能直接激活，需购买充值卡充值
            return [
                'info' => API::INFO0006,
            ];
        }else{//stb状态错误
            return [
                'error' => API::ERR0002,
            ];
        }
    }
    /**
     * stb使用充值卡充值
     * @param string $accountId
     * @param string $cardNumber
     */
    public function actionPurchase($accountId = null, $cardNumber = null){
        Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
        if(empty($accountId)){//缺少accountId参数
            return [
                'error' => API::ERR0001,
            ];
        }
        if(empty($cardNumber)){//缺少cardNumber参数
            return [
                'error' => API::ERR0009,
            ];
        }
        $card = Productcard::findOne($cardNumber);
        if(empty($card)){//数据库中无此充值卡
            return [
                'error' => API::ERR0010,
            ];
        }
        if($card->cardState == 1){//该充值卡已被使用
            return [
                'error' => API::ERR0011,
            ];
        }
        $model = Account::findOne($accountId);
        if(!empty($model)){//该accountId存在于数据库中
            if($model->enable == 0){//该stb已被禁用，暂不能充值
                return [
                    'info' => API::INFO0002,
                ];
            }
            if($model->state == 1002){//需要先激活方可购买
                return [
                    'info' => API::INFO0009,
                ];
            }elseif($model->state == 1001 || $model->state == 1004){//可购买，购买后更新充值卡为不可用
                //查询用户是否存在与该充值卡对应的产品
                $accountProduct = AccountProduct::find()->where(['accountId' => $accountId, 'productId' => $card->productId])->one();
                if(!empty($accountProduct)){//如果之前就有，则只需更新下产品的到期时间
                    $transaction = Yii::$app->db->beginTransaction();
                    $accountProduct->endDate = date('Y-m-d', strtotime("+$card->cardValue day", strtotime($accountProduct->endDate)));
                    if($accountProduct->save()){//保存成功则购买成功，更新充值卡状态
                        $card->cardState = 1;
                        $card->useDate = date('Y-m-d', time());
                        $card->accountId = $accountId;
                        $card->scenario = Productcard::SCENARIO_API;
                        if($card->save()){//修改充值卡状态成功
                            $transaction->commit();
                            return [
                                'info' => API::INFO0010,
                            ];
                        }else {//修改充值卡状态失败
                            $transaction->rollBack();
                            return [
                                'info' => API::INFO0011,
                            ];
                        }
                    }else {//购买失败
                        $transaction->rollBack();
                        return [
                            'info' => API::INFO0011,
                        ];
                    }
                }else{//如果之前stb无此产品，则需新加数据并更新充值卡状态
                    $transaction = Yii::$app->db->beginTransaction();
                    $accountProduct = new AccountProduct();
                    $accountProduct->accountId = $accountId;
                    $accountProduct->productId = $card->productId;
                    $accountProduct->endDate = date('Y-m-d', strtotime("+$card->cardValue day"));
                    if($accountProduct->save()){//成功
                        $card->cardState = 1;
                        $card->useDate = date('Y-m-d', time());
                        $card->accountId = $accountId;
                        $card->scenario = Productcard::SCENARIO_API;
                        if($card->save()){//修改充值卡状态成功
                            $transaction->commit();
                            return [
                                'info' => API::INFO0010,
                            ];
                        }else {//修改充值卡状态失败
                            $transaction->rollBack();
                            return [
                                'info' => API::INFO0011,
                            ];
                        }
                    }else{//失败
                        $transaction->rollBack();
                        return [
                            'info' => API::INFO0011,
                        ];
                    }
                }
            }elseif($model->state == 1003){//购买后需更新自身状态及充值卡状态，且之前肯定不曾有产品
                $transaction = Yii::$app->db->beginTransaction();
                $model->state = 1001;
                $model->scenario = Account::SCENARIO_SAVE;
                if($model->save()){//更新状态
                    $accountProduct = new AccountProduct();
                    $accountProduct->accountId = $accountId;
                    $accountProduct->productId = $card->productId;
                    $accountProduct->endDate = date('Y-m-d', strtotime("+$card->cardValue day"));
                    if($accountProduct->save()){//数据加入成功
                        $card->cardState = 1;
                        $card->useDate = date('Y-m-d', time());
                        $card->accountId = $accountId;
                        $card->scenario = Productcard::SCENARIO_API;
                        if($card->save()){//修改充值卡状态成功
                            $transaction->commit();
                            return [
                                'info' => API::INFO0010,
                            ];
                        }else {//修改充值卡状态失败
                            $transaction->rollBack();
                            return [
                                'info' => API::INFO0011,
                            ];
                        }
                    }else{//数据添加失败
                        $transaction->rollBack();
                        return [
                            'info' => API::INFO0011,
                        ];
                    }
                }else{//更新状态未成功
                    $transaction->rollBack();
                    return [
                        'info' => API::INFO0011,
                    ];
                }
            }else{//错误的stb状态
                return [
                    'error' => API::ERR0002,
                ];
            }
        }else{//数据库中无此accountId，使用充值卡后直接存入数据库，并更新充值卡状态
            $transaction = Yii::$app->db->beginTransaction();
            $account = new Account();
            $account->scenario = Account::SCENARIO_SAVE;
            $account->accountId = $accountId;
            $account->state = 1004;
            $account->enable = 1;
            if($account->save()){//加入新的account成功
                $accountProduct = new AccountProduct();
                $accountProduct->accountId = $accountId;
                $accountProduct->productId = $card->productId;
                $accountProduct->endDate = date('Y-m-d', strtotime("+$card->cardValue day"));
                if($accountProduct->save()){//加入新的数据成功
                    $card->cardState = 1;
                    $card->useDate = date('Y-m-d', time());
                    $card->accountId = $accountId;
                    $card->scenario = Productcard::SCENARIO_API;
                    if($card->save()){//修改充值卡状态成功
                        $transaction->commit();
                        return [
                            'info' => API::INFO0010,
                        ];
                    }else {//修改充值卡状态失败
                        $transaction->rollBack();
                        return [
                            'info' => API::INFO0011,
                        ];
                    }
                }else{//加入新的数据失败
                    $transaction->rollBack();
                    return [
                        'info' => API::INFO0011,
                    ];
                }
            }else{//加入新的account失败
                $transaction->rollBack();
                return [
                    'info' => API::INFO0011,
                ];
            }
        }
    }
}