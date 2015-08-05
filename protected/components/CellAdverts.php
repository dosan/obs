<?php 
Yii::import('zii.widgets.CPortlet');

class CellAdverts extends CPortlet {
	public $title='Cell Adverts';
	public $maxComments=10;
	public $params = array(
		// пусть по умолчанию будет активна ссылка на главную
		'action'=>'index',
	);
	public function getRecent()
	{
		// 0 is type of advert it means buy
		return Adverts::model()->getAdvertsByType($this->maxComments, 0);
	}

	protected function renderContent()
	{
		$this->render('lastadverts', array('params'=>$this->params));
	}
}