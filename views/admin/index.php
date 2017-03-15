<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\base\Widget;
use app\models\Timezone;
$this->title = 'Administrator List';
$this->params['breadcrumbs'][] = $this->title;
$types = [
    0 => 'genernal',
    1 => 'super',
];
?>
<p>
	<div class="btn-group">
    	<?= Html::a('Delete Selected', [''], ['class' => 'btn btn-danger delete-all', 'disabled' => 'disabled']) ?>
    	<span class="btn btn-default delete-num">0</span>
    </div>
    <?php 
        if(Yii::$app->user->identity->type == 0){
            echo Html::a('New Administrator', ['create'], ['class' => 'btn btn-success disabled']);
        }else {
            echo Html::a('New Administrator', ['create'], ['class' => 'btn btn-success']);
        }
    ?>
</p>
<?= GridView::widget([
    'options' => ['class' => 'gridview', 'style' => 'overflow:auto', 'id' => 'grid'],
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
            'class' => 'yii\grid\CheckboxColumn',
            'name' => 'id',
            'checkboxOptions' => function ($model, $key, $index, $column){
                if(Yii::$app->user->identity->type == 0 || Yii::$app->user->identity->userName == $model->userName){
                    return ['disabled' => 'disabled'];
                }
                return [];
            },
            'headerOptions' => ['width' => '10'],
        ],
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '10'],
        ],
        'userName',
        'realName',
        'email',
        [
            'attribute' => 'type',
            'value' => function($model){
                return $model->type ? 'super' : 'genernal';
            },
            'filter' => $types,
        ],
        [
            'attribute' => 'lastLoginTime',
            'value' => function($model){
                return Timezone::date($model->lastLoginTime);
            },
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => '90'],
            'template' => '{view}&nbsp;&nbsp;&nbsp;{update}&nbsp;&nbsp;&nbsp;{delete}',
            'buttons' => [
                'view' => function($url, $model, $key){
                return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                    ['view', 'id' => $key],
                    ['title' => 'View']);
                },
                'update' => function($url, $model, $key){
                    if(Yii::$app->user->identity->type == 0) return '<i class="glyphicon glyphicon-pencil" style="color: gray;"></i>';
                    return Html::a('<i class="glyphicon glyphicon-pencil"></i>',
                    ['update', 'id' => $key],
                    ['title' => 'Update']);
                },
                'delete' => function($url, $model, $key){
                    if(Yii::$app->user->identity->type == 0 || Yii::$app->user->identity->userName == $model->userName) 
                        return '<i class="glyphicon glyphicon-trash" style="color: gray;"></i>';
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                    ['delete', 'id' => $key],
                    ['title' => 'Delete',
                     'data' => ['confirm' => "Are you sure to delete administrator $model->realName?"],
                    ]);
                },
            ],
        ],
    ],
]); 
$this->registerJs("
$(document).on('click', '.gridview', function () {
    var keys = $('#grid').yiiGridView('getSelectedRows');
    if(keys.length>0){
        $('.delete-all').attr('disabled', false);
        $('.delete-num').html(keys.length);
        $('.delete-all').attr('href', 'index.php?r=admin/delete-all&keys='+keys);
    }else{
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
        if(!confirm('are you sure to delete these '+num+' adminstrators?')){
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
    <?php 
        if(Yii::$app->user->identity->type == 0){
            echo Html::a('New Administrator', ['create'], ['class' => 'btn btn-success disabled']);
        }else {
            echo Html::a('New Administrator', ['create'], ['class' => 'btn btn-success']);
        }
    ?>
</p>
