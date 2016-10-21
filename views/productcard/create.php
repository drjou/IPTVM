<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Add Productcard';
$this->params['breadcrumbs'][] = ['label' => 'Productcard List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$cardValues = [
    183 => 183,
    365 => 365,
];
$model->cardState = 0;
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'cardNumber')->textInput() ?>

<?= $form->field($model, 'cardValue', ['template' => 
    '{label}
    <div class="checkgroup">
        {input}
    </div>
    {error}',
])->radioList($cardValues, [ 'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;'])?>

<?= $form->field($model, 'productName')->dropDownList($products) ?>

<?= $form->field($model, 'cardState')->checkbox(['disabled' => 'diabled'])?>

<div class="form-group">
    <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-warning']) ?>
</div>
<?php ActiveForm::end(); ?>