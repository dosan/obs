<?php
/* @var $this advertController */
/* @var $model advert */

$this->breadcrumbs=array(
	'adverts'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List advert', 'url'=>array('index')),
	array('label'=>'Manage advert', 'url'=>array('admin')),
);
?>

<h1>Create advert</h1>

<?php $this->renderPartial('_form', array('model'=>$model)); ?>