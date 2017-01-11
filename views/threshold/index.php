<?php
use yii\widgets\DetailView;
use yii\helpers\Html;
$this->title = 'Threshold Management';
$this->params['breadcrumbs'][] = $this->title;
?>

<?= DetailView::widget(
    [
        'model' => $model,
        'attributes' => ['cpu', 'memory', 'disk', 'loads']
    ]) 
?>
<?=Html::a('Update', ['update'], ['class' => 'btn btn-success']) ?>
