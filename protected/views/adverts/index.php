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

<h1>Categories</h1>
<ul>
<?php foreach ($categories as $key => $value): ?>
		<li><a href="<?php echo Yii::app()->baseUrl ?>/index.php?r=categories/view&id=<?php echo $value['id'] ?>"><?php echo $value['title'] ?></a></li>
		<?php foreach ($value['childs'] as $childs): ?>
			<li><a href="<?php echo Yii::app()->baseUrl ?>/index.php?r=categories/view&id=<?php echo $childs['id'] ?>">--<?php echo $childs['title'] ?></a></li>
		<?php endforeach ?>
<?php endforeach ?>
</ul>


<h1>Buy</h1>
<table >
	<tr>
		<th>title</th>
		<th>description</th>
		<th>image</th>
	</tr>
<?php foreach ($buyAdverts as $value): ?>
		<tr>
			<td><?php echo $value['title'] ?></td>	
			<td><?php echo $value['description'] ?></td>	
		</tr>
<?php endforeach ?>
</table>
<h1>Cell</h1>
<table >
	<tr>
		<th>title</th>
		<th>description</th>
		<th>image</th>
	</tr>
<?php foreach ($adverts as $value): ?>
		<tr>
			<td><?php echo $value['title'] ?></td>	
			<td><?php echo $value['description'] ?></td>	
		</tr>
<?php endforeach ?>
</table>
<a href="<?php echo Yii::app()->baseUrl ?>/index.php?r=adverts/create">Create new advert</a>