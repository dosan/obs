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
 * @property Obyavlenie[] $obyavlenies
 */
class Favorites extends CActiveRecord
{

	public $user_id;
	public $ob_id;

	
	public static function model($className=__CLASS__)
    {
        return parent::model($className);
    }
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'favorites';
	}
/*	public function rules()
	{
		return array(

		);

	}*/
}