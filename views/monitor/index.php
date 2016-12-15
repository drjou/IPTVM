<?php
use yii\helpers\Url;
use app\models\ChartDraw;
$this->title = 'IPTV Monitor';
$this->params['breadcrumbs'][] = $this->title;
?>

<head>
	<style type="text/css">
        .center{
        	text-align: center;
        }
        .center .left{
        	float:left;
        	width: 250px;       
        }
    </style>
</head>

<div class="center">
	<div class="left" >
		<?php echo ChartDraw::drawGauge('CPU', 0, 100, 98.1, '%');?>
		<a href="<?= Url::to(['monitor/cpu-chart']) ?>">View Details</a>
	</div>
	<div class="left">
		<?php echo ChartDraw::drawGauge('RAM', 0, 100, 10.9, '%');?>
		<a>View Details</a>
	</div>
	<div class="left">
		<?php echo ChartDraw::drawGauge('DISK', 0, 100, 30.6, '%');?>
		<a>View Details</a>
	</div>
	<div class="left">
		<?php echo ChartDraw::drawGauge('LOAD', 0, 100, 70.5, '%');?>
		<a>View Details</a>
	</div>
</div>
