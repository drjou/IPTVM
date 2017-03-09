<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\grid\GridView;
$this->title = 'Streams Monitor';
$this->params['breadcrumbs'][]=['label'=>'IPTV Monitor', 'url'=>['index']];
$this->params['breadcrumbs'][] = $this->title;

$status = [
    1 => 'UP',
    0 => 'DOWN'
];

?>

<?=Html::dropDownList('serverName', $model, $servers, ['id'=>'server-servername','class' => 'form-control','style'=>'width:100px;float:left']);?>
&nbsp;&nbsp;&nbsp;
<?= Html::a('Generate Comparation Chart', '#', 
    ['class' => 'btn btn-success',
        'id' => 'create',
        'data-toggle' => 'modal',
        'data-target' => '#create-modal'
]) ?>
&nbsp;&nbsp;&nbsp;
<span>Status:</span>
<span class="label label-success">UP</span>
<span class="label label-warning">UNKNOWN</span>
<span class="label label-danger">DOWN</span>
<?php 

Modal::begin([
    'id' => 'create-modal',
    'header' => '<h4 class="modal-title">Please select streams on '.$model->serverName.'</h4>',
]);
?>
    <?php $form = ActiveForm::begin(); ?>
        <?= $form->field($model, 'streams', ['template' => 
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
            'format' => 'html',
            'value' => function($model){
                if($model->serverInfo->status == 0) return '<i class="fa fa-circle" style="color:#f0ad4e;"></i>';
                return $model->status == 1 ? '<i class="fa fa-circle" style="color:#5cb85c;"></i>' : '<i class="fa fa-circle" style="color:#d9534f;"></i>';
            },
        ],
        'streamName',
        'source',
        [
            'attribute' => 'sourceStatus',
            'filter' => $status,
            'format' => 'html',
            'value' => function($model){
                if($model->serverInfo->status == 0) return '<i class="fa fa-circle" style="color:#f0ad4e;"></i>';
                return $model->status == 1 ? '<i class="fa fa-circle" style="color:#5cb85c;"></i>' : '<i class="fa fa-circle" style="color:#d9534f;"></i>';
            },
        ],
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
            'class' => 'yii\grid\ActionColumn',
            'header' => 'Operations',
            'headerOptions' => ['width' => 120],
            'template' => '{view}&nbsp;&nbsp;&nbsp;{switch}&nbsp;&nbsp;&nbsp;{restart}&nbsp;&nbsp;&nbsp;{play}',
            'buttons' => [
                'view' => function ($url, $model, $key) {
                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', [
                        'stream-detail', 
                        'streamName' => $model->streamName, 
                        'serverName'=>$model->server
                    ], [
                        'title' => 'View'
                    ]);
                },
                'switch' => function ($url, $model, $key) {
                    if ($model->status == 0)
                        return Html::a('<i class="fa fa-power-off" style="color:#5cb85c;"></i>', [
                            'switch',
                            'streamName' => $key
                        ], [
                            'title' => 'Start'
                        ]);
                    return Html::a('<i class="fa fa-power-off" style="color:#d9534f;"></i>', [
                        'switch',
                        'streamName' => $key
                    ], [
                        'title' => 'Stop'
                    ]);
                },
                'restart' => function ($url, $model, $key) {
                    if ($model->status == 0)
                        return '<span class="fa fa-refresh" style="color:gray;"></span>';
                    return Html::a('<span class="fa fa-refresh"></span>', [
                        'restart',
                        'streamName' => $key
                    ], [
                        'title' => 'Restart'
                    ]);
                },
                'play' => function ($url, $model, $key) {
                    if ($model->status == 0)
                        return '<span class="fa fa-play-circle" style="color:gray;"></span>';
                    return Html::a('<span class="fa fa-play-circle"></span>', [
                        'play',
                        'streamName' => $key
                    ], [
                        'title' => 'Delete'
                    ]);
                 },
             ],
        ]
    ]
])
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
        var \$tds = $('td');
        for(var i=13,j=0;i<\$tds.length;i+=8,j++){
            for(var k=i;k<i+2;k++){
                $(\$tds[k]).css('cursor','pointer');
            }
            
            $(\$tds[i]).click(function(){
                var streamName = $(this).siblings().eq(2).html();
                window.location.href='index.php?r=monitor/streams&streams='+streamName+'&serverName=$serverName';
            });
            $(\$tds[i+1]).click(function(){
                var streamName = $(this).siblings().eq(2).html();
                window.location.href='index.php?r=monitor/streams&streams='+streamName+'&serverName=$serverName';
            });
        }
    }
    var updateStream = function(streamName, k){
        var \$process = $('.progress .progress-bar');
        var serverName = '$serverName';
            $.get('index.php?r=monitor/update-stream-grid-info&serverName='+serverName+'&streamName='+streamName,function(data,status){
                changeProcessColor($(\$process[k]),data.cpuInfo);
                $(\$process[k]).css('width',data.cpuInfo+'%');
                $(\$process[k]).text(data.cpuInfo+'%');
                changeProcessColor($(\$process[k+1]),data.ramInfo);
                $(\$process[k+1]).css('width',data.ramInfo+'%');
                $(\$process[k+1]).text(data.ramInfo+'%');
            });
    }
    var updateStreams = function(){
        var \$process = $('.progress .progress-bar');
        for(var i=0;i<\$process.length;i+=2){
            var streamName = $($('.progress')[i]).parent().siblings().eq(2).html();
            updateStream(streamName, i);
        }
    }
    $(document).ready(function() {
        setProgressOnClick();
        updateStreams();
        setInterval(updateStreams,30000);
        $('#server-servername').change(function(){
            var server = $('#server-servername option:selected').text();
            location.href='index.php?r=monitor/streams-monitor&serverName='+server;
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
        $('#server-streams .checkbox').css('display', 'inline');
    });
");
?>