<?php

class AdvertController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/main';
	public $imgsPath='';
	public $obImages;
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
		$advert = Yii::app()->db->createCommand()
			->select('ad.id, ad.title, ad.description, ad.contact, ad.price,  ad.category_id as cat_id,cat.title as cat_name')
			->from('adverts ad')
			->join('categories cat', 'cat.id = ad.category_id')
			->where('ad.id=:id', array(':id'=>$id))
			->queryRow();
		$files = CFileHelper::findFiles(Yii::app()->basePath."/../images/ads/ad-".$id);
		$images = array_map(function($n){return end(explode('/', $n));}, $files);
		$inFavs = Favorites::model()->find('user_id=:user_id AND ob_id=:ad_id', array(':user_id'=>$user_id, ':ad_id'=>$id));
		$this->render('view',array(
			'advert'=>$advert,
			'inFavs'=>$inFavs,
			'images'=>$images
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	public function actionCreate()
	{
		$model=new Advert;
		$user_id = 1;

		if(isset($_POST['Advert']))
		{
			$model->attributes=$_POST['Advert'];
			$model->author_id = $user_id;
			$model->category_id = $_POST['Advert']['category_id'];
			$uploadedFile = CUploadedFile::getInstancesByName('images');
			$data = date('H-i-s');
			if($model->save())
			{
				$data = mkdir(realpath(Yii::app()->basePath.'/../images/ads').'/ad-'.$model->id, 0777);
				if (isset($uploadedFile) && count($uploadedFile) > 0) {
					// go through each uploaded image
					foreach ($uploadedFile as $pic) {
						$fileName = md5(rand(0,9999)).'.'.$pic->getExtensionName();
						$pic->saveAs(Yii::app()->basePath.'/../images/ads/ad-'.$model->id.'/'.$fileName);
					}
				}
				$verifyCode = md5(rand(0,9999));
				$this->sendActivationCode('email@dot.com', 'user_name', $verifyCode);
				$this->redirect(array('view', 'id'=>$model->id));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function sendActivationCode($email, $name, $verification_code){
		return true;
		/*$message = 'Hello World!';
		$mailer = Yii::createComponent('application.extensions.mailer.EMailer');
		$mailer->Host = <your smtp host>;
		$mailer->IsSMTP();
		$mailer->From = 'wei@example.com';
		$mailer->AddReplyTo('wei@example.com');
		$mailer->AddAddress('qiang@example.com');
		$mailer->FromName = 'Wei Yard';
		$mailer->CharSet = 'UTF-8';
		$mailer->Subject = Yii::t('demo', 'Yii rulez!');
		$mailer->Body = $message;
		$mailer->Send();

		$mail=Yii::app()->Smtpmail;
		$mail->SetFrom('sandor@sandor.cu.cc', 'dosan');
		$mail->Subject = 'subject';
		$message = "Dear ".$name.",\nPlease click on below URL or paste into your browser to verify your Email Address\n\n ".Yii::app()->basePath.'advert/verify/'.$verification_code."\n"."\n\nThanks\nAdmin Team";
		$mail->MsgHTML($message);
		$mail->AddAddress($email, "");
		if(!$mail->Send()) {
			die($mail->ErrorInfo);
		}else {
			die("Message sent!");
		}*/
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
		$files = CFileHelper::findFiles(Yii::app()->basePath."/../images/ads/ad-".$id);
		$images = array_map(function($n){return end(explode('/', $n));}, $files);
		if(isset($_POST['Advert']))
		{
			$model->attributes=$_POST['Advert'];
			$model->author_id=$user_id;
			$uploadedFile = CUploadedFile::getInstancesByName('images');
			if($model->save()){
				if (isset($uploadedFile) && count($uploadedFile) > 0) {
					// go through each uploaded image
					foreach ($uploadedFile as $pic) {
						$fileName = md5(rand(0,9999)).'.'.$pic->getExtensionName();
						$pic->saveAs(Yii::app()->basePath.'/../images/ads/ad-'.$id.'/'.$fileName);
					}
				}
				if (isset($_POST['Advert']['remove']))
					foreach ($_POST['Advert']['remove'] as $image => $isTrue)
						if ($isTrue)
							unlink(Yii::app()->basePath."/../images/ads/ad-".$id.'/'.$image);

				$this->redirect(array('view','id'=>$model->id));
			}
		}
		$imageModel = new Images;
		$this->render('update',array(
			'model'=>$model,
			'imageModel'=>$imageModel,
			'images'=>$images
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
		$adverts = Yii::app()->db->createCommand()
			->select('ad.id, ad.title, ad.description')
			->from('adverts ad')
			->limit(4)
			->group('ad.title')
			->queryAll();
		$this->render('index',array(
			'categories'=>$categories,
			'adverts'=>$adverts,
		));
	}

	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{
		$model=new Advert('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Advert']))
			$model->attributes=$_GET['Advert'];

		$this->render('admin',array(
			'model'=>$model,
		));
	}
	public function actionMy(){
		$user_id = 1;

		$adverts = Yii::app()->db->createCommand()
			->select('ad.id, ad.title, ad.description')
			->from('adverts ad')
			->where('author_id = :user_id', array(':user_id'=>$user_id))
			->group('ad.title')
			->queryAll();
		$this->render('my',array(
			'adverts'=>$adverts,
		));
	}
	public function actionFavorites(){
		$user_id = 1;
		
		$adverts = Yii::app()->db->createCommand()
			->select('ad.id, ad.title, ad.description')
			->from('adverts ad')
			->leftJoin('favorites fav', 'ad.id = fav.ob_id')
			->where('fav.user_id = :user_id', array(':user_id'=>$user_id))
			->group('ad.title')
			->queryAll();
		$this->render('my',array(
			'adverts'=>$adverts,
		));
	}
	/**
	 * add to favorites the advert.
	 * @param integer $ad_id the advert ID of the model to be updated
	 * @return bolean updated or not
	 */
	public function actionAddToFavorites($ad_id)
	{
		// user_id should change Yii:app()->user->id
		$instance = new Favorites();
		$user_id = 1;
		$instance->user_id = $user_id;
		$instance->ob_id = $ad_id;
		$record = Favorites::model()->find('user_id=:user_id AND ob_id=:ob_id', array(':user_id'=>$user_id, ':ob_id'=>$ad_id));
		if ($record == null){
			$instance->save();
			print_r(array('succes' => true, 'message' => 'added successful'));
		}else{
			$instance->addError('duplicate', 'You already added into your favorites.');
			print_r($instance->getErrors());
		}
	}

	/**
	 * add to favorites the advert.
	 * @param integer $ad_id the advert ID of the model to be updated
	 * @return bolean updated or not
	 */
	public function actionRemoveFromFavorites($ad_id)
	{
		// user_id should change Yii:app()->user->id
		$instance = new Favorites();
		$user_id = 1;
		$instance->user_id = $user_id;
		$instance->ob_id = $ad_id;
		$record = Favorites::model()->find('user_id=:user_id AND ob_id=:ad_id', array(':user_id'=>$user_id, ':ad_id'=>$ad_id));
		if ($record != null && $record->delete()){
			// delete the row from the database table
			print_r(array('succes' => true, 'message' => 'deleted successful'));
		}else{
			$instance->addError('notexist', 'This advert not exist in your favorites.');
			print_r($instance->getErrors());
		}

	}

	public function verify($verification_code = null){
		if ($verification_code == null && $verification_code == '') show_404();
		$records = $this->users_model->verifyEmailAddress($verification_code);  
		if ($records > 0)
			$message = array( 'success' => "Email Verified Successfully!"); 
		else
			$message = array( 'error' => "Sorry Unable to Verify Your Email!"); 
		$data['message'] = $message; 
		$this->load->view('activated.php', $data);   
	}

	public function loadModel($id)
	{
		$model=Advert::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param Advert $model the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='advert-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
