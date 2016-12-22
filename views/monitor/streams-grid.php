<?php

use yii\helpers\Html;
use yii\grid\GridView;
$this->title = 'Streams Monitor';
$this->params['breadcrumbs'][] = $this->title;

$enables = [
    0 => 'off',
    1 => 'on',
]
?>

<div style="float: right">
<?= Html::a('Chart', ['streams'], ['class' => 'btn btn-default']);?>
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
        'server',
        'processName', 
        [
            'attribute' => 'status',
            'value' => function($model){
                return $model->status == 1 ? 'on' : 'off';
            },
            'filter' => $enables,
            'headerOptions' => ['width' => '85'],
        ], 
        [
            'attribute' => 'recordTime',
            'headerOptions' => ['width' => '155'],
        ],
        'total',
        'user', 
        'system', 
        'memory', 
        'rss', 
        'readByte', 
        'writeByte',
    ]
]);