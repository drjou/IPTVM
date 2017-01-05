<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
$this->title = 'Update Server';
$this->params['breadcrumbs'][] = ['label' => 'Monitored Servers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$states = [
    1 => 'enabled',
    0 => 'disabled',
];
?>
<?php $form = ActiveForm::begin()?>

<?= $form->field($model, 'serverName')->textInput()?>

<?= $form->field($model, 'serverIp')->textInput()?>

<?= $form->field($model, 'state')->dropDownList($states) ?>

<?= $form->field($model, 'operatingSystem')->textInput() ?>

<div class="form-group">
	<?= Html::submitButton('Update', ['class' => 'btn btn-success'])?>
	<?= Html::a('Cancel', \Yii::$app->request->referrer, ['class' => 'btn btn-warning cancel']) ?>
</div>

<?php ActiveForm::end()?>

<?php 
$this->registerJs("
    $(document).on('click', '.cancel', function(){
        window.history.back();
    });
");
?>