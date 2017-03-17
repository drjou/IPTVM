<?php
use yii\grid\GridView;
use yii\helpers\Html;
use app\models\Timezone;
$this->title = 'StreamAccess Log';
$this->params['breadcrumbs'][]=['label'=>'IPTV Monitor', 'url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;

$status = [
    '1' => 'UP',
    '0' => 'DOWN'
];
?>

<?= GridView::widget([
    'options' => ['class' => 'gridview', 'style' => 'overflow:auto', 'id' => 'grid'],
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
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
        'id', 
        'accountId', 
        'server', 
        'stream', 
        'Ip', 
        [
            'attribute' => 'startTime',
            'value' => function($model){
                return Timezone::date($model->startTime);
            }
        ],
        [
            'attribute' => 'endTime',
            'value' => function($model){
                return Timezone::date($model->endTime);
            }
        ], 
        'totalTime',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => '10'],
            'template' => '&nbsp;&nbsp;&nbsp;{delete}',
            'buttons' => [
                'delete' => function($url, $model, $key){
                return Html::a('<i class="glyphicon glyphicon-trash"></i>',
                    ['delete-stream-access-log', 'id' => $key],
                    ['title' => 'Delete', 'data' => ['confirm' => "Are you sure to delete Log $key?"]]);
                },
            ],
        ]
    ]
]);