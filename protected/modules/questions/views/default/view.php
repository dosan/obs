<?php
/* @var $this questions/DefaultController */
/* @var $model Questions */
$this->breadcrumbs=array(
	'Questions'=>array('index'),
	$question->title,
);
$this->menu=array(
	array('label'=>'List question', 'url'=>array('index')),
	array('label'=>'Create question', 'url'=>array('create')),
	array('label'=>'Update question', 'url'=>array('update', 'id'=>$question->id)),
	array('label'=>'Delete question', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$question->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage question', 'url'=>array('admin')),
);
?>


<h1>View Advert #<b><?php echo $question->title; ?></b></h1>
<p><?php echo $question->description ?></p>
<?php $rate = 0;
foreach ($question->ratings as $key => $value):
	$rate += $value['rate'];
 endforeach ?>
Rating: <span><?php echo $rate ?></span>;
<?php echo $question->ratingsCount ?> people rated; 
<?php echo CHtml::ajaxSubmitButton('+',Yii::app()->createUrl('questions/rate'),
		array(
			'type'=>'POST',
			'data'=> 'js:{"rate": +1, "question_id": '.$question->id.' }',                        
			'success'=>'js:function(string){ alert(string); }'           
		),array('class'=>'someCssClass',));?>
<?php echo CHtml::ajaxSubmitButton('-',Yii::app()->createUrl('questions/rate'),
		array(
			'type'=>'POST',
			'data'=> 'js:{"rate": -1, "question_id": '.$question->id.' }',                        
			'success'=>'js:function(string){ alert(string); }'           
		),array('class'=>'someCssClass',));?>
<br>
Author: <span><?php echo $question->author['username'] ?></span><br>
