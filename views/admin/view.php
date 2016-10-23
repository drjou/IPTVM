<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\base\Widget;
$this->title = 'Administrator ' . $model->realName;
$this->params['breadcrumbs'][] = ['label' => 'Administrator List', 'url' => ['index']];
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
	<?= Html::a('Back to Administrator List', ['index'], ['class' => 'btn btn-primary']) ?>
    <?php
        if(Yii::$app->user->identity->type == 0){
            echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-warning disabled']);
            echo '&nbsp;';
        }else{
            echo Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']);
            echo '&nbsp;';
        }
        if(Yii::$app->user->identity->type == 0  || Yii::$app->user->identity->userName == $model->userName){
            echo Html::a('Delete', ['delete', 'id' => $model->id], ['class' => 'btn btn-danger disabled']);
        }else {
            echo Html::a('Delete', ['delete', 'id' => $model->id], ['class' => 'btn btn-danger']);
        }
        $this->registerJs("
            $(document).on('click', '.btn-danger', function(){
                if(!confirm('are you sure to delete this administrator?')){
                    return false;
                }
            });
        ");
    ?>
</p>