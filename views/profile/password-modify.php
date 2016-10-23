<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Password Modify';
$this->params['breadcrumbs'][] = ['label' => 'Personal Info', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$model->password = null;
$model->rePassword = null;
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'oldPassword')->passwordInput() ?>

<?= $form->field($model, 'password')->passwordInput() ?>

<?= $form->field($model, 'rePassword')->passwordInput() ?>

<div class="form-group">
    <?= Html::submitButton('Modify', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Cancel', \Yii::$app->request->referrer, ['class' => 'btn btn-warning cancel']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php 
    $this->registerJs("
        $(document).on('click', '.cancel', function(){
            window.history.back();
        });
    ");
?>