<?php

use yii\widgets\DetailView;
use yii\helpers\Html;
$this->title = "Stream $model->processName on $model->server";
$this->params['breadcrumbs'][] = ['label' => 'Monitored Streams', 'url' => ['index']];
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
                            'processName',
                            'server',
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
	<?= Html::a('Back to Monitored Streams', ['index'], ['class' => 'btn btn-primary']) ?>
    <?php
        echo Html::a('Update', ['update', 'processName' => $model->processName, 'server' => $model->server], ['class' => 'btn btn-warning']);
        echo '&nbsp;';
        echo Html::a('Delete', ['delete', 'processName' => $model->processName, 'server' => $model->server], ['class' => 'btn btn-danger']);
        $this->registerJs("
            $(document).on('click', '.btn-danger', function(){
                if(!confirm('Are you sure to delete this stream?')){
                    return false;
                }
            });
        ");
    ?>
</p>