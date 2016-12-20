<?php
use yii\helpers\Html;
use app\models\ChartDraw;
$this->title = 'RAM Chart';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard','url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$xCatagories = [
    '08:00:00',
    '08:05:00',
    '08:10:00',
    '08:15:00',
    '08:20:00',
    '08:25:00',
    '08:30:00',
    '08:35:00',
    '08:40:00',
    '08:45:00'
];
$data = [
    [
        //CPU总使用的时间百分比
        'name' => 'util',
        'data' => [
            20.4,
            45.1,
            19.4,
            49.1,
            19.4,
            69.2,
            41.2,
            20.9,
            71.3,
            10.9
        ]
    ]
]

?>

<div style="float: right">
<?= Html::a('Chart', null, ['class' => 'btn btn-default']);?>
<?= Html::a('Grid', ['ram-grid'], ['class' => 'btn btn-default']);?><br/>
</div>
<?php
echo ChartDraw::drawLineChart('RAM Utilization Percentage', '', $xCatagories, 'Percentage(%)', '%', $data);