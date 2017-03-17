<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
use app\models\Timezone;
$this->title = 'Timezone ' . $model->timezone;
$this->params['breadcrumbs'][] = ['label' => 'Timezone List', 'url' => ['index']];
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
                        'attributes' => [
                            'timezone',
                            [
                                'attribute' => 'isCurrent',
                                'format' => 'html',
                                'value' => $model->isCurrent ? '<i class="glyphicon glyphicon-ok" style="color:green;"></i>' : '<i class="glyphicon glyphicon-remove" style="color:red;"></i>',
                            ], 
                            [
                                'attribute' => 'status',
                                'format' => 'html',
                                'value' => $model->status ? '<i class="fa fa-circle" style="color:green;"></i>' : '<i class="fa fa-circle" style="color:red;"></i>',
                            ],
                            'continent',
                            'country',
                            [
                                'attribute' => 'icon',
                                'format' => 'raw',
                                'value' => '<svg class="icon" aria-hidden="true">
                                	<use xlink:href="#'. $model->icon .'"></use>
                                </svg>',
                            ],
                            'chinese',
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
<p>
	<?= Html::a('Back to Timezone List', ['index'], ['class' => 'btn btn-primary']) ?>
    <?php
        echo Html::a('Update', ['update', 'timezone' => $model->timezone], ['class' => 'btn btn-warning']);
        echo '&nbsp;';
        echo Html::a('Delete', ['delete', 'timezone' => $model->timezone], ['class' => 'btn btn-danger']);
        $this->registerJs("
            $(document).on('click', '.btn-danger', function(){
                if(!confirm('are you sure to delete this timezone?')){
                    return false;
                }
            });
        ");
    ?>
</p>