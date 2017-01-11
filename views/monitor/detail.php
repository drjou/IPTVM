<?php
use app\models\ChartDraw;
use yii\helpers\Url;
$this->title = 'Server Details';
$this->params['breadcrumbs'][] = ['label' => 'Server Monitor', 'url' => ['servers']];
$this->params['breadcrumbs'][] = $this->title;

$request = Yii::$app->request;
?>

<div class="text-center">
	<div class="gauge left">
    	<?php echo ChartDraw::drawGauge('cpu-gauge', 'CPU', 0, 100, 0, '%');?>
    		<a
			href="<?= Url::to(['monitor/cpu-chart', 'serverName' =>  $request->get('serverName')]) ?>">View
			Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('ram-gauge', 'RAM', 0, 100, 0, '%');?>
		<a
			href="<?= Url::to(['monitor/ram-chart', 'serverName' =>  $request->get('serverName')]) ?>">View
			Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('disk-gauge', 'DISK', 0, 100, 0, '%');?>
		<a
			href="<?= Url::to(['monitor/disk-chart', 'serverName' =>  $request->get('serverName')])?>">View
			Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('load-gauge', 'LOAD', 0, 100, 0, '<br/>');?>
		<a
			href="<?= Url::to(['monitor/load-chart', 'serverName' =>  $request->get('serverName')])?>">View
			Details</a>
	</div>
</div>

<?php
$serverName = $request->get('serverName');
$this->registerJs("
    var updateGuage = function updateGuageChart() {
        var gaugeChart1 = $('#cpu-gauge').highcharts();
        var point1 = gaugeChart1.series[0].points[0];
        var gaugeChart2 = $('#ram-gauge').highcharts();
        var point2 = gaugeChart2.series[0].points[0];
        var gaugeChart3 = $('#disk-gauge').highcharts();
        var point3 = gaugeChart3.series[0].points[0];
        var gaugeChart4 = $('#load-gauge').highcharts();
        var point4 = gaugeChart4.series[0].points[0];
        $.get('index.php?r=monitor/update-gauge-info&serverName=$serverName',function(data,status){
            point1.update(data.cpuInfo);
            point2.update(data.ramInfo);
            point3.update(data.diskInfo);
            point4.update(data.loadInfo);
        });
    }
    window.onload = updateGuage;
    setInterval(updateGuage,1000);
    ");
?>