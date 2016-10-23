<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\assets\FileInputAsset;

$this->title = 'Add Channel';
$this->params['breadcrumbs'][] = ['label' => 'Channel List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
FileInputAsset::register($this);
$urlTypes = [
    'entire' => 'entire',
    'sep' => 'sep',
];
$channelTypes = [
    'live' => 'live',
    'vod' => 'vod',
];
$model->urlType = 'entire';
$model->channelType = 'live';
?>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

<?= $form->field($model, 'channelName')->textInput(['placeholder' => 'length of channel name must between 3 to 10 characters']) ?>

<?= $form->field($model, 'channelIp')->textInput(['placeholder' => 'like 188.138.89.40']) ?>

<?= $form->field($model, 'thumbnail')->fileInput(['id' => 'file-input', 'accept' => 'image/*']) ?>

<?= $form->field($model, 'channelUrl')->textInput(['placeholder' => 'auto fill, can manually modify']) ?>

<?= $form->field($model, 'urlType', ['template' => 
    '{label}
    <div class="checkgroup">
        {input}
    </div>
    {error}',
])->radioList($urlTypes, [ 'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;'])?>

<?= $form->field($model, 'channelType', ['template' => 
    '{label}
    <div class="checkgroup">
        {input}
    </div>
    {error}',
])->radioList($channelTypes, [ 'separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;'])?>

<?= $form->field($model, 'languageName')->dropDownList($languages) ?>

<div class="form-group">
    <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Cancel', \Yii::$app->request->referrer, ['class' => 'btn btn-warning cancel']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php 
    $this->registerJs("
        $('#file-input').fileinput({
            'showUpload':false,
        });
        $('#channel-channelname').bind('input propertychange', function(){
    		if($('#channel-urltype input:checked').val() == 'entire'){
    			$('#channel-channelurl').val('http://'+$('#channel-channelip').val()+'/IPTV_Files/'+$(this).val()+'/'+$(this).val()+'.m3u8');
    		}else{
    			$('#channel-channelurl').val('/IPTV_Files/'+$(this).val()+'/'+$(this).val()+'.m3u8');
    		}
	    });
        
        $('#channel-channelip').bind('input propertychange', function(){
    		if($('#channel-urltype input:checked').val() == 'entire'){
    			$('#channel-channelurl').val('http://'+$(this).val()+'/IPTV_Files/'+$('#channel-channelname').val()+'/'+$('#channel-channelname').val()+'.m3u8');
    		}
	    });
        
        $('#channel-urltype input').change(function(){
    		if(this.checked){
                if($(this).val() == 'entire'){
                    $('#channel-channelurl').val('http://'+$('#channel-channelip').val()+'/IPTV_Files/'+$('#channel-channelname').val()+'/'+$('#channel-channelname').val()+'.m3u8');
                }else{
                    $('#channel-channelurl').val('/IPTV_Files/'+$('#channel-channelname').val()+'/'+$('#channel-channelname').val()+'.m3u8');
                }
    		}
	    });
        $(document).on('click', '.cancel', function(){
            window.history.back();
        });
    ");
?>