<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\ChartDraw;
$this->title = 'Streams Monitor';
$this->params['breadcrumbs'][] = $this->title;

$operation = 'var time = $("#date-range").val().split(" - ");
                var startTime = Date.parse(new Date(time[0]));
                var endTime = Date.parse(new Date(time[1]));
                $("#total-chart").highcharts().showLoading();
                $("#memory-chart").highcharts().showLoading();
                $.get("index.php?r=monitor/update-line-info&serverName='.$serverName.'&type=Streams&startTime="+startTime+"&endTime="+endTime,
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

<div class="left">
    <?php $form = ActiveForm::begin(); ?>
    	<?= $form->field($server, 'serverName')->dropDownList($servers, ['options'=>[$serverName=>['Selected'=>true]]])->label(false) ?>
    <?php ActiveForm::end() ?>
</div>

<?php 
    ChartDraw::drawDateRange($range, $minDate, $operation);
?>

<div class="btn-group right">
    <?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
    <?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['streams-grid','type'=>1], ['class' => 'btn btn-default']);?>
</div>

<?php
echo ChartDraw::drawLineChart('total-chart', $this, 'Total Utilization of Process', 'Total Utilization Percentage of Process(%)', '%', $totalData);
?>
<br/><br/>

<?php
echo ChartDraw::drawLineChart('memory-chart', $this, 'Memory Utilization of Process', 'Memory Utilization Percentage of Process(%)', '%', $memoryData);
$this->registerJs("
    $('#server-servername').change(function(){
    var server = $('#server-servername option:selected').text();
    location.href='index.php?r=monitor/streams&serverName='+server;
});");