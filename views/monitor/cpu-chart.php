<?php
use app\models\ChartDraw;
use yii\helpers\Html;

$request = Yii::$app->request;
$this->title = 'CPU Chart';
$this->params['breadcrumbs'][]=['label'=>'IPTV Monitor', 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label' => 'Servers Monitor', 'url' => ['servers-status']];
$this->params['breadcrumbs'][] = ['label' => 'Server Details', 'url' => ['server-detail','serverName'=>$request->get('serverName')]];
$this->params['breadcrumbs'][] = $this->title;
$timezone =  'Asia/Shanghai';

$operation = 'var time = $("#date-range").val().split(" - ");
                var startTime = Date.parse(new Date(time[0]));
                var endTime = Date.parse(new Date(time[1]));
                $("#linechart").highcharts().showLoading();
                $.get("index.php?r=monitor/update-line-info&serverName='.$request->get('serverName').'&type=CPU&startTime="+startTime+"&endTime="+endTime,
                        function(data,status){
                            var obj = eval(data);
                            for(var i=0;i<obj.length;i++){
                                var series=$("#linechart").highcharts().series[i];
                                series.setData(obj[i].data,false);
                            }
                            $("#linechart").highcharts().redraw();
                            $("#linechart").highcharts().hideLoading();
                        });';
?>

<?php 
    ChartDraw::drawDateRange($range, $minDate, $operation);
?>
    
<div class="btn-group right">
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default',  'style'=>"background-color:#CCCCCC"]);?>
	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['cpu-grid','serverName'=>$request->get('serverName'),'type'=>1], ['class' => 'btn btn-default']);?>
</div>
<br/><br/>

<?php
echo ChartDraw::drawLineChart('linechart', $this, $timezone, 'CPU Utilization', 'CPU Utilization Percentage(%)', '%', $data);
