<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
$this->title = "Update $model->streamName on $model->server";
$this->params['breadcrumbs'][] = ['label' => 'Monitored Streams', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin()?>

<?= $form->field($model, 'server')->dropDownList($servers) ?>

<?= $form->field($model, 'streamName')->textInput()?>

<?= $form->field($model, 'source')->textInput()?>

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