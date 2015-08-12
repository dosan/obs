<?php 
Yii::import('zii.widgets.CPortlet');

class LastQuestions extends CPortlet {
	public $title='Last Questions';
	public $maxComments=10;
	public $params = array(
		'action'=>'index',
	);
	public function getRecent()
	{
		return Questions::model()->findAll(array('select'=>'id, title', 'limit'=>10, 'order'=>'id DESC'));
	}

	protected function renderContent()
	{
		$this->render('lastquestions', array('params'=>$this->params));
	}
}