<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\base\Widget;
$this->title = 'Menu ' . $model->menuName;
$this->params['breadcrumbs'][] = ['label' => 'Menu List', 'url' => ['index']];
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
                            'menuName',
                            'parentId',
                            [
                                'attribute' => 'parentName',
                                'value' => empty($model->parentMenu) ? '(not set)' : $model->parentMenu->menuName,
                            ],
                            'route',
                            'showLevel',
                            'showOrder',
                            [
                                'attribute' => 'icon',
                                'value' => $model->icon . '<span class="' . $model->icon . '"></span>',
                                'format' => 'html',
                            ],
                            'lastModifyTime',
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
	<?= Html::a('Back to Menu List', ['index'], ['class' => 'btn btn-primary']) ?>
    <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
    <?php 
        if(!empty($model->childrenMenus)){
            echo Html::a('Delete', ['delete', 'id' => $model->id], ['class' => 'btn btn-danger disabled']);
        }else{
            echo Html::a('Delete', ['delete', 'id' => $model->id], ['class' => 'btn btn-danger']);
        }
        $this->registerJs("
            $(document).on('click', '.btn-danger', function(){
                if(!confirm('are you sure to delete this menu?')){
                    return false;
                }
            });
        ");
    ?>
</p>