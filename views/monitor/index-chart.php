<?php
use yii\helpers\Html;
use app\models\ChartDraw;
$this->title = 'IPTV Monitor';
$this->params['breadcrumbs'][] = $this->title;
$request = Yii::$app->request;
?>

<div class="btn-group">
	<?= Html::a('<i class="iconfont icon-fw icon-dashboard"></i>', ['index','serverName'=>$request->get('serverName')], ['class' => 'btn btn-default']);?>
	<?= Html::a('<i class="iconfont icon-fw icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
</div>
<br/><br/>

<?php
echo ChartDraw::drawLineChart('General Utilization', 'Click and drag to zoom in', 'Percentage(%)', '%', $data);