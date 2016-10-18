<?php
use yii\helpers\Html;
use yii\grid\GridView;
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
                                'value' => $model->product->productName,
                            ],
                            'cardState',
                            'useDate',
                            'accountId',
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
</p>
