<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\FileInputAsset;
use yii\bootstrap\Progress;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = 'Import Products';
$this->params['breadcrumbs'][] = ['label' => 'Product List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
FileInputAsset::register($this);
?>
<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
    'id' => 'form-id',
]); ?>

<?= $form->field($model, 'importFile')->fileInput(['id' => 'file-input', 'accept' => '.xml']) ?>

<div class="alert <?= $state['class'] ?>" role="alert">
	<?= $state['message'] ?>
</div>

<?= Progress::widget([
    'percent' => $state['percent'], 
    'label' => $state['label'],
    'barOptions' => [
        'class' => 'progress-bar progress-bar-success progress-bar-striped active',
    ],
]) ?>

<div class="form-group">
    <?= Html::submitButton('Import', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Cancel', \Yii::$app->request->referrer, ['class' => 'btn btn-warning cancel']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php
    $this->registerJs("
        $('#file-input').fileinput({
            showUpload: false,
            maxFileSize: 51200, //52M
        });
        $(document).on('click', '.cancel', function(){
            window.history.back();
        });
    ");
?>