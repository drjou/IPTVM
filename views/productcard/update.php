<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Update Productcard';
$this->params['breadcrumbs'][] = ['label' => 'Productcard List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
$cardValues = [
    183 => 183,
    365 => 365,
];
$model->productName = $model->productId;
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
    <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
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