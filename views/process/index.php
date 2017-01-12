<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\base\Widget;
$this->title = 'Monitored Streams';
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
	<div class="btn-group">
    	<?= Html::a('Delete Selected', [''], ['class' => 'btn btn-danger delete-all', 'disabled' => 'disabled']) ?>
    	<span class="btn btn-default delete-num">0</span>
    </div>
    <?= Html::a('New Stream', ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Import Streams', ['import'], ['class' => 'btn btn-warning']) ?>
</p>
<?php 
echo GridView::widget([
    'options' => [
        'class' => 'gridview',
        'style' => 'overflow:auto',
        'id' => 'grid'
    ],
    'dataProvider' => $dataProvider,
    'filterModel' => $filterModel,
    'pager' => [
        'firstPageLabel' => 'First Page',
        'lastPageLabel' => 'Last Page'
    ],
    'rowOptions' => function($model, $key, $index, $grid){
        return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
    },
    'columns' => 
    [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'name' => 'id',
            'headerOptions' => ['width' => '10'],
        ],
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '10'],
        ],
        'processName',
        'source',
        [
            'attribute' => 'server',
            'filter' => $servers
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => 60],
            'template' => '{view}&nbsp;&nbsp;&nbsp;{update}&nbsp;&nbsp;&nbsp;{delete}',
            'buttons' => [
                'view' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                        ['view', 'processName' => $model->processName, 'server' => $model->server],
                        ['title' => 'View']);
                },
                'update' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-pencil"></i>',
                        ['update', 'processName' => $model->processName, 'server' => $model->server],
                        ['title' => 'Update']);
                },
                'delete' => function($url, $model, $key){
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        ['delete', 'processName' => $model->processName, 'server' => $model->server],
                        ['title' => 'Delete', 'data' => ['confirm' => "Are you sure to delete the stream $model->processName on $model->server?"]]);
                }
            ],
        ]
    ]
]);

$this->registerJs("
$(document).on('click', '.gridview', function(){
    var rows = $('#grid').yiiGridView('getSelectedRows');
    var keys = JSON.stringify(rows);
    if(rows.length>0){
        $('.delete-all').attr('disabled', false);
        $('.delete-num').html(rows.length);
        $('.delete-all').attr('href', 'index.php?r=process/delete-all&keys='+keys);
    }
    else{
        $('.delete-all').attr('disabled', 'disabled');
        $('.delete-num').html(0);
        $('.delete-all').attr('href', '');
    }
});
$(document).on('click', '.delete-all', function(){
    if($(this).attr('disabled')){
        return false;
    }else{
        var num = $('.delete-num').html();
        if(!confirm('Are you sure to delete these '+num+' streams?')){
            return false;
        }
    }
});
");
?>

<p>
	<div class="btn-group">
    	<?= Html::a('Delete Selected', [''], ['class' => 'btn btn-danger delete-all', 'disabled' => 'disabled']) ?>
    	<span class="btn btn-default delete-num">0</span>
    </div>
    <?= Html::a('New Server', ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Import Servers', ['import'], ['class' => 'btn btn-warning']) ?>
</p>