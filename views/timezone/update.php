<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Update Timezone';
$this->params['breadcrumbs'][] = ['label' => 'Timezone List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$status = [
    1 => 'enable',
    0 => 'disable'
];
?>

<?php $form = ActiveForm::begin()?>

<?= $form->field($model, 'timezone')->textInput(['placeholder' => 'Asia/Shanghai'])?>

<?= $form->field($model, 'status')->dropDownList($status) ?>

<?= $form->field($model, 'continent')->textInput(['placeholder' => 'Asia']) ?>

<?= $form->field($model, 'country')->textInput(['placeholder' => 'China']) ?>

<?= $form->field($model, 'icon')->textInput(['placeholder' => 'icon-zhongguo']) ?>

<?= $form->field($model, 'chinese')->textInput(['placeholder' => '中国上海']) ?>

<div>
	<?=Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
	<?=Html::a('Cancel', \Yii::$app->request->referrer, ['class' => 'btn btn-warning cancel']) ?>
</div>

<?php ActiveForm::end()?>

<?php 
$this->registerJs("
    $(document).on('click', '.cancel', function(){
        window.history.back();
    });
");
?>