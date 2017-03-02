<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\bootstrap\Modal;
use yii\bootstrap\ActiveForm;

$this->title = 'Servers Status';
$this->params['breadcrumbs'][] = $this->title;

$status = [
    1 => 'up',
    0 => 'down'
]
?>
<?= Html::a('Generate Comparation Chart', '#', 
    ['class' => 'btn btn-success',
        'id' => 'create',
        'data-toggle' => 'modal',
        'data-target' => '#create-modal'
]) ?>

<?php 

Modal::begin([
    'id' => 'create-modal',
    'header' => '<h4 class="modal-title">Please select servers</h4>',
]);
?>
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'servers', ['template' => 
            '{label}
            <div class="checkgroup">
                <input type="checkbox" class="all" Name="CheckAll"><label for="all" class="label-all">Check All</label><br />
                {input}
            </div>
            {error}',
        ])->checkboxList($servers, [ 'separator'=>'&nbsp;&nbsp;']) ?>
        <div class="form-group">
            <?= Html::submitButton('Generate Charts', ['class' => 'btn btn-success']) ?>
            <?= Html::a('Cancel', '#', ['class' => 'btn btn-warning cancel','data-dismiss'=>"modal"]) ?>
        </div>
    <?php ActiveForm::end();
Modal::end();


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
            },
        ],
        'serverName',
        'serverIp',
        [
            'attribute' => 'CPU',
            'format' => 'html',
            'headerOptions' => ['width' => '100'],
            'value' => function($model){
                if($model->status===0){
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
                if($model->status===0){
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
            'attribute' => 'Disk',
            'format' => 'html',
            'headerOptions' => ['width' => '100'],
            'value' => function($model){
                if($model->status===0){
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
            'attribute' => 'Load',
            'format' => 'html',
            'headerOptions' => ['width' => '100'],
            'value' => function($model){
                if($model->status===0){
                    return '<span>---</span>';
                }
                return '<div class="progress" style="margin-bottom: 0px;">
                          <div class="progress-bar" role="progressbar" style="color:black;">
                            0%
                          </div>
                        </div>';
            }
        ],
        'operatingSystem',
        [
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => 20],
            'template' => '{view}',
            'buttons' => [
                'view' => function($url, $model, $key){
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                        ['detail', 'serverName' => $model->serverName],
                        ['title' => 'View']);
                }
            ],
        ]
    ]
]);
?>

<?php
$this->registerJs("
    function changeProcessColor(\$selector,value){
        if(value>=0 && value<=30){
            \$selector.removeClass('progress-bar-warning');
            \$selector.removeClass('progress-bar-danger');
            \$selector.addClass('progress-bar-success');
        }else if(value>30 && value<=70){
            \$selector.removeClass('progress-bar-sucess');
            \$selector.removeClass('progress-bar-danger');
            \$selector.addClass('progress-bar-warning');
        }else{
            \$selector.removeClass('progress-bar-warning');
            \$selector.removeClass('progress-bar-sucess');
            \$selector.addClass('progress-bar-danger');
        }
    }
    function setProgressOnClick(){
        var \$process = $('.progress .progress-bar')
        for(var i=0,j=0;i<\$process.length;i+=4,j++){
            var server = $($('.progress')[j]).parent().siblings().eq(2).html();
            for(var k=i;k<i+4;k++){
                $(\$process[k]).css('cursor','pointer');
            }
            $(\$process[i]).click(function(){
                window.location.href='index.php?r=monitor/cpu-chart&serverName='+server;
            });
            $(\$process[i+1]).click(function(){
                window.location.href='index.php?r=monitor/ram-chart&serverName='+server;
            });
            $(\$process[i+2]).click(function(){
                window.location.href='index.php?r=monitor/disk-chart&serverName='+server;
            });
            $(\$process[i+3]).click(function(){
                window.location.href='index.php?r=monitor/load-chart&serverName='+server;
            });
        }
    }
    var updateProcess = function(){
        var \$process = $('.progress .progress-bar')
        for(var i=0,j=0;i<\$process.length;i+=4,j++){
            var server = $($('.progress')[j]).parent().siblings().eq(2).html();
            var k=i;
            $.get('index.php?r=monitor/update-gauge-info&serverName='+server,function(data,status){
                changeProcessColor($(\$process[k]),data.cpuInfo);
                $(\$process[k]).css('width',data.cpuInfo+'%');
                $(\$process[k]).text(data.cpuInfo+'%');
                changeProcessColor($(\$process[k+1]),data.ramInfo);
                $(\$process[k+1]).css('width',data.ramInfo+'%');
                $(\$process[k+1]).text(data.ramInfo+'%');
                changeProcessColor($(\$process[k+2]),data.diskInfo);
                $(\$process[k+2]).css('width',data.diskInfo+'%');
                $(\$process[k+2]).text(data.diskInfo+'%');
                changeProcessColor($(\$process[k+3]),data.loadInfo);
                $(\$process[k+3]).css('width',data.loadInfo+'%');
                $(\$process[k+3]).text(data.loadInfo+'%');
            });
        }
    }
    $(document).ready(function() {
        setProgressOnClick();
        updateProcess();
        setInterval(updateProcess,2000);
        $('.all').change(function(){
    		if(this.checked){
    			$('.label-all').html('Deselect All');
    		}else{
    			$('.label-all').html('Check All');
    		}
            $('#server-servers input').prop('checked', this.checked);
	    });
        
        $('#server-servers input').change(function(){
    		var checkAll = true;
        	$('#server-servers input').each(function(){
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
