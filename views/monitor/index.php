<?php
use app\models\ChartDraw;
use yii\helpers\Html;
$this->title = 'IPTV Monitor';
$this->params['breadcrumbs'][] = $this->title;

$operationProcess = '
    var time = $("#process-date-range").val().split(" - ");
    var startTime = Date.parse(new Date(time[0]));
    var endTime = Date.parse(new Date(time[1]));
    $("#process-chart").highcharts().showLoading();
    $.get("index.php?r=monitor/update-warning-line&type=Process&startTime="+startTime+"&endTime="+endTime,
        function(data,status){
            var obj = eval(data);
            for(var i=0;i<obj[0].length;i++){
                var series=$("#process-chart").highcharts().series[i];
                series.setData(obj[0][i].data,false);
            }
            $("#process-chart").highcharts().redraw();
            $("#process-chart").highcharts().hideLoading();
            updateProcessTooltip(obj[1]);
    });
';
$operationMySql = '
    var time = $("#mysql-date-range").val().split(" - ");
    var startTime = Date.parse(new Date(time[0]));
    var endTime = Date.parse(new Date(time[1]));
    $("#mysql-chart").highcharts().showLoading();
    $.get("index.php?r=monitor/update-warning-line&type=MySql&startTime="+startTime+"&endTime="+endTime,
        function(data,status){
            var obj = eval(data);
            var series=$("#mysql-chart").highcharts().series[0];
            series.setData(obj[0][0].data);
            $("#mysql-chart").highcharts().hideLoading();
            updateTooltip(obj[1], $("#mysql-chart").highcharts());
    });
';
$operationNginx = '
    var time = $("#nginx-date-range").val().split(" - ");
    var startTime = Date.parse(new Date(time[0]));
    var endTime = Date.parse(new Date(time[1]));
    $("#nginx-chart").highcharts().showLoading();
    $.get("index.php?r=monitor/update-warning-line&type=Nginx&startTime="+startTime+"&endTime="+endTime,
        function(data,status){
            var obj = eval(data);
            var series=$("#nginx-chart").highcharts().series[0];
            series.setData(obj[0][0].data);
            $("#nginx-chart").highcharts().hideLoading();
            updateTooltip(obj[1], $("#nginx-chart").highcharts());
    });
';
?>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">CPU Utilization</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?php 
                        ChartDraw::drawDateRange($range, $minDate, ChartDraw::operation('CPU', 'cpu-data-range', 'cpu-chart'), 'cpu-data-range');
                    ?>
                    <div class="btn-group right">
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['cpu-grid', 'serverName'=>'', 'type'=>0], ['class' => 'btn btn-default']);?>
                    </div>
                    <br/><br/>
                    
                    <?php
                        echo ChartDraw::drawLineChart('cpu-chart', $this, 'CPU Utilization', 'CPU Utilization Percentage(%)', '%', $cpuData);
                    ?>
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
				<h5 style="font-weight: bold;">RAM Utilization</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?php 
                        ChartDraw::drawDateRange($range, $minDate, ChartDraw::operation('RAM', 'ram-data-range', 'ram-chart'), 'ram-data-range');
                    ?>
                    <div class="btn-group right">
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['ram-grid', 'serverName'=>'', 'type'=>0], ['class' => 'btn btn-default']);?>
                    </div>
                    <br/><br/>
                    
                    <?php
                        echo ChartDraw::drawLineChart('ram-chart', $this, 'RAM Utilization', 'RAM Utilization Percentage(%)', '%', $ramData);
                    ?>
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
				<h5 style="font-weight: bold;">Disk Utilization</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?php 
                        ChartDraw::drawDateRange($range, $minDate, ChartDraw::operation('DISK', 'disk-data-range', 'disk-chart'), 'disk-data-range');
                    ?>
                    <div class="btn-group right">
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['disk-grid', 'serverName'=>'', 'type'=>0], ['class' => 'btn btn-default']);?>
                    </div>
                    <br/><br/>
                    
                    <?php
                        echo ChartDraw::drawLineChart('disk-chart', $this, 'Disk Utilization', 'Free Percentage of Disk(%)', '%', $diskData);
                    ?>
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
				<h5 style="font-weight: bold;">Load Utilization</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?php 
                        ChartDraw::drawDateRange($range, $minDate, ChartDraw::operation('LOAD', 'load-data-range', 'load-chart'), 'load-data-range');
                    ?>
                    <div class="btn-group right">
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['load-grid', 'serverName'=>'', 'type'=>0], ['class' => 'btn btn-default']);?>
                    </div>
                    <br/><br/>
                    
                    <?php
                        echo ChartDraw::drawLineChart('load-chart', $this, 'Load Utilization', 'Load Utilization Percentage(%)', '%', $loadData);
                    ?>
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
				<h5 style="font-weight: bold;">Process Status</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?php 
                        ChartDraw::drawDateRange($range, $minDate, $operationProcess, 'process-date-range');
                    ?>
                    <div class="btn-group right">
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['streams-grid','type'=>0], ['class' => 'btn btn-default']);?>
                    </div>
                    <br/><br/>
                    
                    <?php
                        echo ChartDraw::drawLineChart('process-chart', $this, 'Process Status', 'The Numeber of Dead Processes', '', $processData);
                    ?>
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
				<h5 style="font-weight: bold;">MySQL Status</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?php 
                        ChartDraw::drawDateRange($range, $minDate, $operationMySql, 'mysql-date-range');
                    ?>
                    <div class="btn-group right">
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['mysql-grid'], ['class' => 'btn btn-default']);?>
                    </div>
                    <br/><br/>
                    
                    <?php
                        echo ChartDraw::drawLineChart('mysql-chart', $this, 'MySQL Status', 'Disconnected Number of MySQL', '', $mySqlData);
                    ?>
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
				<h5 style="font-weight: bold;">Nginx Status</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?php 
                        ChartDraw::drawDateRange($range, $minDate, $operationNginx, 'nginx-date-range');
                    ?>
                    <div class="btn-group right">
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
                    	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['nginx-grid'], ['class' => 'btn btn-default']);?>
                    </div>
                    <br/><br/>
                    
                    <?php
                        echo ChartDraw::drawLineChart('nginx-chart', $this, 'Nginx Status', 'Disconnected Number of Nginx', '', $nginxData);
                    ?>
				</div>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->
</div>
<?php 
$this->registerJs("
    function addLine(chart, threshold){
        chart.yAxis[0].addPlotLine({
            value:threshold,
            width:3,
            color: 'red',
            label:{
                text: threshold+'%',
                color:'red',
                align:'below',
                style: {
                   color: 'red',
                }
            },
            zIndex:100
        });
        if(chart==$('#disk-chart').highcharts()){
            chart.yAxis[0].update({
                max:threshold
            });
        }else{
            chart.yAxis[0].update({
                min:threshold
            });
        }
    }
    function updateProcessTooltip(obj){
        var chart = $('#process-chart').highcharts();
        chart.update({
            tooltip:{
                shared:false,
                formatter:function () {
                    var process = '';
                    var date = new Date(this.x);
                    var minute = date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes();
                    var time = (1+date.getMonth())+'-'+date.getDate()+' '+date.getHours()+':'+minute+':00';
                    if(obj.length!=0){
                        if(obj.hasOwnProperty(this.series.name)){
                            if(obj[this.series.name].hasOwnProperty(this.x+'')){
                                var process = '<br/>They are:<br/>';
                                for(var i=0;i<obj[this.series.name][this.x+''].length;i++){
                                    process = process + '<b>' + obj[this.series.name][this.x+''][i] + '<b/><br/>';
                                }
                            }
                        }
                    }
                    return 'time:<b>' + time + '</b><br/>'+
                        'count: <b>' + this.y + '</b>' +
                         process;
                }
            }
        });
    }
    function updateTooltip(obj, chart){
        chart.update({
            tooltip:{
                shared:false,
                formatter:function(){
                    var servers = '';
                    var date = new Date(this.x);
                    var minute = date.getMinutes()<10?'0'+date.getMinutes():date.getMinutes();
                    var time = (1+date.getMonth())+'-'+date.getDate()+' '+date.getHours()+':'+minute+':00';
                    if(obj.length!=0){
                        if(obj.hasOwnProperty(this.x+'')){
                            var servers = '<br/>They are:<br/>';
                            for(var i=0;i<obj[this.x+''].length;i++){
                                servers = servers + '<b>' + obj[this.x+''][i] + '<b/><br/>';
                            }
                        }
                    }
                    return 'time:<b>' + time + '</b><br/>'+
                        'count: <b>' + this.y + '</b>' +
                         servers;
                }
            }
        });
    }
    window.onload = function(){
        var cpuChart = $('#cpu-chart').highcharts();
        addLine(cpuChart, $cpuThreshold);
        var ramChart = $('#ram-chart').highcharts();
        addLine(ramChart, $memoryThreshold);
        var diskChart = $('#disk-chart').highcharts();
        addLine(diskChart, $diskThreshold);
        var loadChart = $('#load-chart').highcharts();
        addLine(loadChart, $loadsThreshold);
        var obj = eval($processData2);
        updateProcessTooltip(obj);
        var obj2 = eval($mySqlData2);
        var chart1 = $('#mysql-chart').highcharts();
        updateTooltip(obj2, chart1);
        var obj3 = eval($nginxData2);
        var chart2 = $('#nginx-chart').highcharts();
        updateTooltip(obj3, chart2);
    }
");
?>
