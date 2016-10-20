<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->title = 'Add Directory';
$this->params['breadcrumbs'][] = ['label' => 'Directory List', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php $form = ActiveForm::begin(); ?>

<?= $form->field($model, 'directoryName')->textInput() ?>

<?= $form->field($model, 'parentName')->dropDownList($directories, ['prompt' => 'Select...']) ?>

<?= $form->field($model, 'showOrder')->textInput() ?>

<?= $form->field($model, 'channels', ['template' => 
    '{label}
    <div class="checkgroup">
        <input type="checkbox" class="all" Name="CheckAll"><label for="all" class="label-all">Check All</label><br />
        {input}
    </div>
    {error}',
])->checkboxList($channels, [ 'separator'=>'&nbsp;&nbsp;']) ?>

<div class="form-group">
    <?= Html::submitButton('Add', ['class' => 'btn btn-success']) ?>
    <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-warning']) ?>
</div>
<?php ActiveForm::end(); ?>
<?php 
    $this->registerJs("
        $('.all').change(function(){
    		if(this.checked){
    			$('.label-all').html('Deselect All');
    		}else{
    			$('.label-all').html('Check All');
    		}
            $('#directory-channels input').prop('checked', this.checked);
	    });
        
        $('#directory-channels input').change(function(){
    		var checkAll = true;
        	$('#directory-channels input').each(function(){
        		if(!$(this).prop('checked')){
        			checkAll = false;
        			$('.all').prop('checked', false);
        			$('.label-all').html('Check All');
        			return false;
        		}
        	});
        	if(checkAll){
        		$('.all').prop('checked', true);
        		$('.label-all').html('Deselect All');
        	}
	    });
    ");
?>