<?php
/* @var $this AdvertController */
/* @var $model Advert */
$this->breadcrumbs=array(
	'Adverts'=>array('index'),
	'Manage',
);
$this->menu=array(
	array('label'=>'List Advert', 'url'=>array('index')),
	array('label'=>'Create Advert', 'url'=>array('create')),
);
Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$('#advert-grid').yiiGridView('update', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1>Manage Adverts</h1>

<p>
You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
</p>

<?php echo CHtml::link('Advanced Search','#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'adverts-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		array(
			'name' => 'title',
			'value' => 'CHtml::link(CHtml::encode($data->title), Yii::app()->controller->createUrl("view", array("id" => $data->id)))',
			'type' => 'html',
		),
		'description',
		'price',
		'contact',
		array(
			'name'=>'status',
			'value'=>'$data->status ? "active":"inactive"',
		),
		array(
			'class' => 'phaCheckColumn',
			'name' => 'activate',
			'actionUrl' => array('setIsActive'),
		),
		/*
		'created_at',
		'updated_at',
		'author_id',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>