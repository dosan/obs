<?php 

/**
* AdvertsCategory
*/
class AdvertsCategories extends CActiveRecord
{

	protected $urlPrefix = 'adverts/';

	public static function model($className=__CLASS__){
		return parent::model($className);
	}  
 
	public function tableName(){
		return 'categories';
	} 

	public function findByCatUrl($cat_url){
		return $this->findByAttributes(array('cat_url'=>$cat_url));
	}

	public static function getAssocList()
	{
		$model = self::model()->findAll(array('order'=>'title ASC'));
		return CHtml::listData($model, 'id', 'title');
	}

	public function relations()
	{
		return array_merge(parent::relations(), array(
			'parent' => array(self::BELONGS_TO, 'AdvertsCategories', 'parent_id'),
		));
	}  
}