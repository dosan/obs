<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'answer-form',
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableAjaxValidation'=>false,
			'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),
	)); ?>
	<?php echo $form->hiddenField($answer,'question_id',array('value'=>$question->id)); ?>
	<div class="row">
		<?php echo $form->labelEx($answer,'your answer'); ?>
		<?php echo $form->textArea($answer,'description',array('rows'=>5, 'cols'=>100, 'value'=>'')); ?>
		<?php echo $form->error($answer,'description'); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($answer->isNewRecord ? 'Create' : 'Answer'); ?>
	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->