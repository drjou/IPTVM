<?php
use yii\helpers\Url;
use app\models\ChartDraw;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
$this->title = 'IPTV Monitor';
$this->params['breadcrumbs'][] = $this->title;
$servers = [
    '1001' => 'Server1',
    '1002' => 'Server2',
    '1003' => 'Server3',
    '1004' => 'Server4',
    '1005' => 'Server5',
];
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

<div style="float: right">
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($server, 'serverName')->dropDownList(ArrayHelper::map($data, 'id', 'serverName')) ?>

<?php ActiveForm::end() ?>
</div>
<br style="clear: both"/>
<div class="center">
	<div class="left" >
		<?php echo ChartDraw::drawGauge('CPU', 0, 100, 98.1, '%');?>
		<a href="<?= Url::to(['monitor/cpu-chart']) ?>">View Details</a>
	</div>
	<div class="left">
		<?php echo ChartDraw::drawGauge('RAM', 0, 100, 10.9, '%');?>
		<a href="<?= Url::to(['monitor/ram-chart']) ?>">View Details</a>
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

