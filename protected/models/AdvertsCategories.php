<?php 

/**
* AdvertsCategory
*/
class AdvertsCategories extends CActiveRecord
{

	protected $urlPrefix = 'adverts/';
 
	public function tableName(){
		return 'adverts_categories';
	} 
	public function getCategories(){
		$categories =  Yii::app()->db->createCommand()
		->select('id, title, cat_url')
		->from('adverts_categories')
		->where('parent_id = 0')
		->queryAll();
		foreach($categories as $key => $category){
			$childs = Yii::app()->db->createCommand()
			->select('id, title, parent_id, cat_url')
			->from('adverts_categories')
			->where('parent_id ='.$category['id'])
			->queryAll();
			$categories[$key]['childs'] = $childs;
		}
		return $categories;
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
			'childs' => array(self::HAS_MANY, 'AdvertsCategories', 'parent_id'),
			'adverts' => array(self::HAS_MANY, 'Adverts', 'category_id'),
			//'childadverts' => array(self::MANY_MANY, 'Adverts', 'categories(parent_id, id)'),
		));
	} 
	/*	public function getCategoryOptions()
	{
		$result = array();
		$categories =  Yii::app()->db->createCommand()
			->select('id, title')
			->from('categories')
			->where('parent_id = 0')
			->queryAll();
		foreach ($categories as $category) {
			$result[$category['title']] = Yii::app()->db->createCommand()
			->select('id, title')
			->from('categories')
			->where('parent_id ='.$category['id'])
			->queryAll();
		}
		return $result;
	}	*/
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('parent_id, title, description', 'required'),
			array('parent_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>25),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, parent_id, title, description', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'parent_id' => 'Parent',
			'title' => 'Title',
			'description' => 'Description',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('parent_id',$this->parent_id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Categories the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}