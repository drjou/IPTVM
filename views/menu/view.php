<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\base\Widget;
use yii\grid\GridView;
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
                            [
                                'attribute' => 'parentName',
                                'format' => 'raw',
                                'value' => empty($model->parentMenu) ? '(not set)' : Html::a($model->parentMenu->menuName, ['menu/view', 'id' => $model->parentId], ['title' => 'view']),
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
<div class="row">
	<div class="col-lg-12">
		<div class="panel panel-default">
			<div class="panel-heading" style="background-color: #eeeeee;">
				<h5 style="font-weight: bold;">Children Menus Has</h5>
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
                                'attribute'=>"menuName",
                                'format'=>'raw',
                                'value'=> function($model){
                                    //超链接
                                    return Html::a($model->menuName, ['menu/view', 'id' => $model->id], ['class' => 'profile-link','title' => 'view']);
                                }
                            ],
                            'showLevel',
                            'showOrder',
                            [
                                'attribute' => 'icon',
                                'value' => function($model){
                                    return '<span class="' . $model->icon . '"></span>';
                                },
                                'format' => 'html',
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