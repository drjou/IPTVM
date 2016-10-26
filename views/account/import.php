<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\FileInputAsset;
use yii\bootstrap\Progress;
use yii\base\Widget;
use yii\bootstrap\Modal;
use yii\helpers\Url;

$this->title = 'Import Accounts';
$this->params['breadcrumbs'][] = ['label' => 'Account List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
FileInputAsset::register($this);
?>
<?php $form = ActiveForm::begin([
    'options' => ['enctype' => 'multipart/form-data'],
    'id' => 'form-id',
]); ?>

<?= $form->field($model, 'importFile')->fileInput(['id' => 'file-input', 'accept' => '.xml']) ?>

<?= Progress::widget([
    'percent' => $percent, 
    'label' => $label,
    'barOptions' => [
        'class' => 'progress-bar progress-bar-success progress-bar-striped active',
    ],
]) ?>

<div class="form-group">
    <?= Html::submitButton('Import', [
        'class' => 'btn btn-success import',
        'data-toggle' => 'modal',
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
?>
<?php
    $requestUrl = Url::toRoute('progress');
    $js = "
        $(document).on('click', '.import', function () {
            $.get('{$requestUrl}', {},
                function (data) {
                    $('.modal-body').html(data);
                }
            );
        });
    ";
    $this->registerJs($js);
    $this->registerJs("
        $('#file-input').fileinput({
            
        });
        $(document).on('click', '.imrt', function(){
            $.ajax({
                type: 'post',
                url: 'index.php?r=account/progress',
                data: {},
                dataType: 'html',
                success: function(html){
                    $('.form-group').append(html);
                }
            });
        });
        $(document).on('click', '.cancel', function(){
            window.history.back();
        });
    ");
?>