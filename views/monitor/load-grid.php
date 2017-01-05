<?php
use yii\helpers\Html;
use yii\grid\GridView;

$this->title = 'Load Grid';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$request = Yii::$app->request;
?>

<div class="left">
	<span>Server Name: <strong><?php echo $request->get('serverName')?></strong></span>
</div>

<div class="btn-group right">
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', ['load-chart','serverName'=>$request->get('serverName')], ['class' => 'btn btn-default']);?>
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
            'attribute' => 'recordTime',
            'headerOptions' => ['width' => '180'],
        ],
         'load1', 
        'load5', 
        'load15', 
        'processRun', 
        'processTotal'
    ]
]);
?>

