<?php
use app\models\ChartDraw;
use yii\helpers\Html;
$request = Yii::$app->request;
$this->title = 'Status of MySQL';
$this->params['breadcrumbs'][] = ['label' => 'Servers Status', 'url' => ['servers-status']];
$this->params['breadcrumbs'][] = ['label' => 'Servers Detail', 'url' => ['server-detail', 'serverName'=>$request->get('serverName')]];
$this->params['breadcrumbs'][] = $this->title;

$operation = 'var time = $("#date-range").val().split(" - ");
                var startTime = Date.parse(new Date(time[0]));
                var endTime = Date.parse(new Date(time[1]));
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
	<?= Html::a('<i class="iconfont iconfont-blue icon-linechart"></i>', null, ['class' => 'btn btn-default']);?>
	<?= Html::a('<i class="iconfont iconfont-blue icon-grid"></i>', null, ['class' => 'btn btn-default']);?>
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