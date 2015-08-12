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


<h1>View Question #<b><?php echo $question->title; ?></b></h1>
<p><?php echo $question->description ?></p>
Rating: <span><?php echo array_sum(array_map(function($a){return $a['rate'];},$question->ratings)) ?></span>;
<?php echo $question->ratingsCount ?> people rated; 
<?php if (Yii::app()->user->id != $question->author->id): ?>
	<!-- ajax buttons up and down -->
	<?php echo CHtml::ajaxSubmitButton('+',Yii::app()->createUrl('questions/rate'),
			array('type'=>'POST','data'=> 'js:{"rate": +1, "question_id": '.$question->id.' }',                        
				'success'=>'js:function(string){ alert(string); }'),array('class'=>'someCssClass',));?>
	<?php echo CHtml::ajaxSubmitButton('-',Yii::app()->createUrl('questions/rate'),
			array('type'=>'POST','data'=> 'js:{"rate": -1, "question_id": '.$question->id.' }',                        
				'success'=>'js:function(string){ alert(string); }'),array('class'=>'someCssClass',));?>
<?php endif ?>
<br>
Author: <span><?php echo $question->author->username ?></span><br>
Answers: <span><?php echo $question->answersCount ?></span>
<hr>
<?php foreach ($question->answers as $ans): ?>
	<?php if ($question->author->id == Yii::app()->user->id): ?>
		<?php echo CHtml::ajaxSubmitButton('V',Yii::app()->createUrl('questions/rightanswer'),
		array('type'=>'POST','data'=> 'js:{"right_answer": '.$ans->right_answer ? 0 : 1.', "answer_id": '.$ans->id.'}',                        
			'success'=>'js:function(string){ alert(string); }'),array('class'=>'someCssClass',));?>
	<?php endif ?>
 	Answer: <?php echo $ans->description ?>
	Author: <b><?php echo $ans->author->username ?></b>
	Rating: <span><?php echo array_sum(array_map(function($a){return $a['rate'];},$ans->ratings)) ?></span>;
	<?php echo $ans->ratingsCount ?> people rated; 
	<!-- ajax buttons up and down -->
	<?php if (Yii::app()->user->id != $ans->author->id): ?>
		<?php echo CHtml::ajaxSubmitButton('+',Yii::app()->createUrl('questions/rate'),
				array('type'=>'POST','data'=> 'js:{"rate": +1, "answer_id": '.$ans->id.' }',                        
					'success'=>'js:function(string){ alert(string); }'),array('class'=>'someCssClass',));?>
		<?php echo CHtml::ajaxSubmitButton('-',Yii::app()->createUrl('questions/rate'),
				array('type'=>'POST','data'=> 'js:{"rate": -1, "answer_id": '.$ans->id.' }',                        
					'success'=>'js:function(string){ alert(string); }'),array('class'=>'someCssClass',));?>
	<?php endif ?><hr>
<?php endforeach ?>
<h1>Answer this question</h1>

<?php $this->renderPartial('_answer', array('answer'=>$answer, 'question'=>$question)); ?>