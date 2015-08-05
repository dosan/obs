<?php
/* @var $this CategoriesController */
/* @var $model Categories */
$this->breadcrumbs=array(
	'Adverts'=>array('adverts/index'),
	'My'
);
$this->menu=array(
	array('label'=>'List Categories', 'url'=>array('index')),
	array('label'=>'Create Categories', 'url'=>array('create')),
	array('label'=>'Manage Categories', 'url'=>array('admin')),
);
?>
<h1>Adverts in favorites </h1>


<?php foreach ($adverts as $key => $value): ?>
	<h2><?php echo CHtml::link($value['title'],array('adverts/view', 'id'=>$value['id'])); ?></h2>
<?php endforeach ?>
