<?php
use app\models\ChartDraw;
$this->title = 'Server Monitor';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
echo ChartDraw::drawLineChart('CPU Utilization', 'Click and drag to zoom in', 'CPU Utilization Percentage(%)', '%', $cpuData);
echo ChartDraw::drawLineChart('RAM Utilization', 'Click and drag to zoom in', 'RAM Utilization Percentage(%)', '%', $ramData);
echo ChartDraw::drawLineChart('Disk Utilization', 'Click and drag to zoom in', 'Free Percentage of Disk(%)', '%', $diskData);
echo ChartDraw::drawLineChart('Load Utilization', 'Click and drag to zoom in', 'Load Utilization Percentage(%)', '%', $loadData);