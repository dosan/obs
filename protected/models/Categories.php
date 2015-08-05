<?php

/**
 * This is the model class for table "categories".
 *
 * The followings are the available columns in table 'categories':
 * @property integer $id
 * @property integer $parent_id
 * @property string $title
 * @property string $description
 *
 * The followings are the available model relations:
 * @property Advert[] $adverts
 */
class Categories extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'categories';
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
	public function getCategories(){
		$categories =  Yii::app()->db->createCommand()
		->select('id, title, cat_url')
		->from('categories')
		->where('parent_id = 0')
		->queryAll();
		foreach($categories as $key => $category){
			$childs = Yii::app()->db->createCommand()
			->select('id, title, parent_id, cat_url')
			->from('categories')
			->where('parent_id ='.$category['id'])
			->queryAll();
			$categories[$key]['childs'] = $childs;
		}
		return $categories;
	}
	
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
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
            'Childs' => array(self::HAS_MANY, 'Categories', 'parent_id'),
			'adverts' => array(self::HAS_MANY, 'Adverts', 'category_id'),
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
