<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\ChartDraw;
$this->title = 'Streams Monitor';
$this->params['breadcrumbs'][] = $this->title;
?>
<div style="float: left">
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($server, 'serverName')->dropDownList(ArrayHelper::map($allServer,'serverName','serverName'), ['options'=>[$serverName=>['Selected'=>true]]])->label(false) ?>

<?php ActiveForm::end() ?>
</div>
<div style="float: right">
<?= Html::a('Chart', null, ['class' => 'btn btn-default']);?>
<?= Html::a('Grid', ['streams-grid'], ['class' => 'btn btn-default']);?><br/>
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