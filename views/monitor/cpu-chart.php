<?php
use app\models\ChartDraw;
use yii\helpers\Html;
$this->title = 'CPU Chart';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$request = Yii::$app->request;
$this->registerJs("
    Highcharts.setOptions({
    global:{
    useUTC:false
}});");
?>

<?php 
    ChartDraw::drawDateRange($request->get('serverName'), 'CPU', $range, $minDate);
?>
    
<div class="btn-group right">
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['cpu-grid','serverName'=>$request->get('serverName')], ['class' => 'btn btn-default']);?>
</div>
<br/><br/>

<?php
echo ChartDraw::drawLineChart('CPU Utilization', 'Click and drag to zoom in', 'CPU Utilization Percentage(%)', '%', $data);