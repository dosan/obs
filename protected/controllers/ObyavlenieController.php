<?php

class ObyavlenieController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';

	public $type=0;
	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update','addtofavorites', 'removefromfavorites', 'my', 'favorites'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Displays a particular model.
	 * @param integer $id the ID of the model to be displayed
	 */
	public function actionView($id)
	{
		//user_id change to Yii::app()->user->id
		$user_id =1;
		$obyavlenie = Yii::app()->db->createCommand()
			->select('ob.id, ob.title, ob.description, ob.contact,ob.price,  ob.category_id as cat_id,cat.title as cat_name')
			->from('obyavlenie ob')
			->join('categories cat', 'cat.id = ob.category_id')
			->where('ob.id=:id', array(':id'=>$id))
			->queryRow();
		$inFavs = Favorites::model()->find('user_id=:user_id AND ob_id=:ob_id', array(':user_id'=>$user_id, ':ob_id'=>$id));
		$images = Yii::app()->db->createCommand("SELECT name FROM images WHERE ob_id = {$id}")->queryAll();
		$this->render('view',array(
			'obyavlenie'=>$obyavlenie,
			'images'=>$images,
			'inFavs'=>$inFavs
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Obyavlenie;
		$user_id = 1;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['Obyavlenie']))
		{
			$model->images = new Images;
			$user_id = 1;
			$rnd = rand(0,9999);  // generate random number between 0-9999
			$data = date('H-i-s');
			$model->attributes=$_POST['Obyavlenie'];
			$uploadedFile=CUploadedFile::getInstance($model,'name');
			$fileName = "{$rnd}-{$data}.".$uploadedFile->getExtensionName();  // random number + file name

			$model->images->name = $fileName;
			$model->author_id = $user_id;
			$model->category_id = $_POST['Obyavlenie']['category_id'];
			if($model->save())
			{
				$model->images->ob_id = $model->id;
				$uploadedFile->saveAs(Yii::app()->basePath.'/../images/'.$fileName);  // image will uplode to rootDirectory/banner/
				$model->images->save();
				$this->redirect(array('view', 'id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		//change user_id to Yii::app()->user-id
		$user_id = 1;
		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Obyavlenie']))
		{
			$model->attributes=$_POST['Obyavlenie'];
			$model->author_id = $user_id;
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */
	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}

	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$categories = Categories::model()->getCategories();
		$obyavlenies = Yii::app()->db->createCommand()
			->select('ob.id, ob.title, ob.description, im.name as img')
			->from('obyavlenie ob')
			->leftJoin('images im', 'im.ob_id = ob.id')
			->limit(4)
			->group('ob.title')
			->queryAll();
		$this->render('index',array(
			'categories'=>$categories,
			'obyavlenies'=>$obyavlenies,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Obyavlenie('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Obyavlenie']))
			$model->attributes=$_GET['Obyavlenie'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	public function actionMy(){
		$user_id = 1;

		$obyavlenies = Yii::app()->db->createCommand()
			->select('ob.id, ob.title, ob.description, im.name as img')
			->from('obyavlenie ob')
			->leftJoin('images im', 'im.ob_id = ob.id')
			->where('author_id = :user_id', array(':user_id'=>$user_id))
			->group('ob.title')
			->queryAll();
		$this->render('my',array(
			'obyavlenies'=>$obyavlenies,
		));
	}
	public function actionFavorites(){
		$user_id = 1;
		
		$obyavlenies = Yii::app()->db->createCommand()
			->select('ob.id, ob.title, ob.description, im.name as img')
			->from('obyavlenie ob')
			->leftJoin('images im', 'im.ob_id = ob.id')
			->leftJoin('favorites fav', 'ob.id = fav.ob_id')
			->where('fav.user_id = :user_id', array(':user_id'=>$user_id))
			->group('ob.title')
			->queryAll();
		$this->render('my',array(
			'obyavlenies'=>$obyavlenies,
		));
	}
	/**
	 * add to favorites the obyavlenie.
	 * @param integer $ob_id the obyavlenie ID of the model to be updated
	 * @return bolean updated or not
	 */
	public function actionAddToFavorites($ob_id)
	{
		// user_id should change Yii:app()->user->id
		$instance = new Favorites();
		$user_id = 1;
		$instance->user_id = $user_id;
		$instance->ob_id = $ob_id;
		$record = Favorites::model()->find('user_id=:user_id AND ob_id=:ob_id', array(':user_id'=>$user_id, ':ob_id'=>$ob_id));
		if ($record == null){
			$instance->save();
			print_r(array('succes' => true, 'message' => 'added successful'));
		}else{
			$instance->addError('duplicate', 'You already added into your favorites.');
			print_r($instance->getErrors());
		}
	}
	/**
	 * add to favorites the obyavlenie.
	 * @param integer $ob_id the obyavlenie ID of the model to be updated
	 * @return bolean updated or not
	 */
	public function actionRemoveFromFavorites($ob_id)
	{
		// user_id should change Yii:app()->user->id
		$instance = new Favorites();
		$user_id = 1;
		$instance->user_id = $user_id;
		$instance->ob_id = $ob_id;
		$record = Favorites::model()->find('user_id=:user_id AND ob_id=:ob_id', array(':user_id'=>$user_id, ':ob_id'=>$ob_id));
		if ($record != null && $record->delete()){
			 // delete the row from the database table
			print_r(array('succes' => true, 'message' => 'deleted successful'));
		}else{
			$instance->addError('notexist', 'This obyavlenie not exist in your favorites.');
			print_r($instance->getErrors());
		}

	}

	public function loadModel($id)
	{
		$model=Obyavlenie::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Obyavlenie $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='obyavlenie-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
