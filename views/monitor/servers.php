<?php
use app\models\ChartDraw;
$this->title = 'Server Monitor';
$this->params['breadcrumbs'][] = $this->title;

$operation = 'function() {
                var time = $("#w0").val().split(" - ");
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
                                    series.setData(obj[i][j].data);
                                }
                             }
                            $("#cpu-chart").highcharts().hideLoading();
                            $("#ram-chart").highcharts().hideLoading();
                            $("#disk-chart").highcharts().hideLoading();
                            $("#load-chart").highcharts().hideLoading();
                        });
             }';
?>

<?php 
    ChartDraw::drawDateRange($range, $minDate, $operation);
?><br/><br/>

<?php
echo ChartDraw::drawLineChart('cpu-chart', $this, 'CPU Utilization', 'CPU Utilization Percentage(%)', '%', $cpuData);
echo ChartDraw::drawLineChart('ram-chart', $this, 'RAM Utilization', 'RAM Utilization Percentage(%)', '%', $ramData);
echo ChartDraw::drawLineChart('disk-chart', $this, 'Disk Utilization', 'Free Percentage of Disk(%)', '%', $diskData);
echo ChartDraw::drawLineChart('load-chart', $this, 'Load Utilization', 'Load Utilization Percentage(%)', '%', $loadData);