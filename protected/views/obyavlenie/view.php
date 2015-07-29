<?php
/* @var $this ObyavlenieController */
/* @var $model Obyavlenie */

$this->breadcrumbs=array(
	'Obyavlenies'=>array('index'),
	$obyavlenie['cat_name'] =>array('categories/view&id='.$obyavlenie['cat_id']),
	$obyavlenie['title'],
);
$this->menu=array(
	array('label'=>'List Obyavlenie', 'url'=>array('index')),
	array('label'=>'Create Obyavlenie', 'url'=>array('create')),
	array('label'=>'Update Obyavlenie', 'url'=>array('update', 'id'=>$obyavlenie['id'])),
	array('label'=>'Delete Obyavlenie', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$obyavlenie['id']),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Obyavlenie', 'url'=>array('admin')),
);
?>

<h1>View Obyavlenie #<b><?php echo $obyavlenie['title']; ?></b></h1>
<p><?= $obyavlenie['description'] ?></p>
<span><?= $obyavlenie['contact'] ?></span>
<span><?php echo $obyavlenie['price'] ?></span>
<?php //foreach ($images as $image): ?>
	<img width="600" src="<?php // echo Yii::app()->baseUrl.'/images/obs/ob-'.$model->id.'/'. $image['name'] ?>"><br>
<?php echo '';// endforeach ?>

<a href="<?php echo Yii::app()->baseUrl ?>/index.php?r=obyavlenie/update&id=<?php echo $obyavlenie['id'] ?>">update</a>
<?php if ($inFavs): ?>
	<a href="<?php echo Yii::app()->baseUrl ?>/index.php?r=obyavlenie/removefromfavorites&ob_id=<?php echo $obyavlenie['id'] ?>">
		remove from favorites
	</a>
<?php else: ?>
	<a href="<?php echo Yii::app()->baseUrl ?>/index.php?r=obyavlenie/addtofavorites&ob_id=<?php echo $obyavlenie['id'] ?>">
		add to favorite
	</a>
<?php endif ?>
