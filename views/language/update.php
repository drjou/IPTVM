<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Update Language';
$this->params['breadcrumbs'][] = ['label' => 'Language List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'languageName')->textInput() ?>

<div class="form-group">
    <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-warning']) ?>
</div>
<?php ActiveForm::end(); ?>
