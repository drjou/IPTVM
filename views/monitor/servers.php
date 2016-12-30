<?php
use app\models\ChartDraw;
$this->title = 'Server Monitor';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    Highcharts.setOptions({
    global:{
    useUTC:false
}});");

$opration=
    'var obj = eval(data);
     for(var i=0;i<obj.length;i++){
        var id;
        switch(i){
            case 0: id="#w1"; break;
            case 1: id="#w2"; break;
            case 2: id="#w3"; break;
            case 3: id="#w4"; break;
        }
        for(var j=0;j<obj[i].length;j++){
            var series=$(id).highcharts().series[j];
            series.setData(obj[i][j].data);
        }
     }';
?>

<?php 
    ChartDraw::drawDateRange('', 'Servers', $range, $minDate, $opration);
?><br/><br/>

<?php
echo ChartDraw::drawLineChart('CPU Utilization', 'Click and drag to zoom in', 'CPU Utilization Percentage(%)', '%', $cpuData);
echo ChartDraw::drawLineChart('RAM Utilization', 'Click and drag to zoom in', 'RAM Utilization Percentage(%)', '%', $ramData);
echo ChartDraw::drawLineChart('Disk Utilization', 'Click and drag to zoom in', 'Free Percentage of Disk(%)', '%', $diskData);
echo ChartDraw::drawLineChart('Load Utilization', 'Click and drag to zoom in', 'Load Utilization Percentage(%)', '%', $loadData);