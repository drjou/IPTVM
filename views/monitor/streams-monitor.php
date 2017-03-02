<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
$this->title = 'Streams Monitor';
$this->params['breadcrumbs'][] = $this->title;

$status = [
    1 => 'up',
    0 => 'down'
]
?>

<?php $form=ActiveForm::begin(['id'=>'form'])?>

<?=$form->field($model, 'serverName')->dropDownList($servers, ['style'=>['width'=>'100px', 'float'=>'left'],'label'=>''])->label(false) ?>

<?php ActiveForm::end(); ?>
&nbsp;&nbsp;&nbsp;
<?= Html::a('Generate Comparation Chart', '#', 
    ['class' => 'btn btn-success',
        'id' => 'create',
        'data-toggle' => 'modal',
        'data-target' => '#create-modal'
]) ?>
<?php 

Modal::begin([
    'id' => 'create-modal',
    'header' => '<h4 class="modal-title">Please select streams on '.$model->serverName.'</h4>',
]);
?>
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model2, 'streams', ['template' => 
            '{label}
            <div class="checkgroup">
                <input type="checkbox" class="all" Name="CheckAll"><label for="all" class="label-all">Check All</label><br/>
                {input}
            </div>
            {error}',
        ])->checkboxList($streams, [ 'separator'=>'&nbsp;&nbsp;']) ?>
        <div class="form-group">
            <?= Html::submitButton('Generate Charts', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', '#', ['class' => 'btn btn-warning cancel','data-dismiss'=>"modal"]) ?>
        </div>
    <?php ActiveForm::end();?>
<?php Modal::end();?>

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
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '10'],
        ],
        [
            'attribute' => 'status',
            'filter' => $status,
            'value' => function($model){
                return $model->status == 1 ? 'up' : 'down';
            }
        ],
        'streamName',
        'source',
        [
            'attribute' => 'sourceStatus',
            'filter' => $status,
            'value' => function($model){
                return $model->sourceStatus == 1 ? 'up' : 'down';
            }
        ],
        [
            'attribute' => 'CPU',
            'format' => 'html',
            'headerOptions' => ['width' => '100'],
            'value' => function($model){
                if($model->status===0 || $model->sourceStatus===0){
                    return '<span>---</span>';
                }
                return '<div class="progress"  style="margin-bottom: 0px;">
                                  <div class="progress-bar" role="progressbar" style="color:black;">
                                    0%
                                  </div>
                                </div>';
                }
        ],
        [
            'attribute' => 'RAM',
            'format' => 'html',
            'headerOptions' => ['width' => '100'],
            'value' => function($model){
                if($model->status===0 || $model->sourceStatus===0){
                    return '<span>---</span>';
                }
                return '<div class="progress"  style="margin-bottom: 0px;">
                                  <div class="progress-bar" role="progressbar" style="color:black;">
                                    0%
                                  </div>
                                </div>';
                }
        ],
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => 120],
            'template' => '{view}&nbsp;&nbsp;&nbsp;{turnoff}&nbsp;&nbsp;&nbsp;{restart}&nbsp;&nbsp;&nbsp;{start}',
            'buttons' => [
                'view' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                        ['view', 'serverName' => $key],
                        ['title' => 'View']);
                },
                'turnoff' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-off" style="color:red"></i>',
                        ['update', 'serverName' => $key],
                        ['title' => 'Turn Off']);
                },
                'restart' => function($url, $model, $key){
                    return Html::a('<span class="glyphicon glyphicon-refresh"></span>',
                        ['delete', 'serverName' => $key],
                        ['title' => 'Restart']);
                },
                'start' => function($url, $model, $key){
                    if($model->status==1){
                        return Html::a('<span class="glyphicon glyphicon-play-circle"></span>',
                            ['disable', 'serverName' => $key],
                            ['title' => 'Start']);
                    }
                    else{
                        return Html::a('<span class="glyphicon glyphicon-ok-circle"></span>',
                            ['enable', 'serverName' => $key],
                            ['title' => 'Enable']);
                    }
                }
                ],
        ]
    ]
])
?>

<?php 
$this->registerJs("
    $(document).ready(function() {
        $('#server-servername').change(function(){
            $('#form').submit();
        });
        $('.all').change(function(){
    		if(this.checked){
    			$('.label-all').html('Deselect All');
    		}else{
    			$('.label-all').html('Check All');
    		}
            $('#server-streams input').prop('checked', this.checked);
	    });
        
        $('#server-streams input').change(function(){
    		var checkAll = true;
        	$('#server-streams input').each(function(){
        		if(!$(this).prop('checked')){
        			checkAll = false;
        			$('.all').prop('checked', false);
        			$('.label-all').html('Check All');
        			return false;
        		}
        	});
        	if(checkAll){
        		$('.all').prop('checked', true);
        		$('.label-all').html('Deselect All');
        	}
	    });
    });
");
?>