<?php
use app\models\ChartDraw;
use yii\helpers\Html;
$this->title = 'Load Chart';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$request = Yii::$app->request;
?>
<div style="float: right">
<?= Html::a('Chart', null, ['class' => 'btn btn-default']);?>
<?= Html::a('Grid', ['load-grid','serverName'=>$request->get('serverName')], ['class' => 'btn btn-default']);?><br/>
</div>
<?php
echo ChartDraw::drawLineChart('Load Utilization', 'Click and drag to zoom in', 'Load Utilization Percentage(%)', '%', $data);
