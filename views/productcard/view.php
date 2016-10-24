<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
$this->title = 'Productcard ' . $model->cardNumber;
$this->params['breadcrumbs'][] = ['label' => 'Productcard List', 'url' => ['index']];
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
                            'cardNumber',
                            'cardValue',
                            [
                                'attribute' => 'productName',
                                'format' => 'raw',
                                'value'=>  Html::a($model->product->productName, ['product/view', 'productId' => $model->productId], ['class' => 'profile-link','title' => 'view']),
                            ],
                            [
                                'attribute' => 'cardState',
                                'value' => $model->cardState ? 'used' : 'not used',
                            ],
                            [
                                'attribute' => 'useDate',
                                'value' => empty($model->useDate) ? '(not set)' : $model->useDate,
                            ],
                            [
                                'attribute' => 'accountId',
                                'format' => 'raw',
                                'value'=>  empty($model->accountId) ? '(not set)' : Html::a($model->accountId, ['account/view', 'accountId' => $model->accountId], ['class' => 'profile-link','title' => 'view']),
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
    <?= Html::a('Back to Productcard List', ['index'], ['class' => 'btn btn-primary']) ?>
    <?php 
        if($model->cardState == 1){
            echo Html::a('Update', ['update', 'cardNumber' => $model->cardNumber], ['class' => 'btn btn-warning disabled']);
            echo '&nbsp;';
            echo Html::a('Delete', ['delete', 'cardNumber' => $model->cardNumber], ['class' => 'btn btn-danger disabled']);
        }else{
            echo Html::a('Update', ['update', 'cardNumber' => $model->cardNumber], ['class' => 'btn btn-warning']);
            echo '&nbsp;';
            echo Html::a('Delete', ['delete', 'cardNumber' => $model->cardNumber], ['class' => 'btn btn-danger']);
        }
        $this->registerJs("
            $(document).on('click', '.btn-danger', function(){
                if(!confirm('are you sure to delete this productcard?')){
                    return false;
                }
            });
        ");
    ?>
</p>
