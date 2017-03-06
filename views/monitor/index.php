<?php 
use yii\grid\GridView;
use yii\helpers\Html;
$this->title = 'Monitor Dashboard';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">Servers' Status
				<span class="label label-success">UP</span>
                <span class="label label-danger">DOWN</span></h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?= GridView::widget([
                        'options' => ['class' => 'gridview', 'style' => 'overflow:auto', 'id' => 'grid'],
                        'dataProvider' => $servers['dataProvider'],
                        'filterModel' => $servers['searchModel'],
                        'pager' => [
                            'firstPageLabel' => 'First Page',
                            'lastPageLabel' => 'Last Page',
                        ],
                        'rowOptions' => function($model, $key, $index, $grid){
                            return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
                        },
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'headerOptions' => ['width' => '10'],
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => function($model){
                                    return $model->status == 1 ? '<i class="fa fa-circle" style="color:#5cb85c;"></i>' : '<i class="fa fa-circle" style="color:#d9534f;"></i>';
                                },
                                'headerOptions' => ['width' => '10'],
                                'filter' => $filter,
                            ],
                            'serverName',
                            'serverIp',
                            'operatingSystem',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Operations',
                                'headerOptions' => ['width' => '10'],
                                'template' => '&nbsp;&nbsp;&nbsp;{view}',
                                'buttons' => [
                                    'view' => function($url, $model, $key){
                                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                                        ['detail', 'serverName' => $key],
                                        ['title' => 'View']);
                                    },
                                ],
                            ],
                        ],
                    ]); 
                    ?>
				</div>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">Streams' Status
				<span class="label label-success">UP</span>
                <span class="label label-warning">UNKNOWN</span>
                <span class="label label-danger">DOWN</span></h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?= GridView::widget([
                        'options' => ['class' => 'gridview', 'style' => 'overflow:auto', 'id' => 'grid'],
                        'dataProvider' => $streams['dataProvider'],
                        'filterModel' => $streams['searchModel'],
                        'pager' => [
                            'firstPageLabel' => 'First Page',
                            'lastPageLabel' => 'Last Page',
                        ],
                        'rowOptions' => function($model, $key, $index, $grid){
                            return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
                        },
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'headerOptions' => ['width' => '10'],
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => function($model){
                                    if($model->serverInfo->status == 0) return '<i class="fa fa-circle" style="color:#f0ad4e;"></i>';
                                    return $model->status == 1 ? '<i class="fa fa-circle" style="color:#5cb85c;"></i>' : '<i class="fa fa-circle" style="color:#d9534f;"></i>';
                                },
                                'headerOptions' => ['width' => '10'],
                                'filter' => $filter,
                            ],
                            'streamName',
                            'source',
                            [
                                'attribute' => 'sourceStatus',
                                'format' => 'html',
                                'value' => function($model){
                                    if($model->serverInfo->status == 0) return '<i class="fa fa-circle" style="color:#f0ad4e;"></i>';
                                    return $model->sourceStatus == 1 ? '<i class="fa fa-circle" style="color:#5cb85c;"></i>' : '<i class="fa fa-circle" style="color:#d9534f;"></i>';
                                },
                                'headerOptions' => ['width' => '10'],
                                'filter' => $filter,
                            ],
                            [
                                'attribute' => 'server',
                                'format' => 'raw',
                                'value' => function($model){
                                    return Html::a($model->server, ['monitor/server', 'serverName' => $model->server]);
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Operations',
                                'headerOptions' => ['width' => '120'],
                                'template' => '{view}&nbsp;&nbsp;&nbsp;{switch}&nbsp;&nbsp;&nbsp;{restart}&nbsp;&nbsp;&nbsp;{play}',
                                'buttons' => [
                                    'view' => function($url, $model, $key){
                                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                                        ['view', 'streamName' => $key],
                                        ['title' => 'View']);
                                    },
                                    'switch' => function($url, $model, $key){
                                    if($model->status == 0) return Html::a('<i class="fa fa-power-off" style="color:#5cb85c;"></i>',
                                        ['switch', 'streamName' => $key],
                                        ['title' => 'Start']);
                                    return Html::a('<i class="fa fa-power-off" style="color:#d9534f;"></i>',
                                        ['switch', 'streamName' => $key],
                                        ['title' => 'Stop']);
                                    },
                                    'restart' => function($url, $model, $key){
                                        if($model->status == 0) return '<span class="fa fa-refresh" style="color:gray;"></span>';
                                        return Html::a('<span class="fa fa-refresh"></span>',
                                        ['restart', 'streamName' => $key],
                                        ['title' => 'Restart']);
                                    },
                                    'play' => function($url, $model, $key){
                                        if($model->status == 0) return '<span class="fa fa-play-circle" style="color:gray;"></span>';
                                        return Html::a('<span class="fa fa-play-circle"></span>',
                                        ['play', 'streamName' => $key],
                                        ['title' => 'Delete',]);
                                    },
                                ],
                            ],
                        ],
                    ]); 
                    ?>
				</div>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->
</div>
<div class="row">
	<div class="col-lg-6">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">MySQLs' Status
				<span class="label label-success">UP</span>
                <span class="label label-warning">UNKNOWN</span>
                <span class="label label-danger">DOWN</span></h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?= GridView::widget([
                        'options' => ['class' => 'gridview', 'style' => 'overflow:auto', 'id' => 'grid'],
                        'dataProvider' => $mysqls['dataProvider'],
                        'filterModel' => $mysqls['searchModel'],
                        'pager' => [
                            'firstPageLabel' => 'First Page',
                            'lastPageLabel' => 'Last Page',
                        ],
                        'rowOptions' => function($model, $key, $index, $grid){
                            return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
                        },
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'headerOptions' => ['width' => '10'],
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => function($model){
                                    if($model->serverInfo->status == 0) return '<i class="fa fa-circle" style="color:#f0ad4e;"></i>';
                                    return $model->status == 1 ? '<i class="fa fa-circle" style="color:#5cb85c;"></i>' : '<i class="fa fa-circle" style="color:#d9534f;"></i>';
                                },
                                'headerOptions' => ['width' => '10'],
                                'filter' => $filter,
                            ],
                            [
                                'attribute' => 'server',
                                'format' => 'raw',
                                'value' => function($model){
                                    return Html::a($model->server, ['monitor/server', 'serverName' => $model->server]);
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Operations',
                                'headerOptions' => ['width' => '120'],
                                'template' => '{view}&nbsp;&nbsp;&nbsp;{switch}&nbsp;&nbsp;&nbsp;{restart}',
                                'buttons' => [
                                    'view' => function($url, $model, $key){
                                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                                        ['view', 'sever' => $key],
                                        ['title' => 'View']);
                                    },
                                    'switch' => function($url, $model, $key){
                                    if($model->status == 0) return Html::a('<i class="fa fa-power-off" style="color:#5cb85c;"></i>',
                                        ['switch', 'server' => $key],
                                        ['title' => 'Start']);
                                    return Html::a('<i class="fa fa-power-off" style="color:#d9534f;"></i>',
                                        ['switch', 'server' => $key],
                                        ['title' => 'Stop']);
                                    },
                                    'restart' => function($url, $model, $key){
                                        if($model->status == 0) return '<span class="fa fa-refresh" style="color:gray;"></span>';
                                        return Html::a('<span class="fa fa-refresh"></span>',
                                        ['restart', 'server' => $key],
                                        ['title' => 'Restart']);
                                    },
                                ],
                            ],
                        ],
                    ]); 
                    ?>
				</div>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-6 -->
	<div class="col-lg-6">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">Nginxes' Status
				<span class="label label-success">UP</span>
                <span class="label label-warning">UNKNOWN</span>
                <span class="label label-danger">DOWN</span></h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?= GridView::widget([
                        'options' => ['class' => 'gridview', 'style' => 'overflow:auto', 'id' => 'grid'],
                        'dataProvider' => $nginxes['dataProvider'],
                        'filterModel' => $nginxes['searchModel'],
                        'pager' => [
                            'firstPageLabel' => 'First Page',
                            'lastPageLabel' => 'Last Page',
                        ],
                        'rowOptions' => function($model, $key, $index, $grid){
                            return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
                        },
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'headerOptions' => ['width' => '10'],
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => function($model){
                                    if($model->serverInfo->status == 0) return '<i class="fa fa-circle" style="color:#f0ad4e;"></i>';
                                    return $model->status == 1 ? '<i class="fa fa-circle" style="color:#5cb85c;"></i>' : '<i class="fa fa-circle" style="color:#d9534f;"></i>';
                                },
                                'headerOptions' => ['width' => '10'],
                                'filter' => $filter,
                            ],
                            [
                                'attribute' => 'server',
                                'format' => 'raw',
                                'value' => function($model){
                                    return Html::a($model->server, ['monitor/server', 'serverName' => $model->server]);
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Operations',
                                'headerOptions' => ['width' => '120'],
                                'template' => '{view}&nbsp;&nbsp;&nbsp;{switch}&nbsp;&nbsp;&nbsp;{restart}',
                                'buttons' => [
                                    'view' => function($url, $model, $key){
                                    return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                                        ['view', 'sever' => $key],
                                        ['title' => 'View']);
                                    },
                                    'switch' => function($url, $model, $key){
                                    if($model->status == 0) return Html::a('<i class="fa fa-power-off" style="color:#5cb85c;"></i>',
                                        ['switch', 'server' => $key],
                                        ['title' => 'Start']);
                                    return Html::a('<i class="fa fa-power-off" style="color:#d9534f;"></i>',
                                        ['switch', 'server' => $key],
                                        ['title' => 'Stop']);
                                    },
                                    'restart' => function($url, $model, $key){
                                        if($model->status == 0) return '<span class="fa fa-refresh" style="color:gray;"></span>';
                                        return Html::a('<span class="fa fa-refresh"></span>',
                                        ['restart', 'server' => $key],
                                        ['title' => 'Restart']);
                                    },
                                ],
                            ],
                        ],
                    ]); 
                    ?>
				</div>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-6 -->
</div>
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">Online Clients
				<span class="label label-success">UP</span>
                <span class="label label-warning">UNKNOWN</span></h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
                    <?= GridView::widget([
                        'options' => ['class' => 'gridview', 'style' => 'overflow:auto', 'id' => 'grid'],
                        'dataProvider' => $onlineClients['dataProvider'],
                        'filterModel' => $onlineClients['searchModel'],
                        'pager' => [
                            'firstPageLabel' => 'First Page',
                            'lastPageLabel' => 'Last Page',
                        ],
                        'rowOptions' => function($model, $key, $index, $grid){
                            return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
                        },
                        'columns' => [
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'headerOptions' => ['width' => '10'],
                            ],
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => function($model){
                                    if($model->serverInfo->status == 0) return '<i class="fa fa-circle" style="color:#f0ad4e;"></i>';
                                    return '<i class="fa fa-circle" style="color:#5cb85c;"></i>';
                                },
                                'headerOptions' => ['width' => '10'],
                            ],
                            'accountId',
                            'Ip',
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'header' => 'Operations',
                                'headerOptions' => ['width' => '10'],
                                'template' => '{view}&nbsp;&nbsp;&nbsp;{enable}',
                                'buttons' => [
                                    'view' => function($url, $model, $key){
                                        return Html::a('<i class="glyphicon glyphicon-eye-open"></i>',
                                            ['client-monitor', 'OnlineClientSearch[accountId]' => $key['accountId']],
                                            ['title' => 'View']);
                                    },
                                ],
                            ],
                        ],
                    ]); 
                    ?>
				</div>
			</div>
			<!-- /.panel-body -->
		</div>
		<!-- /.panel -->
	</div>
	<!-- /.col-lg-12 -->
</div>