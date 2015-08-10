<?php
/* @var $this AdvertController */
/* @var $model Advert */
$this->breadcrumbs=array(
	'Adverts'=>array('index'),
	$advert->title,
);
$this->menu=array(
	array('label'=>'List Advert', 'url'=>array('index')),
	array('label'=>'Create Advert', 'url'=>array('create')),
	array('label'=>'Update Advert', 'url'=>array('update', 'id'=>$advert->id)),
	array('label'=>'Delete Advert', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$advert->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Advert', 'url'=>array('admin')),
);
?>


<h1>View Advert #<b><?php echo $advert->title; ?></b></h1>
<p><?= $advert['description'] ?></p><br>
contact: <span><?= $advert->contact ?></span><br>
price: <span><?php echo $advert->price ?></span><br>
watches: <span><?php echo $advert->watches ?></span><br>
Author: <span><?php echo $advert->author['username'] ?></span><br>
<div class="row">

<?php foreach ($advert->getImages() as $image): ?>
	<?php echo CHtml::image(Yii::app()->baseUrl.'/images/obs/ob-'.$advert->id.'/'. $image,'alternative',array('style'=>'width:300px')) ?>
<?php  endforeach ?>
</div>

<?php if (Yii::app()->user->id == $advert['author_id']): ?>
	<?php echo CHtml::link('update',array('adverts/update', 'id'=>$advert->id)); ?> / 
	<!-- <?php echo CHtml::link('delete',array('adverts/delete', 'id'=>$advert->id)); ?> /  -->
	<?php if ($advert->verify_code == null): ?>
		<?php echo CHtml::link($advert->status ? 'deactivate' : 'activate',array('adverts/activate', 'id'=>$advert->id)); ?> / 
	<?php else: ?>
		Please check your email adress. Activate your advert /
	<?php endif ?>
<?php endif ?>
<?php if (Yii::app()->user->isAdmin()): ?>
<?php echo CHtml::link(CHtml::encode('Delete advert'), array('adverts/delete', 'id'=>$advert->id), array(
    'submit'=>array('adverts/delete', 'id'=>$advert->id),
    'class' => 'delete','confirm'=>'This will remove the advert. Are you sure?'));?> / 
<?php endif ?>
<?php if ($inFavs): ?>
	<?php echo CHtml::link('remove from favorites',array('adverts/removefromfavorites', 'ad_id'=>$advert->id)); ?>
<?php else: ?>
	<?php echo CHtml::link('add to favorite',array('adverts/addtofavorites', 'ad_id'=>$advert->id)); ?>
<?php endif ?>