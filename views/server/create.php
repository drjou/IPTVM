<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Add Server';
$this->params['breadcrumbs'][] = ['label' => 'Monitored Servers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$state = [
    1 => 'up',
    0 => 'down'
];
?>

<?php $form = ActiveForm::begin()?>

<?= $form->field($model, 'serverName')->textInput()?>

<?= $form->field($model, 'serverIp')->textInput() ?>

<?= $form->field($model, 'state')->dropDownList($state) ?>

<?= $form->field($model, 'operatingSystem')->textInput() ?>

<div>
	<?=Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
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