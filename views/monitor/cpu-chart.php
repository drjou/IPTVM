<?php
use app\models\ChartDraw;
$this->title = 'CPU Chart';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
echo ChartDraw::drawLineChart('CPU Ultilization Percentage', '', [
    '05:00:00',
    '05:00:05',
    '05:00:10',
    '05:00:15',
    '05:00:20',
    '05:00:25',
    '05:00:30',
    '05:00:35',
    '05:00:40',
    '05:00:45'
], 'Percentage(%)', '%', [
    [
        'name' => 'util',
        'data' => [
            50.4,
            30.1,
            20.5,
            10.6,
            15.9,
            26.4,
            59.8,
            76.2,
            80.6,
            90.5
        ]
    ]
]);