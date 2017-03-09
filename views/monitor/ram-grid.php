<?php
use yii\helpers\Html;
use yii\grid\GridView;

$request = Yii::$app->request;
$this->title = 'RAM Grid';
$this->params['breadcrumbs'][]=['label'=>'IPTV Monitor', 'url'=>['index']];
if($request->get('type') == 1){
    $this->params['breadcrumbs'][] = ['label' => 'Servers Monitor', 'url' => ['servers-status']];
    $this->params['breadcrumbs'][] = ['label' => 'Server Details', 'url' => ['server-detail','serverName'=>$request->get('serverName')]];
}else{
    $this->params['breadcrumbs'][] = ['label' => 'Servers Fault', 'url' => ['servers-fault']];
}
$this->params['breadcrumbs'][] = $this->title;
$url = $request->get('type') == 0? ['servers-fault']:['ram-chart','serverName'=>$request->get('serverName')];
?>


<div class="btn-group right">
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', $url, ['class' => 'btn btn-default']);?>
	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', null, ['class' => 'btn btn-default', 'style'=>"background-color:#CCCCCC"]);?>
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
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '10'],
        ],
        [
            'attribute' => 'server',
            'headerOptions' => ['width' => '100'],
            'filter' => $servers
        ],
        [
            'attribute' => 'recordTime',
            'headerOptions' => ['width' => '180'],
        ],
        'utilize', 
        'free', 
        'used', 
        'total', 
        'buffer', 
        'cache'
    ]
]);
?>

