<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;
$this->title = 'Add Stream';
$this->params['breadcrumbs'][] = ['label' => 'Monitored Streams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php $form = ActiveForm::begin()?>

<?= $form->field($model, 'server')->dropDownList($servers) ?>

<?= $form->field($model, 'processName')->textInput()?>

<?= $form->field($model, 'source')->textInput()?>

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