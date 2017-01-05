<?php

use app\assets\FileInputAsset;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Progress;

$this->title = 'Import Servers';
$this->params['breadcrumbs'][] = ['label' => 'Monitored Servers', 'url' => ['index']];
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
    <?= Html::submitButton('Import', [
        'class' => 'btn btn-success import',
        'data-toggle' => 'modall',
        'data-target' => '#progress-modal',
    ]) ?>
    <?= Html::a('Cancel', \Yii::$app->request->referrer, ['class' => 'btn btn-warning cancel']) ?>
</div>
<?php ActiveForm::end(); ?>

<?php Modal::begin([
    'id' => 'progress-modal',
    'header' => '<h4 class="modal-title">Progress</h4>',
    'footer' => '<a href="#" class="btn btn-primary" data-dismiss="modal">Close</a>',
]); 
Modal::end();

$this->registerJs("
    $('#file-input').fileinput({
            showUpload: false,
            maxFileSize: 51200, //52M
        });
    $(document).on('click', '.cancel', function(){
            window.history.back();
        });
");
