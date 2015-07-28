<?php

/**
 * This is the model class for table "obyavlenie".
 *
 * The followings are the available columns in table 'obyavlenie':
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property string $price
 * @property integer $contact
 * @property string $created_at
 * @property string $updated_at
 * @property integer $author_id
 */
class Obyavlenie extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'obyavlenie';
	}
	public function getData(){
		return self::model()->findAll();
	}
	/**
	 * @return array validation rules for model attributes.
	 */
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

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'categories'   => array(self::HAS_MANY,   'Categories',    'id'),
			'images'   => array(self::HAS_MANY,   'Images',    'id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
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
	 * @return Obyavlenie the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
