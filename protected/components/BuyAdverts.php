<?php 
Yii::import('zii.widgets.CPortlet');

class BuyAdverts extends CPortlet {
	public $title='Buy Adverts';
	public $maxComments=10;
	public $params = array(
		'action'=>'index',
	);
	public function getRecent()
	{
		// 1 is type of advert it means buy
		return Adverts::model()->getAdvertsByType(1, $this->maxComments);
	}

	protected function renderContent()
	{
		$this->render('lastadverts', array('params'=>$this->params));
	}
}