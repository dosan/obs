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

<?php foreach ($adverts as $key => $value): ?>
	<h1><a href="<?php echo Yii::app()->baseUrl ?>/index.php?r=adverts/view&id=<?php echo $value['id'] ?>"><?php echo $value['title'] ?></a></h1>
<?php endforeach ?>