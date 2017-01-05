<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\base\Widget;
$this->title = 'Monitored Servers';
$this->params['breadcrumbs'][] = $this->title;
$states = [
    1 => 'enabled',
    0 => 'disabled'
]
?>
<p>
	<div class="btn-group">
    	<?= Html::a('Delete Selected', [''], ['class' => 'btn btn-danger delete-all', 'disabled' => 'disabled']) ?>
    	<span class="btn btn-default delete-num">0</span>
    </div>
    <?= Html::a('New Server', ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Import Servers', ['import'], ['class' => 'btn btn-warning']) ?>
</p>
<?php 
echo GridView::widget([
    'options' => [
        'class' => 'gridview',
        'style' => 'overflow:auto',
        'id' => 'grid'
    ],
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
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
        'serverName',
        'serverIp',
        [
            'attribute' => 'state',
            'value' => function($model){
                if($model->state==1){
                    return 'enabled';
                }
                else{
                    return 'disabled';
                }
            },
            'filter' => $states
        ],
        'operatingSystem',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => 120],
            'template' => '{view}&nbsp;&nbsp;&nbsp;{update}&nbsp;&nbsp;&nbsp;{delete}&nbsp;&nbsp;&nbsp;{enable}',
            'buttons' => [
                'view' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                        ['view', 'serverName' => $key],
                        ['title' => 'View']);
                },
                'update' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-pencil"></i>',
                        ['update', 'serverName' => $key],
                        ['title' => 'Update']);
                },
                'delete' => function($url, $model, $key){
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        ['delete', 'serverName' => $key],
                        ['title' => 'Delete', 'data' => ['confirm' => "Are you sure to delete server $key?"]]);
                },
                'enable' => function($url, $model, $key){
                    if($model->state==1){
                        return Html::a('<span class="glyphicon glyphicon-ban-circle"></span>',
                            ['disable', 'serverName' => $key],
                            ['title' => 'Disable', 'data' => ['confirm' => "Are you sure to disable server $key ?"]]);
                    }
                    else{
                        return Html::a('<span class="glyphicon glyphicon-ok-circle"></span>',
                            ['enable', 'serverName' => $key],
                            ['title' => 'Enable', 'data' => ['confirm' => "Are you sure to enable server $key ?"]]);
                    }
                }
            ],
        ]
    ]
]);

$this->registerJs("
$(document).on('click', '.gridview', function(){
    var keys = $('#grid').yiiGridView('getSelectedRows');
    if(keys.length>0){
        $('.delete-all').attr('disabled', false);
        $('.delete-num').html(keys.length);
        $('.delete-all').attr('href', 'index.php?r=server/delete-all&keys='+keys);
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
        if(!confirm('Are you sure to delete these '+num+' servers?')){
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