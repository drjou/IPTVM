<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\data\ArrayDataProvider;
$this->title = 'CPU Grid';
$this->params['breadcrumbs'][] = ['label' => 'Monitor Dashboard', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


?>

<div style="float: right">
<?= Html::a('Chart', ['cpu-chart'], ['class' => 'btn btn-default']);?>
<?= Html::a('Grid', null, ['class' => 'btn btn-default']);?><br/>
</div><br/>
<?php echo GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'rowOptions' => function($model, $key, $index, $grid){
        return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
    },
    'columns' => [
        [
            'class' => 'yii\grid\SerialColumn',
            'headerOptions' => ['width' => '10'],
        ],
        'ncpu',
        [
            'attribute' => 'recordTime',
            'headerOptions' => ['width' => '155'],
        ],
        'utilize',
        'user', 
        'system', 
        'wait', 
        'hardIrq', 
        'softIrq', 
        'nice', 
        'steal', 
        'guest'
    ]
]);
?>
<p>
	<strong>util(%):</strong>CPU总使用的时间百分比<br/>
	<strong>hirq(%):</strong>系统处理硬中断所花费的时间百分比<br/>
	<strong>sirq(%):</strong>系统处理软中断所花费的时间百分比<br/>
	<strong>nice(%):</strong>系统调整进程优先级所花费的时间百分比<br/>
	<strong>user(h):</strong>CPU执行用户进程的时间<br/>
	<strong>sys(h):</strong>CPU在内核运行时间<br/>
	<strong>wait(h):</strong>CPU在等待I/O操作完成所花费的时间<br/>
	<strong>steal(h):</strong>被强制等待（involuntary wait）虚拟CPU的时间<br/>
	<strong>ncpu:</strong>CPU的总个数<br/>
</p>

