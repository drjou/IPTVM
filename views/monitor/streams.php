<?php
use yii\helpers\Html;
use app\models\ChartDraw;

$request = Yii::$app->request;
$this->title = 'Streams Utilization';
$this->params['breadcrumbs'][] = ['label' => 'Streams Monitor', 'url' => ['streams-monitor', 'serverName'=>$request->get('serverName')]];
$this->params['breadcrumbs'][] = $this->title;



$operation = 'var time = $("#date-range").val().split(" - ");
                var startTime = Date.parse(new Date(time[0]));
                var endTime = Date.parse(new Date(time[1]));
                $("#total-chart").highcharts().showLoading();
                $("#memory-chart").highcharts().showLoading();
                $.get("index.php?r=monitor/update-streams-data&serverName='.$request->get('serverName').'&streams='.$request->get('streams').'&startTime="+startTime+"&endTime="+endTime,
                        function(data,status){
                             var obj = eval(data);
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
                        });';

?>

<?php 
    ChartDraw::drawDateRange($range, $minDate, $operation);
?>

<div class="btn-group right">
    <?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
    <?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['streams-grid','type'=>1], ['class' => 'btn btn-default']);?>
</div>

<?php
echo ChartDraw::drawLineChart('total-chart', $this, 'Total Utilization of Stream Process', 'Total Utilization Percentage of Process(%)', '%', $totalData);
?>
<br/><br/>

<?php
echo ChartDraw::drawLineChart('memory-chart', $this, 'Memory Utilization of Stream Process', 'Memory Utilization Percentage of Stream Process(%)', '%', $memoryData);
