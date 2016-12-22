<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'CPU Grid';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$request = Yii::$app->request;
?>
<div style="float: left">
<span>Server Name: <strong><?php echo $request->get('serverName')?></strong></span>
</div>
<div style="float: right">
<?= Html::a('Chart', ['cpu-chart','serverName'=>$request->get('serverName')], ['class' => 'btn btn-default']);?>
<?= Html::a('Grid', null, ['class' => 'btn btn-default']);?><br/>
</div><br/><br/>
<?php echo GridView::widget([
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

