<?php
/* @var $this AdvertsController */
/* @var $model Adverts */
/* @var $form CActiveForm */
?>
<div class="form">
<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'adverts-form',
		// Please note: When you enable ajax validation, make sure the corresponding
		// controller action is handling ajax validation correctly.
		// There is a call to performAjaxValidation() commented in generated controller code.
		// See class documentation of CActiveForm for details on this.
		'enableAjaxValidation'=>false,
			'htmlOptions' => array(
			'enctype' => 'multipart/form-data',
		),
	)); ?>
	<p class="note">Fields with <span class="required">*</span> are required.</p>
	<?php echo $form->errorSummary($model); ?>
	<div class="row">
		<?php echo $form->labelEx($model,'title'); ?>
		<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'title'); ?>
	</div>
<!-- 	<div class="row">
	<?php echo $form->labelEx($model,'category_id'); ?><br />
	<?php echo $form->dropDownList($model,'category_id', AdvertsCategories::getAssocList()); ?><br />
	<?php echo $form->error($model,'category_id'); ?>
</div> -->
	<?php echo $form->labelEx($model, 'Category'); ?>
		<select name="Adverts[category_id]" id="Adverts_category_id">
			<?php foreach (Categories::model()->getCategories() as $mainCategories): ?>
			<optgroup label="<?php echo $mainCategories['title'] ?>">
				<?php foreach ($mainCategories['childs'] as $child): ?>
					<option value="<?= $child['id'] ?>" <?php echo $child['id'] == $model->category_id ? ' selected="selected"' : '' ?>><?= $child['title'] ?></option>
				<?php endforeach ?>
			</optgroup>
			<?php endforeach ?>
		</select>
		<span class="required">*</span>
	<?php echo $form->error($model, 'category');?>
	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'price'); ?>
		<?php echo $form->textField($model,'price',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'price'); ?>
	</div>
	<div class="row">
		<?php echo $form->labelEx($model,'contact'); ?>
		<?php echo $form->textField($model,'contact'); ?>
		<?php echo $form->error($model,'contact'); ?>
	</div>
	<div class="row">
		<?php $i = 1; foreach ($images as $image): ?>
		<?php echo CHtml::activeLabel($model, 'remove', array('for'=>'rm_'.$image))?>
		<?php echo CHtml::activeCheckBox($model,'remove',array('name'=>'Adverts[remove]['.$image.']', 'id'=>'rm_'.$image)) ?>
		<?php echo CHtml::link(CHtml::image(Yii::app()->baseUrl.'/images/obs/ob-'.$model['id'].'/'.$image,'name', array('style'=>'width:200px;')),
		Yii::app()->baseUrl.'/images/obs/ob-'.$model['id'].'/'. $image); ?>	
		<?php $i++; endforeach ?>
	</div>
	<div class="row">
		<?php $this->widget('CMultiFileUpload', array(
			'name' => 'images',
			'accept' => 'jpeg|jpg|gif|png',
			'duplicate' => 'Duplicate file!',
			'denied' => 'Invalid file type'
		)); ?>
	</div>
	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save'); ?>
	</div>
<?php $this->endWidget(); ?>
</div><!-- form -->