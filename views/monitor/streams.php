<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\ChartDraw;
$this->title = 'Streams Monitor';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs("
    Highcharts.setOptions({
    global:{
    useUTC:false
}});");

$operation = 
    'var obj = eval(data);
     for(var i=0;i<obj.length;i++){
        var id;
        switch(i){
            case 0: id="#w2"; break;
            case 1: id="#w3"; break;
        }
        for(var j=0;j<obj[i].length;j++){
            var series=$(id).highcharts().series[j];
            series.setData(obj[i][j].data);
        }
     }';
?>

<div class="left">
    <?php $form = ActiveForm::begin(); ?>
    	<?= $form->field($server, 'serverName')->dropDownList(ArrayHelper::map($allServer,'serverName','serverName'), ['options'=>[$serverName=>['Selected'=>true]]])->label(false) ?>
    <?php ActiveForm::end() ?>
</div>

<?php 
    ChartDraw::drawDateRange($serverName, 'Streams', $range, $minDate, $operation, '#w1');
?>

<div class="btn-group right">
    <?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
    <?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['streams-grid'], ['class' => 'btn btn-default']);?>
</div>

<?php
echo ChartDraw::drawLineChart('Total Utilization of Process', 'Click and drag to zoom in', 'Total Utilization Percentage of Process(%)', '%', $totalData);
?>
<br/><br/>

<?php
echo ChartDraw::drawLineChart('Memory Utilization of Process', 'Click and drag to zoom in', 'Memory Utilization Percentage of Process(%)', '%', $memoryData);
$this->registerJs("
    $('#server-servername').change(function(){
    var server = $('#server-servername option:selected').text();
    location.href='index.php?r=monitor/streams&serverName='+server;
});");