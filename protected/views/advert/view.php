<?php
/* @var $this AdvertController */
/* @var $model Advert */

$this->breadcrumbs=array(
	'Adverts'=>array('index'),
	$advert['cat_name'] =>array('categories/view&id='.$advert['cat_id']),
	$advert['title'],
);
$this->menu=array(
	array('label'=>'List Advert', 'url'=>array('index')),
	array('label'=>'Create Advert', 'url'=>array('create')),
	array('label'=>'Update Advert', 'url'=>array('update', 'id'=>$advert['id'])),
	array('label'=>'Delete Advert', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$advert['id']),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Advert', 'url'=>array('admin')),
);
?>

<h1>View Advert #<b><?php echo $advert['title']; ?></b></h1>
<p><?= $advert['description'] ?></p>
<span><?= $advert['contact'] ?></span>
<span><?php echo $advert['price'] ?></span>
<div class="row">
	
<?php foreach ($images as $image): ?>


	<?php echo CHtml::image(Yii::app()->baseUrl.'/images/ads/ad-'.$advert['id'].'/'. $image,'alternative',array()) ?>
	<!-- <div class="row">
		<img style="display: block !important" width="301" src="<?php echo Yii::app()->baseUrl.'/images/ads/ad-'.$advert['id'].'/'. $image ?>" alt="something">
	</div> -->
<?php  endforeach ?>
</div>

<a href="<?php echo Yii::app()->baseUrl ?>/index.php?r=advert/update&id=<?php echo $advert['id'] ?>">update</a>
<?php if ($inFavs): ?>
	<a href="<?php echo Yii::app()->baseUrl ?>/index.php?r=advert/removefromfavorites&ad_id=<?php echo $advert['id'] ?>">
		remove from favorites
	</a>
<?php else: ?>
	<a href="<?php echo Yii::app()->baseUrl ?>/index.php?r=advert/addtofavorites&ad_id=<?php echo $advert['id'] ?>">
		add to favorite
	</a>
<?php endif ?>
