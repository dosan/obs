<?php

class DefaultController extends Controller
{

	public function filters()
	{
		return array( 'accessControl' ); // perform access control for CRUD operations
	}

	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view', 'category'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update', 'rate', 'rightAnswer'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'setIsActive', 'update', 'lastactivated', 'createcategory'),
				'users'=>array('donald', 'dosan', 'admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	public function actionIndex()
	{
		$categories = QuestionsCategories::model()->findAll();
		$question = new Questions;
		$this->render('index', array('question'=> $question, 'categories'=>$categories));
	}
	public function actionView($id)
	{
		$answer = new Answers;
		if (isset($_POST['Answers'])) {
			$answer->attributes = $_POST['Answers'];
			$answer->author_id = Yii::app()->user->id;
			if ($answer->save())
				$this->redirect(array('view','id'=>$answer->question_id));
		}
		$question = $this->loadModel($id);
		$this->render('view', array('answer'=>$answer, 'question'=>$question));
	}

	public function actionCreate()
	{
		$model=new Questions;
		$categories=new QuestionsCategories;
		$model->author_id = Yii::app()->user->id;
		if(isset($_POST['Questions']))
		{
			$model->attributes=$_POST['Questions'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
		$this->render('create',array(
			'model'=>$model,
			'categories'=>$categories
		));
	}

	public function actionCategory($id){
		$category = QuestionsCategories::model()->findByPk($id);
		if($category){
			$this->render('category',array(
				'category'=>$category,
			));
		}else{
			throw new CHttpException(405,'Указанная запись не найдена');
		}
	}

	public function actionCreateCategory()
	{
		$model=new QuestionsCategories;
		$model->author_id = Yii::app()->user->id;
		if(isset($_POST['Questions']))
		{
			$model->attributes=$_POST['Questions'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		// $this->performAjaxValidation($model);
		if(isset($_POST['Questions']))
		{
			$model->attributes=$_POST['Questions'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}
		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();

		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax']))
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
	}
	public function actionRate(){
		if (isset($_POST['answer_id'])) {
			$model = AnswersRatings::model()->findByAttributes(array('answer_id'=>$_POST['answer_id'], 'user_id'=>Yii::app()->user->id, 'rate'=>$_POST['rate']));
			if (!is_object($model)) {
				$model = AnswersRatings::model()->findByAttributes(array('answer_id'=>$_POST['answer_id'], 'user_id'=>Yii::app()->user->id));
				if (is_object($model)) {
					$model->rate=$_POST['rate'];
					if ($model->update()) echo json_encode($_POST['rate']);
					else echo json_encode('Unexpected Error!');
				}else{
					$model = new AnswersRatings;
					$model->rate = $_POST['rate'];
					$model->user_id = Yii::app()->user->id;
					$model->answer_id = $_POST['answer_id'];
					if ($model->save())	echo json_encode($_POST['rate']);
					else echo json_encode('Sorry unexpected error');
				}
			}else{
				echo json_encode($_POST['rate']);
			}
		}else{
			$model = QuestionsRatings::model()->findByAttributes(array('question_id'=>$_POST['question_id'], 'user_id'=>Yii::app()->user->id, 'rate'=>$_POST['rate']));
			if (!is_object($model)) {
				$model = QuestionsRatings::model()->findByAttributes(array('question_id'=>$_POST['question_id'], 'user_id'=>Yii::app()->user->id));
				if (is_object($model)) {
					$model->rate=$_POST['rate'];
					if ($model->update()) echo json_encode($_POST['rate']);
					else echo json_encode('Unexpected Error!');
				}else{
					$model = new QuestionsRatings;
					$model->rate = $_POST['rate'];
					$model->user_id = Yii::app()->user->id;
					$model->question_id = $_POST['question_id'];
					if ($model->save())	echo json_encode($_POST['rate']);
					else echo json_encode('Sorry unexpected error');
				}
			}else{
				echo json_encode($_POST['rate']);
			}
		}

	}
	public function actionRightAnswer(){
		if($_POST['answer_id'] AND $_POST['right_answer']){
			$model = Answers::model()->findByPk($_POST['answer_id']);
			$model->right_answer = $_POST['right_answer'];
			if ($model->save()) {
				echo 'rigth answer is '.$model->description;
			}else{
				echo 'something went wrong';
			}
		}else{
			throw new CHttpException(404, 'The requested page does not exist.');
		}
	}
	public function actionAdmin()
	{
		$model=new Questions('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Questions']))
			$model->attributes=$_GET['Questions'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}



	public function loadModel($id)
	{
		$model=Questions::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='categories-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}