<?php 
Yii::import('zii.widgets.CPortlet');

class LastAdverts extends CPortlet {
	public $title='Last Adverts';
	public $max=10;
	public $params = array(
		// пусть по умолчанию будет активна ссылка на главную
		'action'=>'index',
	);
	public function getRecent()
	{
		return Adverts::model()->findRecent($this->max);
	}

	protected function renderContent()
	{
		$this->render('lastadverts', array('params'=>$this->params));
	}
}