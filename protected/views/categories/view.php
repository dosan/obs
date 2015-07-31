<?php
/* @var $this CategoriesController */
/* @var $model Categories */

$this->breadcrumbs=array(
	'Adverts'=>array('advert/index'),
	$category['title']
);

$this->menu=array(
	array('label'=>'List Categories', 'url'=>array('index')),
	array('label'=>'Create Categories', 'url'=>array('create')),
	array('label'=>'Update Categories', 'url'=>array('update', 'id'=>$category['id'])),
	array('label'=>'Delete Categories', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$category['id']),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Categories', 'url'=>array('admin')),
);
?>

<h1>Adverts by Category #<?php echo $category['title']; ?></h1>

<?php foreach ($adverts as $key => $value): ?>
	<h1><a href="<?php echo Yii::app()->baseUrl ?>/index.php?r=advert/view&id=<?php echo $value['id'] ?>"><?php echo $value['title'] ?></a></h1>
<?php endforeach ?>
