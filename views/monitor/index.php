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
        	<?=$form->field($server, 'serverName')->dropDownList(ArrayHelper::map($data, 'serverName', 'serverName'), ['options' => [$serverName => ['Selected' => true]]])->label(false)?>
        <?php ActiveForm::end()?>
    </div>

	<div class="right server-icon">
		<i class="iconfont iconfont-blue icon-server"></i>
	</div>
</div>

<br style="clear: both" />
<div class="text-center">
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('cpu-gauge', 'CPU', 0, 100, 0, '%');?>
		<a
			href="<?= Url::to(['monitor/cpu-chart', 'serverName' =>  $serverName]) ?>">View
			Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('ram-gauge', 'RAM', 0, 100, 0, '%');?>
		<a
			href="<?= Url::to(['monitor/ram-chart', 'serverName' =>  $serverName]) ?>">View
			Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('disk-gauge', 'DISK', 0, 100, 0, '%');?>
		<a
			href="<?= Url::to(['monitor/disk-chart', 'serverName' =>  $serverName])?>">View
			Details</a>
	</div>
	<div class="gauge left">
		<?php echo ChartDraw::drawGauge('load-gauge', 'LOAD', 0, 100, 0, '<br/>');?>
		<a
			href="<?= Url::to(['monitor/load-chart', 'serverName' =>  $serverName])?>">View
			Details</a>
	</div>
</div>
<br style="clear: both" />
<br />

<div>
<button id="btn-fullscreen" class="btn btn-default right"><i class="iconfont iconfont-blue icon-fullscreen"></i></button>
<?php
echo Highcharts::widget([
    'id' => 'heat-map',
    'scripts' => [
        'modules/heatmap',
    ],
    
    'options' => [
        'chart' => [
            'type' => 'heatmap',
            'marginTop' => 40,
            'height' => 400,
        ],
        'title' => [
            'text' => 'Utilization of Servers'
        ],
        'credits' => [
            'enabled' => false
        ],
        'xAxis' => [
            'categories' => $xCategories
        ],
        'yAxis' => [
            'categories' => [
                'CPU',
                'RAM',
                'DISK',
                'LOAD'
            ],
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
            ]
        ],
        'legend' => [
            'align' => 'right',
            'layout' => 'vertical',
            'margin' => 0,
            'verticalAlign' => 'top',
            'y' => 23,
            'symbolHeight' => 320
        ],
        'tooltip' => [
            'formatter' => new JsExpression('function () {
                return "<b>" + this.series.yAxis.categories[this.point.y] + "</b> of <b>" +
                 this.series.xAxis.categories[this.point.x]+ "</b><br> is <b>" + this.point.value + "</b>";
            }')
        ],
        'plotOptions' => [
            'series' => [
                'events' => [
                    'click' => new JsExpression('function(event){
                                        var x = this.xAxis.categories[event.point.x];
                                        var y = this.yAxis.categories[event.point.y];
                                        switch(y){
                                            case "CPU":
                                                location.href="index.php?r=monitor/cpu-chart&serverName="+x;
                                                break;
                                            case "RAM":
                                                location.href="index.php?r=monitor/ram-chart&serverName="+x;
                                                break;
                                            case "DISK":
                                                location.href="index.php?r=monitor/disk-chart&serverName="+x;
                                                break;
                                            case "LOAD":
                                                location.href="index.php?r=monitor/load-chart&serverName="+x;
                                                break;
                                        }
                                    }')
                ]
            ]
        ],
        'series' => [
            [
                'name' => 'Utiliztion of Servers',
                'borderWidth' => 1,
                'data' => $heatData,
                'dataLabels' => [
                    'enabled' => true,
                    'color' => '#000000'
                ]
            ]
        ]
    ]
]);
?>
</div>

<?php
$this->registerJs("
    $('#server-servername').change(function(){
        var server = $('#server-servername option:selected').text();
        location.href='index.php?r=monitor/index&serverName='+server;
    });
    var updateGuage = function updateGuageChart() {
        var gaugeChart1 = $('#cpu-gauge').highcharts();
        var point1 = gaugeChart1.series[0].points[0];
        var gaugeChart2 = $('#ram-gauge').highcharts();
        var point2 = gaugeChart2.series[0].points[0];
        var gaugeChart3 = $('#disk-gauge').highcharts();
        var point3 = gaugeChart3.series[0].points[0];
        var gaugeChart4 = $('#load-gauge').highcharts();
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
            var heatMap = $('#heat-map').highcharts();
            var series = heatMap.series[0];
            var obj = eval(data);
            series.setData(obj);
        })
    }
    setInterval(updateHeat,1000);
    var fullscreen = document.getElementById('btn-fullscreen');
    var heatMap = document.getElementById('heat-map');
    if (fullscreen && heatMap) {
        fullscreen.addEventListener('click', function (evt) {
            if (heatMap.requestFullscreen) {
                heatMap.requestFullscreen();
            }
            else if (heatMap.msRequestFullscreen) {
                heatMap.msRequestFullscreen();
            }
            else if (heatMap.mozRequestFullScreen) {
                heatMap.mozRequestFullScreen();
            }
            else if (heatMap.webkitRequestFullScreen) {
                heatMap.webkitRequestFullScreen();
            }
        }, false);
    }
    ");
?>