<?php
/* @var $this ObyavlenieController */
/* @var $model Obyavlenie */

$this->breadcrumbs=array(
	'Obyavlenies'=>array('index'),
	$model->title=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Obyavlenie', 'url'=>array('index')),
	array('label'=>'Create Obyavlenie', 'url'=>array('create')),
	array('label'=>'View Obyavlenie', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Obyavlenie', 'url'=>array('admin')),
);
?>

<h1>Update Obyavlenie <?php echo $model->id; ?></h1>

<?php $this->renderPartial('_updateform', array('model'=>$model)); ?>