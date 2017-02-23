<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
$this->title = 'Account ' . $model->accountId;
$this->params['breadcrumbs'][] = ['label' => 'Account List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$states = [
    '1001' => 'activated',
    '1002' => 'need activate',
    '1003' => 'need recharge',
    '1004' => 'purchase activated',
];
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
                            'accountId',
                            [
                                'attribute' => 'state',
                                'value' => $states[$model->state],
					        ],
                            [
                                'attribute' => 'enable',
                                'format' => 'html',
                                'value' => $model->enable == 1 ? '<i class="glyphicon glyphicon-ok" style="color:green;"></i>' : '<i class="glyphicon glyphicon-remove" style="color:red;"></i>',
				            ],
                            'createTime',
                            'updateTime',
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
				<h5 style="font-weight: bold;">Products Pre-Binds</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
					<?= GridView::widget([
                        'dataProvider' => $bindProvider,
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
                                'attribute'=>"productName",
                                'format'=>'raw',
                                'value'=> function($model){
                                    //超链接
                                    return Html::a($model->product->productName, ['product/view', 'productId' => $model->productId], ['class' => 'profile-link','title' => 'view']);
                                }
                            ],
					        'bindDay',
					        [
    					        'attribute'=>"isActive",
    					        'value'=> function($model){
    					           return $model->isActive ? 'yes' : 'no';
    					        }
					        ],
					        'activeDate',
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
				<h5 style="font-weight: bold;">Products Has</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
					<?= GridView::widget([
                        'dataProvider' => $productProvider,
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
                                'attribute'=>"productName",
                                'format'=>'raw',
                                'value'=> function($model){
                                    //超链接
                                    return Html::a($model->product->productName, ['product/view', 'productId' => $model->productId], ['class' => 'profile-link','title' => 'view']);
                                }
                            ],
					        'endDate',
					        'expire',
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
				<h5 style="font-weight: bold;">Productcards Used</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
					<?= GridView::widget([
                        'dataProvider' => $productcardProvider,
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
                                'attribute'=>"cardNumber",
                                'format'=>'raw',
                                'value'=> function($model){
                                    //超链接
                                    return Html::a($model->cardNumber, ['productcard/view', 'cardNumber' => $model->cardNumber], ['class' => 'profile-link','title' => 'view']);
                                }
                            ],
                            "cardValue",
                            "useDate",
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
    <?= Html::a('Back to Account List', ['index'], ['class' => 'btn btn-primary']) ?>
    <?php 
        if($model->state == '1001' || $model->state == '1004'){
            echo Html::a('Update', ['update', 'accountId' => $model->accountId], ['class' => 'btn btn-warning disabled']);
            echo '&nbsp;';
            echo Html::a('Delete', ['delete', 'accountId' => $model->accountId], ['class' => 'btn btn-danger disabled']);
        }else{
            echo Html::a('Update', ['update', 'accountId' => $model->accountId], ['class' => 'btn btn-warning']);
            echo '&nbsp;';
            echo Html::a('Delete', ['delete', 'accountId' => $model->accountId], ['class' => 'btn btn-danger']);
        }
        $this->registerJs("
            $(document).on('click', '.btn-danger', function(){
                if(!confirm('are you sure to delete this account?')){
                    return false;
                }
            });
        ");
    ?>
</p>
