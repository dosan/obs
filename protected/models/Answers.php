<?php

/**
 * This is the model class for table "answers".
 *
 * The followings are the available columns in table 'answers':
 * @property integer $id
 * @property string $description
 * @property integer $author_id
 * @property string $created_at
 * @property string $updatet_at
 * @property integer $question_id
 * @property integer $up
 * @property integer $down
 *
 * The followings are the available model relations:
 * @property Users $author
 * @property Questions $question
 */
class Answers extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'answers';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('description, author_id, created_at, question_id, up, down', 'required'),
			array('author_id, question_id, up, down', 'numerical', 'integerOnly'=>true),
			array('description', 'length', 'max'=>1000),
			array('updatet_at', 'safe'),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('id, description, author_id, created_at, updatet_at, question_id, up, down', 'safe', 'on'=>'search'),
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
			'author' => array(self::BELONGS_TO, 'Users', 'author_id'),
			'question' => array(self::BELONGS_TO, 'Questions', 'question_id'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'description' => 'Description',
			'author_id' => 'Author',
			'created_at' => 'Created At',
			'updatet_at' => 'Updatet At',
			'question_id' => 'Question',
			'up' => 'Up',
			'down' => 'Down',
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
		$criteria->compare('description',$this->description,true);
		$criteria->compare('author_id',$this->author_id);
		$criteria->compare('created_at',$this->created_at,true);
		$criteria->compare('updatet_at',$this->updatet_at,true);
		$criteria->compare('question_id',$this->question_id);
		$criteria->compare('up',$this->up);
		$criteria->compare('down',$this->down);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return Answers the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
