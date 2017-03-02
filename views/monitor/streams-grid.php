<?php

use yii\helpers\Html;
use yii\grid\GridView;

$request = \Yii::$app->request;
$this->title = 'Streams Grid';
if($request->get('type')==1){
    $this->params['breadcrumbs'][] = ['label'=>'Streams Monitor', 'url'=>'streams'];
}else{
    $this->params['breadcrumbs'][] = ['label'=>'IPTV Monitor', 'url'=>'index'];
}
$this->params['breadcrumbs'][] = $this->title;

$enables = [
    0 => 'down',
    1 => 'up',
];

$columns = [
    [
        'class' => 'yii\grid\SerialColumn',
        'headerOptions' => ['width' => '10'],
    ],
    [
        'attribute' => 'server',
        'filter' => $servers
    ],
    'streamName',
    [
        'attribute' => 'status',
        'value' => function($model){
            return $model->status == 1 ? 'up' : 'down';
        },
        'filter' => $enables,
        'headerOptions' => ['width' => '85'],
    ],
    [
        'attribute' => 'sourceStatus',
        'value' => function($model){
            return $model->sourceStatus == 1 ? 'up' : 'down';
        },
        'filter' => $enables,
        'headerOptions' => ['width' => '85'],
    ],
    [
        'attribute' => 'recordTime',
        'headerOptions' => ['width' => '180'],
    ]
];

$type = $request->get('type');
$url = ['streams'];
$type==1?array_push($columns, 'total', 'user', 'system', 'memory', 'rss', 'readByte', 'writeByte'):$url=['index'];
?>

<div class="btn-group right">
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', $url, ['class' => 'btn btn-default']);?>
	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', null, ['class' => 'btn btn-default']);?>
</div><br/><br/>

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
    'columns' => $columns
]);