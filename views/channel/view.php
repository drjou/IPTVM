<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
$this->title = 'Channel ' . $model->channelName;
$this->params['breadcrumbs'][] = ['label' => 'Channel List', 'url' => ['index']];
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
                            'channelName',
                            'channelIp',
                            'channelPic',
                            [
                                'attribute' => 'thumbnail',
                                'format' => 'html',
                                'value' => Html::img('/IPTVM/web' . $model->channelPic),
					        ],
                            'channelUrl',
                            'urlType',
                            'channelType',
                            [
                                'attribute' => 'languageName',
                                'format' => 'raw',
                                'value' => Html::a($model->language->languageName, ['language/view', 'languageId' => $model->languageId], ['class' => 'profile-link','title' => 'view']),
                            ],
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
				<h5 style="font-weight: bold;">Products Belongs</h5>
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
                                    return Html::a($model->productName, ['product/view', 'productId' => $model->productId], ['class' => 'profile-link','title' => 'view']);
                                }
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
				<h5 style="font-weight: bold;">Directories Belongs</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
					<?= GridView::widget([
                        'dataProvider' => $directoryProvider,
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
                                'attribute'=>"directoryName",
                                'format'=>'raw',
                                'value'=> function($model){
                                    //超链接
                                    return Html::a($model->directoryName, ['directory/view', 'directoryId' => $model->directoryId], ['class' => 'profile-link','title' => 'view']);
                                }
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
    <?= Html::a('Back to Channel List', ['index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Update', ['update', 'channelId' => $model->channelId], ['class' => 'btn btn-warning']) ?>
    <?= Html::a('Delete', ['delete', 'channelId' => $model->channelId], ['class' => 'btn btn-danger']) ?>
</p>
<?php 
    $this->registerJs("
        $(document).on('click', '.btn-danger', function(){
            if(!confirm('are you sure to delete this channel?')){
                return false;
            }
        });
    ");
?>
