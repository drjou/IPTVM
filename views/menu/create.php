<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Add Menu';
$this->params['breadcrumbs'][] = ['label' => 'Menu List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'menuName')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'parentName')->dropDownList($menu_items, ['prompt' => 'Select...']) ?>

<?= $form->field($model, 'route')->textInput(['maxlength' => true, 'placeholder' => 'such as /site/index, if not, javascrip:void(0) is recommended']) ?>

<?= $form->field($model, 'showLevel')->textInput(['maxlength' => true, 'placeholder' => 'level should between 1 and 3']) ?>

<?= $form->field($model, 'showOrder')->textInput(['maxlength' => true, 'placeholder' => 'show order of the same level is [1,2,...]']) ?>

<?= $form->field($model, 'icon', ['template' => 
    '{label}
    <div class="input-group">
        {input}
        <span class="input-group-addon"><span id="menu-img" class="fa fa-dashboard fa-fw"></span></span>
    </div>
    {error}'])->textInput(['class' => 'form-control menu-text','maxlength' => true, 'placeholder' => 'fa fa-dashboard fa-fw']) ?>

<div class="form-group">
    <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Cancel', \Yii::$app->request->referrer, ['class' => 'btn btn-warning cancel']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php 
    $this->registerJs("
        $('.menu-text').bind('input propertychange', function(){
    		$('#menu-img').removeClass().addClass($(this).val());
    	});
        $(document).on('click', '.cancel', function(){
            window.history.back();
        });
    ");
?>