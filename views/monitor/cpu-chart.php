<?php
use app\models\ChartDraw;
use yii\helpers\Html;
$this->title = 'CPU Chart';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$request = Yii::$app->request;
?>
<div style="float: right">
<?= Html::a('Chart', null, ['class' => 'btn btn-default']);?>
<?= Html::a('Grid', ['cpu-grid','serverName'=>$request->get('serverName')], ['class' => 'btn btn-default']);?><br/>
</div>
<?php
echo ChartDraw::drawLineChart('CPU Utilization', $request->get('serverName'), 'Percentage(%)', '%', $data);