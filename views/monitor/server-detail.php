<?php
use app\models\ChartDraw;
use yii\helpers\Url;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;
$this->title = 'Server Details';
$this->params['breadcrumbs'][] = ['label' => 'Server Status', 'url' => ['servers-status']];
$this->params['breadcrumbs'][] = $this->title;

$request = Yii::$app->request;
?>

<?php $form=ActiveForm::begin([])?>

<?=$form->field($model, 'serverName')->dropDownList($servers)->label(false) ?>

<?php ActiveForm::end(); ?>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">Realtime State</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <div class="text-center">
                    	<div class="left" style="width:220px;">
                        	<?php echo ChartDraw::drawGauge('cpu-gauge', 'CPU', 0, 100, 0, '%');?>
                        		<a
                    			href="<?= Url::to(['monitor/cpu-chart', 'serverName' =>  $request->get('serverName')]) ?>">View
                    			Details</a>
                    	</div>
                    	<div class="left" style="width:220px;">
                    		<?php echo ChartDraw::drawGauge('ram-gauge', 'RAM', 0, 100, 0, '%');?>
                    		<a
                    			href="<?= Url::to(['monitor/ram-chart', 'serverName' =>  $request->get('serverName')]) ?>">View
                    			Details</a>
                    	</div>
                    	<div class="left" style="width:220px;">
                    		<?php echo ChartDraw::drawGauge('disk-gauge', 'DISK', 0, 100, 0, '%');?>
                    		<a
                    			href="<?= Url::to(['monitor/disk-chart', 'serverName' =>  $request->get('serverName')])?>">View
                    			Details</a>
                    	</div>
                    	<div class="left" style="width:220px;">
                    		<?php echo ChartDraw::drawGauge('load-gauge', 'LOAD', 0, 100, 0, '<br/>');?>
                    		<a
                    			href="<?= Url::to(['monitor/load-chart', 'serverName' =>  $request->get('serverName')])?>">View
                    			Details</a>
                    	</div>
                    </div>
				</div>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">Other State</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?php echo DetailView::widget([
                        'model' => $model,
                        'template' => function ($attribute, $index, $widget){
                            if($index%2 == 0){
                                return '<tr class="label-white"><th>' . $attribute['label'] . '</th><td>' . $attribute['value'] . '</td></tr>';
                            }else{
                                return '<tr class="label-grey"><th>' . $attribute['label'] . '</th><td>' . $attribute['value'] . '</td></tr>';
                            }
                        },
                        'attributes' => [
                            [
                                'label' => 'Live Streams Status',
                                'format' => 'html',
                                'value' => '<span>'.$model->liveStreamsCount.' of '.$model->streamsCount.' Active</span>&nbsp;&nbsp;
                                <a href="'.Url::to(['monitor/streams-monitor', 'serverName'=>$request->get('serverName')]).
                                '&StreamSearch%5Bstatus%5D=1">View Details</a>',
                            ],
                            [
                                'label' => 'Nginx Status',
                                'format' => 'html',
                                'value' => '<span>'.($model->nginx->status===1?'UP':'DOWN').'</span>&nbsp;&nbsp;
                                <a href="'.Url::to(['monitor/nginx-chart', 'serverName'=>$request->get('serverName')]).
                                '">View Details</a>',
                            ],
                            [
                                'label' => 'MySQL Status',
                                'format' => 'html',
                                'value' => '<span>'.($model->mysql->status===1?'UP':'DOWN').'</span>&nbsp;&nbsp;
                                <a href="'.Url::to(['monitor/mysql-chart', 'serverName'=>$request->get('serverName')]).
                                '">View Details</a>',
                            ]
                        ],
                    ]);?>
				</div>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->
</div>

<?php
$serverName = $request->get('serverName');
$this->registerJs("
    var updateGuage = function() {
        var gaugeChart1 = $('#cpu-gauge').highcharts();
        var point1 = gaugeChart1.series[0].points[0];
        var gaugeChart2 = $('#ram-gauge').highcharts();
        var point2 = gaugeChart2.series[0].points[0];
        var gaugeChart3 = $('#disk-gauge').highcharts();
        var point3 = gaugeChart3.series[0].points[0];
        var gaugeChart4 = $('#load-gauge').highcharts();
        var point4 = gaugeChart4.series[0].points[0];
        $.get('index.php?r=monitor%2Fupdate-gauge-info&serverName=$serverName',function(data,status){
            point1.update(data.cpuInfo);
            point2.update(data.ramInfo);
            point3.update(data.diskInfo);
            point4.update(data.loadInfo);
        });
    }
    $(document).ready(function() {
        updateGuage();
        setInterval(updateGuage,1000);
        $('#server-servername').change(function(){
            var server = $('#server-servername option:selected').text();
            location.href='index.php?r=monitor/server-detail&serverName='+server;
        });
    });
    ");
?>