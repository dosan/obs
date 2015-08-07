<?php 
Yii::import('zii.widgets.CPortlet');

class CellAdverts extends CPortlet {
	public $title='Cell Adverts';
	public $maxComments=10;
	public $params = array(
		'action'=>'index',
	);
	public function getRecent()
	{
		// 0 is type of advert it means buy
		return Adverts::model()->getAdvertsByType(0, $this->maxComments);
	}

	protected function renderContent()
	{
		$this->render('lastadverts', array('params'=>$this->params));
	}
}