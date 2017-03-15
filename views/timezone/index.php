<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\base\Widget;
use app\models\Timezone;
$this->title = 'Timezone List';
$this->params['breadcrumbs'][] = $this->title;
$currents = [
    1 => 'Yes',
    0 => 'No'
];
$status = [
    1 => 'Enable',
    0 => 'Disable'
];
?>
<p>
    <?= Html::a('New Timezone', ['create'], ['class' => 'btn btn-success']) ?>
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
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '10'],
        ],
        'timezone',
        [
            'attribute' => 'isCurrent',
            'format' => 'html',
            'value' => function($model){
                return $model->isCurrent == 1 ? '<i class="glyphicon glyphicon-ok" style="color:green;"></i>' : '<i class="glyphicon glyphicon-remove" style="color:red;"></i>';
            },
            'filter' => $currents,
        ],
        [
            'attribute' => 'status',
            'format' => 'html',
            'value' => function($model){
                return $model->status == 1 ? '<i class="fa fa-circle" style="color:green;"></i>' : '<i class="fa fa-circle" style="color:red;"></i>';
            },
            'filter' => $status,
        ],
        'continent',
        'country',
        [
            'attribute' => 'icon',
            'format' => 'raw',
            'value' => function($model){
                return '<svg class="icon" aria-hidden="true">
                        	<use xlink:href="#'. $model->icon .'"></use>
                        </svg>';
            }
        ],
        'chinese',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => '120'],
            'template' => '{view}&nbsp;&nbsp;&nbsp;{update}&nbsp;&nbsp;&nbsp;{delete}&nbsp;&nbsp;&nbsp;{change-status}',
            'buttons' => [
                'view' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                        ['view', 'timezone' => $key],
                        ['title' => 'View']);
                },
                'update' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-pencil"></i>',
                        ['update', 'timezone' => $key],
                        ['title' => 'View']);
                },
                'delete' => function($url, $model, $key){
                if($model->isCurrent) return '<i class="glyphicon glyphicon-trash" style="color:gray;"></i>';
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        ['delete', 'timezone' => $key],
                        ['title' => 'Delete',
                         'data' => ['confirm' => "Are you sure to delete timezone $key?"],
                        ]);
                },
                'change-status' => function($url, $model, $key){
                    if($model->isCurrent) return '<i class="glyphicon glyphicon-ban-circle" style="color:gray;"></i>';
                    if($model->status == 1) 
                        return Html::a('<span class="glyphicon glyphicon-ban-circle"></span>',
                        ['disable', 'timezone' => $key],
                        ['title' => 'Disable',
                            'data' => ['confirm' => "Are you sure to disable timezone $key?"],
                        ]);
                    return Html::a('<span class="glyphicon glyphicon-ok-circle"></span>',
                        ['enable', 'timezone' => $key],
                        ['title' => 'Enable',
                            'data' => ['confirm' => "Are you sure to enable timezone $key?"],
                        ]);
                },
            ],
        ],
    ],
]); 
?>
<p>
    <?= Html::a('New Timezone', ['create'], ['class' => 'btn btn-success']) ?>
</p>
