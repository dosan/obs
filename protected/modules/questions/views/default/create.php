<?php
/* @var $this questionsController */
/* @var $model questions */
$this->breadcrumbs=array(
	'Questions'=>array('index'),
	'Create',
);
$this->menu=array(
	array('label'=>'List questions', 'url'=>array('index')),
	array('label'=>'Manage questions', 'url'=>array('admin')),
);
?>

<h1>Ask questions</h1>

<?php $this->renderPartial('_form', array('model'=>$model, 'categories'=>$categories)); ?>