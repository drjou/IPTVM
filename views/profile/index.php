<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\base\Widget;
$this->title = 'Personal Info';
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
                            'userName',
                            'realName',
                            'email',
                            [
                                'attribute' => 'type',
                                'value' => $model->type ? 'Super Administrator' : 'Genernal Administrator',
                            ],
                            'lastLoginTime',
                            'createTime',
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
	<?= Html::a('Info Modify', ['info-modify'], ['class' => 'btn btn-warning']) ?>
	<?= Html::a('Password Modify', ['password-modify'], ['class' => 'btn btn-danger']) ?>
</p>