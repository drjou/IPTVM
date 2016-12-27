<?php
use app\models\ChartDraw;
use yii\helpers\Html;
$this->title = 'Load Chart';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$request = Yii::$app->request;
?>

<div class="btn-group right">
	<?= Html::a('<i class="iconfont icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
	<?= Html::a('<i class="iconfont icon-grid"></i>', ['load-grid','serverName'=>$request->get('serverName')], ['class' => 'btn btn-default']);?>
</div>
<br/><br/>

<?php
echo ChartDraw::drawLineChart('Load Utilization', 'Click and drag to zoom in', 'Load Utilization', '', $data);
