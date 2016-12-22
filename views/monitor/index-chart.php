<?php
use yii\helpers\Html;
use app\models\ChartDraw;
$this->title = 'IPTV Monitor';
$this->params['breadcrumbs'][] = $this->title;
$request = Yii::$app->request;
?>
<div style="float: left">
<?= Html::a('Gauge', ['index','serverName'=>$request->get('serverName')], ['class' => 'btn btn-default']);?>
<?= Html::a('Line', null, ['class' => 'btn btn-default']);?><br/>
</div>
<?php
echo ChartDraw::drawLineChart('General Utilization', $request->get('serverName'), 'Percentage(%)', '%', $data);