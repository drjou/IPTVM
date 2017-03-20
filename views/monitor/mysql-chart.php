<?php
use app\models\ChartDraw;
use yii\helpers\Html;
use app\models\Timezone;
$request = Yii::$app->request;
$this->title = 'MySQL Chart';
$this->params['breadcrumbs'][]=['label'=>'IPTV Monitor', 'url'=>['index']];
$this->params['breadcrumbs'][] = ['label' => 'Servers Monitor', 'url' => ['servers-status']];
$this->params['breadcrumbs'][] = ['label' => 'Server Details', 'url' => ['server-detail', 'serverName'=>$request->get('serverName')]];
$this->params['breadcrumbs'][] = $this->title;
$timezone = Timezone::getCurrentTimezone();
$operation = 'var time = $("#date-range").val().split(" - ");
                var startTime = moment.tz(time[0], "'.$timezone->timezone.'").format("X");
                var endTime = moment.tz(time[1], "'.$timezone->timezone.'").format("X");
                $("#linechart").highcharts().showLoading();
                $.get("index.php?r=monitor/update-line-info&serverName='.$request->get('serverName').'&type=Mysql&startTime="+startTime+"&endTime="+endTime,
                        function(data,status){
                            var obj = eval(data);
                            for(var i=0;i<obj.length;i++){
                                var series=$("#linechart").highcharts().series[i];
                                series.setData(obj[i].data,false);
                            }
                            $("#linechart").highcharts().redraw();
                            $("#linechart").highcharts().hideLoading();
                        });';
    ChartDraw::drawDateRange($range, $minDate, $operation);
?>
<?=Html::dropDownList('serverName', $model, $servers, ['id'=>'server-servername','class' => 'form-control','style'=>'float:left;width:100px;margin-left:20px;margin-right:20px;']);?>
<?php echo '<span style="font-size:x-large;">Status:'.($status==1 ? '<i class="fa fa-circle" style="color:#5cb85c;"></i></span>' : '<i class="fa fa-circle" style="color:#d9534f;"></i></span>')?>

<div class="btn-group right">
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default', 'style'=>"background-color:#CCCCCC"]);?>
	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', ['mysql-info-grid', 'MysqlInfoSearch[server]'=>$request->get('serverName'), 'serverName'=>$request->get('serverName')], ['class' => 'btn btn-default']);?>
</div>
<br/><br/>

<?php
echo ChartDraw::drawLineChart('linechart', $this, 'Status of MySQL', 'Number', '', $data);

$this->registerJs("
    $(document).ready(function(){
        $('#server-servername').change(function(){
            var server = $('#server-servername option:selected').text();
            location.href='index.php?r=monitor/mysql-chart&serverName='+server;
        });
    });
");