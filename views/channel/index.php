<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\base\Widget;
$this->title = 'Channel List';
$this->params['breadcrumbs'][] = $this->title;
$urlTypes = [
    'entire' => 'entire',
    'sep' => 'sep',
];
$channelTypes = [
    'live' => 'live',
    'vod' => 'vod',
];
?>
<p>
	<div class="btn-group">
    	<?= Html::a('Delete Selected', [''], ['class' => 'btn btn-danger delete-all', 'disabled' => 'disabled']) ?>
    	<span class="btn btn-default delete-num">0</span>
    </div>
    <?= Html::a('New Channel', ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Import Channels', ['import'], ['class' => 'btn btn-warning']) ?>
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
            'headerOptions' => ['width' => '10'],
        ],
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '10'],
        ],
        'channelName',
        'channelIp',
        [
            'attribute' => 'thumbnail',
            'format' => [
                'image',
                [
                    'width' => 20,
                    'height' => 20,
                ],
            ],
            'value' => function($model){
                return '/IPTVM/web' . $model->channelPic;
            }
        ],
        [
            'attribute' => 'urlType',
            'filter' => $urlTypes,
        ],
        [
            'attribute' => 'channelType',
            'filter' => $channelTypes,
        ],
        [
            'attribute' => 'languageName',
            'format' => 'raw',
            'value' => function($model){
                return Html::a($model->language->languageName, ['language/view', 'languageId' => $model->languageId]);
            },
            'filter' => Html::activeTextInput($searchModel, 'languageName', ['class' => 'form-control']),
        ],
        'createTime',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => '90'],
            'template' => '{view}&nbsp;&nbsp;&nbsp;{update}&nbsp;&nbsp;&nbsp;{delete}',
            'buttons' => [
                'view' => function($url, $model, $key){
                return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                    ['view', 'channelId' => $key],
                    ['title' => 'View']);
                },
                'update' => function($url, $model, $key){
                return Html::a('<i class="glyphicon glyphicon-pencil"></i>',
                    ['update', 'channelId' => $key],
                    ['title' => 'Update']);
                },
                'delete' => function($url, $model, $key){
                    return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                    ['delete', 'channelId' => $key],
                    ['title' => 'Delete',
                     'data' => ['confirm' => "Are you sure to delete channel $model->channelName?"],
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
        $('.delete-all').attr('href', 'index.php?r=channel/delete-all&keys='+keys);
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
        if(!confirm('are you sure to delete these '+num+' channels?')){
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
    <?= Html::a('New Channel', ['create'], ['class' => 'btn btn-success']) ?>
    <?= Html::a('Import Channels', ['import'], ['class' => 'btn btn-warning']) ?>
</p>
