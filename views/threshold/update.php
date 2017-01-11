<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Update Threshold';
$this->params['breadcrumbs'][] = ['label' => 'Threshold Management', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form=ActiveForm::begin() ?>

<?= $form->field($model, 'cpu')->textInput() ?>

<?= $form->field($model, 'memory')->textInput() ?>

<?= $form->field($model, 'disk')->textInput() ?>

<?= $form->field($model, 'loads')->textInput() ?>

<?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>

<?php ActiveForm::end()?>