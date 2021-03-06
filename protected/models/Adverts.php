<?php


class Adverts extends CActiveRecord
{
	const IMG_PATH = '';
	public $remove;
	public $images = array();
	public $imageUrl;
	public $maxImage = 5;
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'adverts';
	}

	public function findRecent($limit = null){
		$criteria=new CDbCriteria;
		$criteria->select='id, title, description';
		$criteria->limit=$limit;
		$criteria->condition='status = :status AND activate = :activate';
		$criteria->params=array(':status'=>1, ':activate'=>1);
		return $this->findAll($criteria);
	}
	
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('type, title, description, category_id, price, contact, author_id', 'required'),
			array('type, contact, author_id', 'numerical', 'integerOnly'=>true,),
			array('category_id','numerical', 'min'=>1,'tooSmall'=>'You should choose one Category.'),
			array('type','numerical', 'max'=>1, 'tooBig'=>'Something went wrong.'),
			array('title', 'length', 'max'=>255),
			array('price', 'length', 'max'=>10),
			array('updated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('title, description, price, contact, created_at, updated_at, author_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'categories'   => array(self::BELONGS_TO,   'AdvertsCategories',    'id'),
			'author'   => array(self::BELONGS_TO,   'User', 'author_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'title' => 'Title',
			'description' => 'Description',
			'price' => 'Price',
			'contact' => 'Contact',
			'created_at' => 'Created At',
			'updated_at' => 'Updated At',
			'author_id' => 'Author',
		);
	}

	public function getImages($id = false){
/*		if ($id) {
			$this->id = $id;	
		}
		$files = CFileHelper::findFiles(Yii::app()->basePath."/../images/obs/ob-".$this->id);
		$img = array();
		foreach ($files as $image) {
			$tmp = explode('/', $image);
			$img[] = end($tmp);
		}
		return $img;*/
		$files = CFileHelper::findFiles(Yii::app()->basePath."/../images/obs/ob-".$this->id);
		$img = array();
		foreach ($files as $image) {
			$tmp = explode('/', $image);
			$img[] = end($tmp);
		}
		return $img; 
	}

	public function getAdvertsByType($type = 0, $limit){
		$criteria=new CDbCriteria;
		$criteria->select='id, title, description';
		$criteria->limit=$limit;
		$criteria->condition='type=:type AND status = :status AND activate = :activate';
		$criteria->params=array(':type'=>$type, ':status'=>1, ':activate'=>1);
		return self::model()->findAll($criteria);
	}
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.
		$criteria=new CDbCriteria;
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('price',$this->price,true);
		$criteria->compare('contact',$this->contact);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('author_id',$this->author_id);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}
	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Advert the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	private static $menuTree = array();

	public static function getMenuTree() {
		if (empty(self::$menuTree)) {
			$rows = AdvertsCategories::model()->findAll('parent_id = 0');
			foreach ($rows as $item) {
				self::$menuTree[] = self::getMenuItems($item);
			}
		}
		return self::$menuTree;
	}

	private static function getMenuItems($modelRow) {
		if (!$modelRow) return;
		if (isset($modelRow->Childs)) {
			$chump = self::getMenuItems($modelRow->Childs);
			if ($chump != null){
				$res = array('label' => $modelRow->title, 'items' => $chump, 'url' => Yii::app()->createUrl('adverts/'.$modelRow->cat_url));
			}
			else{
				$res = array('label' => $modelRow->title, 'url' => Yii::app()->createUrl('adverts/'.$modelRow->cat_url));
			}
			return $res;
		} else {
			if (is_array($modelRow)) {
				$arr = array();
				foreach ($modelRow as $leaves) {
					$arr[] = self::getMenuItems($leaves);
				}
				return $arr;
			} else {
				return array('label' => ($modelRow->title), 'url' => Yii::app()->createUrl('adverts/'.$modelRow->cat_url));
			}
		}
	}
}
