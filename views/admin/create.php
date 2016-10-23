<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
$this->title = 'Add Administrator';
$this->params['breadcrumbs'][] = ['label' => 'Administrator List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'userName')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'realName')->textInput(['maxlength' => true]) ?>

<?= $form->field($model, 'password')->passwordInput() ?>

<?= $form->field($model, 'rePassword')->passwordInput() ?>

<?= $form->field($model, 'email') ?>

<div class="form-group">
    <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
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