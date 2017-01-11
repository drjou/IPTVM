<?php
use yii\helpers\Html;
use yii\grid\GridView;

$request = Yii::$app->request;

$this->title = 'Disk Grid';
if($request->get('type') == 1){
    $this->params['breadcrumbs'][] = ['label' => 'Server Monitor', 'url' => ['servers']];
    $this->params['breadcrumbs'][] = ['label' => 'Server Details', 'url' => ['detail','serverName'=>$request->get('serverName')]];
}else{
    $this->params['breadcrumbs'][] = ['label' => 'IPTV Monitor', 'url' => ['index']];
}
$this->params['breadcrumbs'][] = $this->title;


$url = $request->get('serverName') == ''? ['index']:['disk-chart','serverName'=>$request->get('serverName')];
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
        'freePercent', 
        'free', 
        'used', 
        'total'
    ]
]);
?>

