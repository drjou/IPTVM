<?php
use yii\helpers\Html;
use app\models\ChartDraw;
$this->title = 'RAM Chart';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard','url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$request = Yii::$app->request;
$operation = 'function() {
                var time = $("#w0").val().split(" - ");
                var startTime = Date.parse(new Date(time[0]));
                var endTime = Date.parse(new Date(time[1]));
                $("#linechart").highcharts().showLoading();
                $.get("index.php?r=monitor/update-line-info&serverName='.$request->get('serverName').'&type=RAM&startTime="+startTime+"&endTime="+endTime,
                        function(data,status){
                            var obj = eval(data);
                            for(var i=0;i<obj.length;i++){
                                var series=$("#linechart").highcharts().series[i];
                                series.setData(obj[i].data);
                            }
                            $("#linechart").highcharts().hideLoading();
                        });
             }';
?>

<?php 
    ChartDraw::drawDateRange($range, $minDate, $operation);
?>

<div class="btn-group right">
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['ram-grid','serverName' => $request->get('serverName')], ['class' => 'btn btn-default']);?>
</div>
<br/><br/>

<?php
echo ChartDraw::drawLineChart('linechart', $this, 'RAM Utilization', 'RAM Utilization Percentage(%)', '%', $data);

