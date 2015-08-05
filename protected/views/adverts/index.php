<?php
/* @var $this AdvertController */
/* @var $dataProvider CActiveDataProvider */
$this->breadcrumbs=array(
	'Adverts',
);
$this->menu=array(
	array('label'=>'Create Advert', 'url'=>array('create')),
	array('label'=>'Manage Advert', 'url'=>array('admin')),
);
?>

<h1>Buy</h1>
<ul>
<?php foreach ($categories as $key => $value): ?>
		<li><?php echo CHtml::link($value['title'],array('buy/category/'.$value['cat_url'])); ?></li>
		<?php foreach ($value['childs'] as $childs): ?>
			<li>==<?php echo CHtml::link($childs['title'],array('buy/category/'.$value['cat_url'])); ?></li>
		<?php endforeach ?>
<?php endforeach ?>
</ul>
<h1>Cell</h1>
<ul>
<?php foreach ($categories as $key => $value): ?>
		<li><?php echo CHtml::link($value['title'],array('cell/category/'.$value['cat_url'])); ?></li>
		<?php foreach ($value['childs'] as $childs): ?>
			<li>==<?php echo CHtml::link($childs['title'],array('cell/category/'.$value['cat_url'])); ?></li>
		<?php endforeach ?>
<?php endforeach ?>
</ul>

<?php echo CHtml::link('Create new Advert',array('adverts/create')); ?> / 
<?php echo CHtml::link('Favorites',array('adverts/my')); ?>
