<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\base\Widget;
use app\models\Account;
use app\models\Timezone;
$this->title = 'STB Log List';
$this->params['breadcrumbs'][] = $this->title;
?>
<?= GridView::widget([
    'options' => ['class' => 'gridview', 'style' => 'overflow:auto', 'id' => 'grid'],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => [
        'firstPageLabel' => 'First Page',
        'lastPageLabel' => 'Last Page',
    ],
    'rowOptions' => function($model, $key, $index, $grid){
        return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
    },
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '10'],
        ],
        [
            'attribute' => 'prefix',
            'format' => 'raw',
            'value' => function($model){
                if(!empty(Account::findOne($model->prefix))){
                    return Html::a($model->prefix, ['account/view', 'accountId' => $model->prefix], ['title' => 'view']);
                }else{
                    return $model->prefix;
                }
            }
        ],
        [
            'attribute' => 'log_time',
            'value' => function($model){
                return Timezone::date($model->log_time);
            }
        ],
        [
            'attribute' => 'message',
            'value' => function($model){
                if(strlen($model->message) <= 40){
                    return $model->message;
                }
                return substr($model->message, 0, 40) . '...';
            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => '30'],
            'template' => '&nbsp;&nbsp;&nbsp;&nbsp;{stb-view}',
            'buttons' => [
                'stb-view' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                    ['stb-view', 'id' => $key],
                    ['title' => 'View']);
                },
            ],
        ],
    ],
]); 
?>