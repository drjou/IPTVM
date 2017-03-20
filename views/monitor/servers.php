<?php
use app\models\ChartDraw;
use miloschuman\highcharts\Highcharts;
use yii\web\JsExpression;
use app\models\Timezone;
$this->title = 'Server Utilization';
$this->params['breadcrumbs'][]=['label'=>'IPTV Monitor', 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label' => 'Servers Monitor', 'url' => ['servers-status']];
$this->params['breadcrumbs'][] = $this->title;
$timezone = Timezone::getCurrentTimezone();
$operation = 'var time = $("#date-range").val().split(" - ");
                var startTime = moment.tz(time[0], "'.$timezone->timezone.'").format("X");
                var endTime = moment.tz(time[1], "'.$timezone->timezone.'").format("X");
                $("#cpu-chart").highcharts().showLoading();
                $("#ram-chart").highcharts().showLoading();
                $("#disk-chart").highcharts().showLoading();
                $("#load-chart").highcharts().showLoading();
                $.get("index.php?r=monitor/update-servers-data&servers='.Yii::$app->request->get('servers').'&startTime="+startTime+"&endTime="+endTime,
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


<?php 
    ChartDraw::drawDateRange($range, $minDate, $operation);
?><br/><br/>
                    
<?php
    echo ChartDraw::drawLineChart('cpu-chart', $this, 'CPU Utilization', 'CPU Utilization Percentage(%)', '%', $cpuData);
    echo ChartDraw::drawLineChart('ram-chart', $this, 'RAM Utilization', 'RAM Utilization Percentage(%)', '%', $ramData);
    echo ChartDraw::drawLineChart('disk-chart', $this, 'Disk Utilization', 'Free Percentage of Disk(%)', '%', $diskData);
    echo ChartDraw::drawLineChart('load-chart', $this, 'Load Utilization', 'Load Utilization Percentage(%)', '%', $loadData);
?>
				
<div class="gototop">
	<a href="javascript:;" title="返回顶部"></a>
</div>

<?php 
    $this->registerJs("
        $(document).ready(function(){
            $operation
        });
    ");
?>
