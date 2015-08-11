<?php
/* @var $this DefaultController */

$this->breadcrumbs=array(
	$this->module->id,
);
?>
<h1>Buy</h1>
<ul>
<?php foreach ($categories as $key => $value): ?>
	<li><?php echo CHtml::link($value['title'],array('category/'.$value['id'])); ?></li>
<?php endforeach ?>
</ul>

<h3><?php echo CHtml::link('Add Question', array('/questions/create')); ?></h3>