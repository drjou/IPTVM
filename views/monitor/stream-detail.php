<?php

use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\helpers\Html;
use app\models\ChartDraw;
$request = Yii::$app->request;
$this->title = 'Stream Details';
$this->params['breadcrumbs'][] = ['label' => 'Streams Monitor', 'url' => ['streams-monitor', 'serverName'=>$request->get('serverName')]];
$this->params['breadcrumbs'][] = $this->title;
$operation='
    var time = $("#date-range").val().split(" - ");
                var startTime = Date.parse(new Date(time[0]));
                var endTime = Date.parse(new Date(time[1]));
                $("#total-chart").highcharts().showLoading();
                $("#memory-chart").highcharts().showLoading();
                $.get("index.php?r=monitor/update-stream-data&serverName='.$request->get('serverName').'&streamName='.$request->get('streamName').'&startTime="+startTime+"&endTime="+endTime,
                        function(data,status){
                             var obj = eval(data);
                            console.log(obj);
                             for(var i=0;i<obj.length;i++){
                                var id;
                                switch(i){
                                    case 0: id="#total-chart"; break;
                                    case 1: id="#memory-chart"; break;
                                }
                                for(var j=0;j<obj[i].length;j++){
                                    var series=$(id).highcharts().series[j];
                                    series.setData(obj[i][j].data,false);
                                }
                             }
                            $("#total-chart").highcharts().redraw();
                            $("#memory-chart").highcharts().redraw();
                            $("#total-chart").highcharts().hideLoading();
                            $("#memory-chart").highcharts().hideLoading();
                        });
    ';
?>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">Stream Status</h5>
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
                            'streamName',
                            [
                                'label' => 'server',
                                'format' => 'html',
                                'value' => '<a href="'.Url::to(['monitor/server-detail', 'serverName'=>$request->get('serverName')]).
                                '">'.$model->server.'</a>',
                            ],
                            [
                                'label' => 'Status',
                                'format' => 'html',
                                'value' => $model->status == 1 ? '<i class="fa fa-circle" style="color:#5cb85c;"></i>' : '<i class="fa fa-circle" style="color:#d9534f;"></i>'
                            ],
                            [
                                'label' => 'Source Status',
                                'format' => 'html',
                                'value' => $model->sourceStatus == 1 ? '<i class="fa fa-circle" style="color:#5cb85c;"></i>' : '<i class="fa fa-circle" style="color:#d9534f;"></i>'
                            ],
                            'latestDisconnectedTime',
                            [
                                'label' => 'Source',
                                'format' => 'html',
                                'value' => '<a href="http://'.$model->serverInfo->serverIp.$model->source.
                                '">http://'.$model->serverInfo->serverIp.$model->source.'</a>'
                            ],
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
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">CPU&RAM Utilization</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?= ChartDraw::drawDateRange($range, $minDate, $operation);?>
                    
                    <div class="btn-group right">
                        <?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
                        <?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', null, ['class' => 'btn btn-default']);?>
                    </div>
                    
                    <?= ChartDraw::drawLineChart('total-chart', $this, 'CPU Utilization of Stream', 'CPU Utilization Percentage of Process(%)', '%', $cpuData);?>
                    <br/><br/>
                    
                    <?= ChartDraw::drawLineChart('memory-chart', $this, 'RAM Utilization of Stream', 'RAM Utilization Percentage of Stream Process(%)', '%', $ramData);?>
				</div>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->
</div>