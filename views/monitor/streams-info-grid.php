<?php

use yii\helpers\Html;
use yii\grid\GridView;

$request = \Yii::$app->request;
$this->title = 'Streams Info Grid';
$this->params['breadcrumbs'][]=['label'=>'IPTV Monitor', 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label'=>'Streams Monitor', 'url'=>['streams-monitor', 'serverName'=>$request->get('serverName')]];
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
    'streamName',
    [
        'attribute' => 'server',
        'filter' => $servers
    ],
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
    'total','user', 'system', 'memory', 'rss', 'readByte', 'writeByte', 'recordTime'
];

$url=null;
if($request->get('streams')===''){
    $url=['stream-detail', 'streamName'=>$request->get('streamName'), 'serverName'=>$request->get('serverName')];
}else{
    $url=['streams', 'streams'=>$request->get('streams'), 'serverName'=>$request->get('serverName')];
}

?>


<div class="btn-group right">
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', $url, ['class' => 'btn btn-default']);?>
	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', null, ['class' => 'btn btn-default', 'style'=>"background-color:#CCCCCC"]);?>
</div><br/><br/>

<?php 
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'pager' => [
        'firstPageLabel' => 'First Page',
        'lastPageLabel' => 'Last Page',
    ],
    'rowOptions' => function($model, $key, $index, $grid){
        return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
    },
    'columns' => $columns
]);