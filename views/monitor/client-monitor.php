<?php

use yii\grid\GridView;
use yii\helpers\Html;

$this->title='Clinet Monitor';
$this->params['breadcrumbs'][]=['label'=>'IPTV Monitor', 'url'=>['index']];
$this->params['breadcrumbs'][]=$this->title;

?>

<?php 
echo GridView::widget([
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
            'attribute' => 'status',
            'format' => 'html',
            'value' => function($model){
                if($model->serverInfo->status == 0) return '<i class="fa fa-circle" style="color:#f0ad4e;"></i>';
                return '<i class="fa fa-circle" style="color:#5cb85c;"></i>';
            },
            'headerOptions' => ['width' => '10'],
        ],
        'accountId',
        'server',
        'stream',
        'Ip',
        'startTime',
        'totalTime',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => '10'],
            'template' => '&nbsp;&nbsp;&nbsp;{enable}',
            'buttons' => [
                'enable' => function($url, $model, $key){
                return Html::a('<i class="glyphicon glyphicon-ban-circle"></i>',
                    ['enable', 'accountId' => $key['accountId']],
                    ['title' => 'View']);
                },
            ],
        ],
    ]
]);
?>