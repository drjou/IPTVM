<?php
use app\models\ChartDraw;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
$this->title = 'Server Monitor';
$this->params['breadcrumbs'][] = $this->title;

$operation = 'var time = $("#date-range").val().split(" - ");
                var startTime = Date.parse(new Date(time[0]));
                var endTime = Date.parse(new Date(time[1]));
                $("#cpu-chart").highcharts().showLoading();
                $("#ram-chart").highcharts().showLoading();
                $("#disk-chart").highcharts().showLoading();
                $("#load-chart").highcharts().showLoading();
                $.get("index.php?r=monitor/update-line-info&serverName=&type=Servers&startTime="+startTime+"&endTime="+endTime,
                        function(data,status){
                             var obj = eval(data);
                             for(var i=0;i<obj.length;i++){
                                var id;
                                switch(i){
                                    case 0: id="#cpu-chart"; break;
                                    case 1: id="#ram-chart"; break;
                                    case 2: id="#disk-chart"; break;
                                    case 3: id="#load-chart"; break;
                                }
                                for(var j=0;j<obj[i].length;j++){
                                    var series=$(id).highcharts().series[j];
                                    series.setData(obj[i][j].data,false);
                                }
                             }
                            $("#cpu-chart").highcharts().redraw();
                            $("#ram-chart").highcharts().redraw();
                            $("#disk-chart").highcharts().redraw();
                            $("#load-chart").highcharts().redraw();
                            $("#cpu-chart").highcharts().hideLoading();
                            $("#ram-chart").highcharts().hideLoading();
                            $("#disk-chart").highcharts().hideLoading();
                            $("#load-chart").highcharts().hideLoading();
                        });';
?>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">Details</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <div>
                        <button id="btn-fullscreen" class="btn btn-default right"><i class="iconfont iconfont-blue icon-fullscreen"></i></button>
                        <?php
                        echo Highcharts::widget([
                            'id' => 'heat-map',
                            'scripts' => [
                                'modules/heatmap',
                            ],
                            
                            'options' => [
                                'chart' => [
                                    'type' => 'heatmap',
                                    'marginTop' => 40,
                                    'height' => 400,
                                ],
                                'title' => [
                                    'text' => 'Utilization of Servers'
                                ],
                                'credits' => [
                                    'enabled' => false
                                ],
                                'xAxis' => [
                                    'categories' => $xCategories
                                ],
                                'yAxis' => [
                                    'categories' => [
                                        'CPU',
                                        'RAM',
                                        'DISK',
                                        'LOAD'
                                    ],
                                    'title' => null
                                ],
                                'colorAxis' => [
                                    'min' => 0,
                                    'max' => 100,
                                    'stops' => [
                                        [
                                            0.1,
                                            '#55BF3B'
                                        ], // green
                                        [
                                            0.5,
                                            '#DDDF0D'
                                        ], // yellow
                                        [
                                            0.9,
                                            '#DF5353'
                                        ]
                                    ]
                                ],
                                'legend' => [
                                    'align' => 'right',
                                    'layout' => 'vertical',
                                    'margin' => 0,
                                    'verticalAlign' => 'top',
                                    'y' => 23,
                                    'symbolHeight' => 320
                                ],
                                'tooltip' => [
                                    'formatter' => new JsExpression('function () {
                                        return "<b>" + this.series.yAxis.categories[this.point.y] + "</b> of <b>" +
                                         this.series.xAxis.categories[this.point.x]+ "</b><br> is <b>" + this.point.value + "</b>";
                                    }')
                                ],
                                'plotOptions' => [
                                    'series' => [
                                        'events' => [
                                            'click' => new JsExpression('function(event){
                                                                var x = this.xAxis.categories[event.point.x];
                                                                location.href="index.php?r=monitor/detail&serverName="+x;
                                                            }')
                                        ]
                                    ]
                                ],
                                'series' => [
                                    [
                                        'name' => 'Utiliztion of Servers',
                                        'borderWidth' => 1,
                                        'data' => $heatData,
                                        'dataLabels' => [
                                            'enabled' => true,
                                            'color' => '#000000'
                                        ]
                                    ]
                                ]
                            ]
                        ]);
                        ?>
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
				<h5 style="font-weight: bold;">Utilization of Servers</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?php 
                        ChartDraw::drawDateRange($range, $minDate, $operation);
                    ?><br/><br/>
                    
                    <?php
                    echo ChartDraw::drawLineChart('cpu-chart', $this, 'CPU Utilization', 'CPU Utilization Percentage(%)', '%', $cpuData);
                    echo ChartDraw::drawLineChart('ram-chart', $this, 'RAM Utilization', 'RAM Utilization Percentage(%)', '%', $ramData);
                    echo ChartDraw::drawLineChart('disk-chart', $this, 'Disk Utilization', 'Free Percentage of Disk(%)', '%', $diskData);
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
<div class="gototop">
	<a href="javascript:;" title="返回顶部"></a>
</div>

<?php 
$this->registerJs("
    var updateHeat = function updateHeatMap(){
        $.get('index.php?r=monitor/update-heat-map',function(data,status){
            var heatMap = $('#heat-map').highcharts();
            var series = heatMap.series[0];
            var obj = eval(data);
            series.setData(obj);
        })
    }
    setInterval(updateHeat,1000);
    var fullscreen = document.getElementById('btn-fullscreen');
    var heatMap = document.getElementById('heat-map');
    if (fullscreen && heatMap) {
        fullscreen.addEventListener('click', function (evt) {
            if (heatMap.requestFullscreen) {
                heatMap.requestFullscreen();
            }
            else if (heatMap.msRequestFullscreen) {
                heatMap.msRequestFullscreen();
            }
            else if (heatMap.mozRequestFullScreen) {
                heatMap.mozRequestFullScreen();
            }
            else if (heatMap.webkitRequestFullScreen) {
                heatMap.webkitRequestFullScreen();
            }
        }, false);
    }
");
?>