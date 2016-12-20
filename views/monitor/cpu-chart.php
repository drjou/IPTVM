<?php
use app\models\ChartDraw;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
$this->title = 'CPU Chart';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$xCatagories = ArrayHelper::getColumn($cpuData, 'recordTime');
$data = [
    [
        'name' => 'utilize',
        'data' => ArrayHelper::getColumn($cpuData, function($element){
            return $element['utilize']+0;
        })
    ],
    [
        'name' => 'user',
        'data' => ArrayHelper::getColumn($cpuData, function($element){
            return $element['user']+0;
        })
    ],
    [
        'name' => 'system',
        'data' => ArrayHelper::getColumn($cpuData, function($element){
            return $element['system']+0;
        })
    ],
    [
        'name' => 'wait',
        'data' => ArrayHelper::getColumn($cpuData, function($element){
            return $element['wait']+0;
        })
    ],
    [
        'name' => 'hardIrq',
        'data' => ArrayHelper::getColumn($cpuData, function($element){
        return $element['hardIrq']+0;
        })
    ],
    [
        'name' => 'softIrq',
        'data' => ArrayHelper::getColumn($cpuData, function($element){
        return $element['softIrq']+0;
        })
    ],
    [
        'name' => 'nice',
        'data' => ArrayHelper::getColumn($cpuData, function($element){
        return $element['nice']+0;
        })
    ],
    [
        'name' => 'steal',
        'data' => ArrayHelper::getColumn($cpuData, function($element){
        return $element['steal']+0;
        })
    ],
    [
        'name' => 'guest',
        'data' => ArrayHelper::getColumn($cpuData, function($element){
        return $element['guest']+0;
        })
    ]
]
?>
<div style="float: right">
<?= Html::a('Chart', null, ['class' => 'btn btn-default']);?>
<?= Html::a('Grid', ['cpu-grid'], ['class' => 'btn btn-default']);?><br/>
</div>
<?php
echo ChartDraw::drawLineChart('CPU Utilization Percentage', '', $xCatagories, 'Percentage(%)', '%', $data);