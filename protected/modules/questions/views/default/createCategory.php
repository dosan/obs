<?php
/* @var $this questionsController */
/* @var $model questions */
$this->breadcrumbs=array(
	'adverts'=>array('index'),
	'Create',
);
$this->menu=array(
	array('label'=>'List questions', 'url'=>array('index')),
	array('label'=>'Manage questions', 'url'=>array('admin')),
);
?>

<h1>Create questions</h1>

<?php $this->renderPartial('_categoryForm', array('model'=>$model)); ?>