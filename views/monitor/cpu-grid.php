<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'CPU Grid';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$request = Yii::$app->request;
?>

<div class="left">
	<span>Server Name: <strong><?php echo $request->get('serverName')?></strong></span>
</div>

<div class="btn-group right">
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', ['cpu-chart','serverName'=>$request->get('serverName')], ['class' => 'btn btn-default']);?>
	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', null, ['class' => 'btn btn-default']);?>
</div><br/><br/>

<?php 
echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'rowOptions' => function($model, $key, $index, $grid){
        return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
    },
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '10'],
        ],
        'ncpu',
        [
            'attribute' => 'recordTime',
            'headerOptions' => ['width' => '180'],
        ],
        'utilize',
        'user', 
        'system', 
        'wait', 
        'hardIrq', 
        'softIrq', 
        'nice', 
        'steal', 
        'guest',
        'idle'
    ]
]);
?>

