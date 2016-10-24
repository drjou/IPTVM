<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\base\Widget;
$this->title = 'Menu List';
$this->params['breadcrumbs'][] = $this->title;
?>
<p>
    <?= Html::a('New Menu', ['create'], ['class' => 'btn btn-success']) ?>
</p>
<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pager' => [
        'firstPageLabel' => 'First Page',
        'lastPageLabel' => 'Last Page',
    ],
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],

        'menuName',
        [
            'attribute' => 'parentName',
            'format' => 'raw',
            'value' => function($model){
                if(!empty($model->parentMenu)){
                    return Html::a($model->parentMenu->menuName, ['menu/view', 'id' => $model->parentId], ['title' => 'view']);
                }
            },
            'filter'=>Html::activeTextInput($searchModel, 'parentName',['class'=>'form-control']),
        ],
        'route',
        [
            'attribute' => 'showLevel',
            'headerOptions' => [
                'width' => 30,
            ],
        ],
        [
            'attribute' => 'showOrder',
            'headerOptions' => [
                'width' => 30,
            ],
        ],
        [
            'attribute' => 'icon',
            'value' => function($model){
                return '<span class="' . $model->icon . '"></span>';
            },
            'headerOptions' => [
                'width' => 40,
            ],
            'format' => 'html',
        ],

        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => '90'],
            'template' => '{view}&nbsp;&nbsp;&nbsp;{update}&nbsp;&nbsp;&nbsp;{delete}',
            'buttons' => [
                'update' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-pencil"></i>',
                        ['update', 'id' => $key],
                        ['title' => 'Update']);
                },
                'delete' => function($url, $model, $key){
                    if(!empty($model->childrenMenus)) return '<i class="glyphicon glyphicon-trash" style="color: #gray;"></i>';
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                    ['delete', 'id' => $key],
                    ['title' => 'Delete',
                        'class' => '',
                        'data' => ['confirm' => "are you sure to delete menu $model->menuName?",]
                    ]);
                },
            ],
        ],
    ],
]); ?>
<p>
    <?= Html::a('New Menu', ['create'], ['class' => 'btn btn-success']) ?>
</p>