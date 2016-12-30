<?php
use yii\helpers\Url;
use app\models\ChartDraw;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use miloschuman\highcharts\Highcharts;
use yii\base\Widget;
use yii\web\JsExpression;
$this->title = 'IPTV Monitor';
$this->params['breadcrumbs'][] = $this->title;
?>

<div>
    <div class="right">
        <?php $form = ActiveForm::begin(); ?>
        	<?= $form->field($server, 'serverName')
        	    ->dropDownList(ArrayHelper::map($data,'serverName','serverName'), ['options'=>[$serverName=>['Selected'=>true]]])
        	    ->label(false) ?>
        <?php ActiveForm::end() ?>
    </div>
    
    <div class="right server-icon">
    	<i class="iconfont iconfont-blue icon-server"></i>
    </div>
</div>

<br style="clear: both"/>
<div class="text-center">
	<div class="gauge left" >
		<?php echo ChartDraw::drawGauge('CPU', 0, 100, 0, '%');?>
		<a href="<?= Url::to(['monitor/cpu-chart', 'serverName' =>  $serverName]) ?>">View Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('RAM', 0, 100, 0, '%');?>
		<a href="<?= Url::to(['monitor/ram-chart', 'serverName' =>  $serverName]) ?>">View Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('DISK', 0, 100, 0, '%');?>
		<a href="<?= Url::to(['monitor/disk-chart', 'serverName' =>  $serverName])?>">View Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('LOAD', 0, 100, 0, '<br/>');?>
		<a href="<?= Url::to(['monitor/load-chart', 'serverName' =>  $serverName])?>">View Details</a>
	</div>
</div>
<br style="clear: both"/>
<br/>
<?php 
    echo Highcharts::widget([
        'scripts' => [
            'modules/heatmap'
        ],

        'options' => [
        'chart' => [
            'type' => 'heatmap',
            'marginTop' => 40,
            'marginBottom' => 80
        ],
        'title' => [
            'text' => 'Utilization of Servers'
        ],
        'credits' => [
            'enabled' => false
        ],
        'xAxis' => [
            'categories' => $xCatagories
        ],
        'yAxis' => [
            'categories' => ['CPU', 'RAM', 'DISK', 'LOAD'],
            'title' => null
        ],
        'colorAxis' => [
            'min' => 0,
            'max' => 100,
            'stops' => [
                        [
                            0.1,
                            '#55BF3B'
                        ], // green
                        [
                            0.5,
                            '#DDDF0D'
                        ], // yellow
                        [
                            0.9,
                            '#DF5353'
                        ]
                    ],
        ],
        'legend' => [
            'align' => 'right',
            'layout' => 'vertical',
            'margin' => 0,
            'verticalAlign' => 'top',
            'y' => 25,
            'symbolHeight' => 280
        ],
        'tooltip' => [
            'formatter' => new JsExpression('function () {
                return "<b>" + this.series.yAxis.categories[this.point.y] + "</b> of <b>" +
                 this.series.xAxis.categories[this.point.x]+ "</b><br> is <b>" + this.point.value + "</b>";
            }')
        ],
        'series' => [[
            'name' => 'Utiliztion of Servers',
            'borderWidth' => 1,
            'data' => $heatData,
            'dataLabels' => [
                'enabled' => true,
                'color' => '#000000'
            ]]
        ]
     ]]);
?>

<?php 
$this->registerJs("
    $('#server-servername').change(function(){
        var server = $('#server-servername option:selected').text();
        location.href='index.php?r=monitor/index&serverName='+server;
    });
    var updateGuage = function updateGuageChart() {
        var gaugeChart1 = $('#w1').highcharts();
        var point1 = gaugeChart1.series[0].points[0];
        var gaugeChart2 = $('#w2').highcharts();
        var point2 = gaugeChart2.series[0].points[0];
        var gaugeChart3 = $('#w3').highcharts();
        var point3 = gaugeChart3.series[0].points[0];
        var gaugeChart4 = $('#w4').highcharts();
        var point4 = gaugeChart4.series[0].points[0];
        var server = $('#server-servername option:selected').text();
        $.get('index.php?r=monitor/update-gauge-info&serverName='+server,function(data,status){
            point1.update(data.cpuInfo);
            point2.update(data.ramInfo);
            point3.update(data.diskInfo);
            point4.update(data.loadInfo);
        });
    }
    window.onload = updateGuage;
    setInterval(updateGuage,1000);
    var updateHeat = function updateHeatMap(){
        $.get('index.php?r=monitor/update-heat-map',function(data,status){
            var heatMap = $('#w5').highcharts();
            var series = heatMap.series[0];
            var obj = eval(data);
            series.setData(obj);
        })
    }
    setInterval(updateHeat,1000);
    ");
?>