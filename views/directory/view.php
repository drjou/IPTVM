<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;
use app\models\Timezone;
$this->title = 'Directory ' . $model->directoryName;
$this->params['breadcrumbs'][] = ['label' => 'Directory List', 'url' => ['index']];
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
                            'directoryName',
                            [
                                'attribute' => 'parentName',
                                'format' => 'raw',
                                'value' => empty($model->parentDirectory) ? '(not set)' : Html::a($model->parentDirectory->directoryName, ['directory/view', 'directoryId' => $model->parentId], ['class' => 'profile-link','title' => 'view']),
                            ],
                            'showOrder',
                            [
                                'attribute' => 'createTime',
                                'value' => Timezone::date($model->createTime),
                            ],
                            [
                                'attribute' => 'updateTime',
                                'value' => Timezone::date($model->updateTime),
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
				<h5 style="font-weight: bold;">Children Directories Has</h5>
			</div>
			<!-- /.panel-heading -->
			<div class="panel-body">
				<div class="dataTable_wrapper">
					<?= GridView::widget([
                        'dataProvider' => $childrenProvider,
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
                            'showOrder',
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
                                'attribute'=>"channelName",
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
    <?= Html::a('Back to Directory List', ['index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Update', ['update', 'directoryId' => $model->directoryId], ['class' => 'btn btn-warning']) ?>
    <?php 
        if(!empty($model->childrenDirectories)){
            echo Html::a('Delete', ['delete', 'directoryId' => $model->directoryId], ['class' => 'btn btn-danger disabled']);
        }else{
            echo Html::a('Delete', ['delete', 'directoryId' => $model->directoryId], ['class' => 'btn btn-danger']);
        }
    ?>
</p>
<?php 
    $this->registerJs("
        $(document).on('click', '.btn-danger', function(){
            if(!confirm('are you sure to delete this directory?')){
                return false;
            }
        });
    ");
?>
