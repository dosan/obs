<?php

/**
 * This is the model class for table "questions".
 *
 * The followings are the available columns in table 'questions':
 * @property integer $id
 * @property string $title
 * @property string $description
 * @property integer $author_id
 * @property string $created_at
 * @property string $upadated_at
 * @property integer $category_id
 * @property integer $up
 * @property integer $down
 *
 * The followings are the available model relations:
 * @property Answers[] $answers
 * @property Users $author
 * @property QuestionsCategories $category
 */
class Questions extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'questions';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('title, description, category_id', 'required'),
			array('author_id, category_id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>200),
			array('upadated_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, title, description, author_id, created_at, upadated_at, category_id, up, down', 'safe', 'on'=>'search'),
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
			'ratingsCount'=>array(self::STAT, 'QuestionsRatings', 'question_id'),
			'answersCount' => array(self::STAT, 'Answers', 'question_id'),
			'ratings' => array(self::HAS_MANY, 'QuestionsRatings', 'question_id'),
			'answers' => array(self::HAS_MANY, 'Answers', 'question_id'),
			'author' => array(self::BELONGS_TO, 'User', 'author_id'),
			'category' => array(self::BELONGS_TO, 'QuestionsCategories', 'category_id'),
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
			'author_id' => 'Author',
			'created_at' => 'Created At',
			'upadated_at' => 'Upadated At',
			'category_id' => 'Category',
			'up' => 'Up',
			'down' => 'Down',
		);
	}


	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);
		$criteria->compare('title',$this->title,true);
		$criteria->compare('description',$this->description,true);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('upadated_at',$this->upadated_at,true);
		$criteria->compare('category_id',$this->category_id);
		$criteria->compare('up',$this->up);
		$criteria->compare('down',$this->down);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
