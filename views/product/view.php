<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
$this->title = 'Product ' . $model->productName;
$this->params['breadcrumbs'][] = ['label' => 'Product List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">Details</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
					<?= DetailView::widget([
                        'model' => $model,
					    'template' => function ($attribute, $index, $widget){
					         if($index%2 == 0){
					             return '<tr class="label-white"><th>' . $attribute['label'] . '</th><td>' . $attribute['value'] . '</td></tr>';
					         }else{
					             return '<tr class="label-grey"><th>' . $attribute['label'] . '</th><td>' . $attribute['value'] . '</td></tr>';
					         }
					    },
                        'attributes' => [
                            'productName',
                        ],
                    ]) ?>
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
				<h5 style="font-weight: bold;">Accounts Belongs</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
					<?= GridView::widget([
                        'dataProvider' => $accountProvider,
                        'pager' => [
                            'firstPageLabel' => 'First Page',
                            'lastPageLabel' => 'Last Page',
                        ],
					    'rowOptions' => function($model, $key, $index, $grid){
					         return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
					    },
                        'columns' =>[
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'headerOptions' => ['width' => '10'],
					        ],
                            [
                                'attribute'=>"accountId",
                                'format'=>'raw',
                                'value'=> function($model){
                                    //超链接
                                    return Html::a($model->accountId, ['account/view', 'accountId' => $model->accountId], ['class' => 'profile-link','title' => 'view']);
                                }
                            ],
                            'state',
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
				<h5 style="font-weight: bold;">Channels Has</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
					<?= GridView::widget([
                        'dataProvider' => $channelProvider,
                        'pager' => [
                            'firstPageLabel' => 'First Page',
                            'lastPageLabel' => 'Last Page',
                        ],
					    'rowOptions' => function($model, $key, $index, $grid){
					        return ['class' => $index % 2 == 0 ? 'label-white' : 'label-grey' ];
					    },
                        'columns' =>[
                            [
                                'class' => 'yii\grid\SerialColumn',
                                'headerOptions' => ['width' => '10'],
					        ],
                            [
                                'attribute'=>'channelName',
                                'format'=>'raw',
                                'value'=> function($model){
                                    //超链接
                                    return Html::a($model->channelName, ['channel/view', 'channelId' => $model->channelId], ['class' => 'profile-link','title' => 'view']);
                                }
                            ],
                            'channelIp',
                            'channelType',
                            [
                                'attribute' => 'languageName',
                                'value' => 'language.languageName',
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
<p>
    <?= Html::a('Back to Product List', ['index'], ['class' => 'btn btn-primary']) ?>
</p>
