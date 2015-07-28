<?php
/* @var $this ObyavlenieController */
/* @var $model Obyavlenie */

$this->breadcrumbs=array(
	'Obyavlenies'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List Obyavlenie', 'url'=>array('index')),
	array('label'=>'Manage Obyavlenie', 'url'=>array('admin')),
);
?>

<h1>Create Obyavlenie</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>