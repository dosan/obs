<?php
use yii\helpers\Url;
class AdvertsController extends Controller
{

	public $layout='//layouts/main';
	public $type=0;	
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			'postOnly + delete', // we only allow deletion via POST request
		);
	}

	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'index' and 'view' actions
				'actions'=>array('index','view', 'adverts'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','addtofavorites', 'removefromfavorites', 'my', 'favorites', 'category'),
				'users'=>array('@'),
			),
			array('allow',
				'actions'=>array('update'),
				'users'=>array(Yii::app()->user->name),
				'expression'=>array('AdvertsController', 'allowOnlyOwner'),
				),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'setIsActive', 'update'),
				'users'=>array('donald'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionView($id)
	{
		$model = $this->loadModel($id);

		$dir = realpath(Yii::app()->basePath.'/../images/obs').'/ob-'.$model->id;
		if (!file_exists($dir) && !is_dir($dir)) {
			mkdir($dir, 0777);
		}
		$model->watches++;
		$advert = $model->getAdvert($id);
		$inFavs = Favorites::model()->find('user_id=:user_id AND ob_id=:ad_id', array(':user_id'=>Yii::app()->user->id, ':ad_id'=>$id));
		$model->save();
		$this->render('view',array(
			'advert'=>$advert,
			'inFavs'=>$inFavs,
		));
	}

	public function actionCreate()
	{
		$user = Users::model()->findByPk(Yii::app()->user->id);
		$model=new Adverts;
		if(isset($_POST['Adverts']))
		{
			$model->attributes=$_POST['Adverts'];
			$model->author_id=Yii::app()->user->id;
			$model->category_id=$_POST['Adverts']['category_id'];
			$verifyCode = md5(rand(0,9999));
			$model->verify_code = $verifyCode;
			$uploadedFile = CUploadedFile::getInstancesByName('images');
			$data = date('H-i-s');
			if($model->save())
			{
				$data = mkdir(realpath(Yii::app()->basePath.'/../images/obs').'/ob-'.$model->id, 0777);
				if (isset($uploadedFile) && count($uploadedFile) > 0) {
					// go through each uploaded image
					foreach ($uploadedFile as $pic) {
						$fileName = md5(rand(0,9999)).'.'.$pic->getExtensionName();
						$pic->saveAs(Yii::app()->basePath.'/../images/obs/ob-'.$model->id.'/'.$fileName);
					}
				}
				$this->sendActivationCode($user->email, $user->username, $verifyCode);
				$this->redirect(array('view', 'id'=>$model->id));
			}
		}
		$this->render('create',array(
			'model'=>$model,
		));
	}
	public function sendActivationCode($email, $name, $verification_code){
		$message = 'Hello World! verify your advert '.Yii::app()->baseUrl.'adverts/verify/'.$verification_code;
		die($message);
		$mailer = Yii::createComponent('application.extensions.mailer.EMailer');
		$mailer->Host = 'smtp.gmail.com';
		$mailer->IsSMTP();
		$mailer->SMTPAuth = true;
		$mailer->From = 'foo@gmail.com';
		$mailer->AddAddress('bratdonald@gmail.com');
		$mailer->FromName = 'Dosan';
		$mailer->CharSet = 'UTF-8';
		$mailer->Subject = Yii::t('demo', 'Yii rulez!');
		$mailer->Body = $message;
		$mailer->Send();
	/*	$name='=?UTF-8?B?'.base64_encode($name).'?=';
		$subject='=?UTF-8?B?'.base64_encode('it works!').'?=';
		$headers="From: $name <{$email}>\r\n".
			"Reply-To: {$email}\r\n".
			"MIME-Version: 1.0\r\n".
			"Content-Type: text/plain; charset=UTF-8";

		die(mail('mr.seitkanov@gmail.com',$subject,'Hello World!'.$verification_code,$headers));
		Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
		$this->refresh();*/
		
	}

	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		$user = $model->author_id;
		//change user_id to Yii::app()->user-id

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);
		$images = $model->getImagesById($id);
		if(isset($_POST['Adverts']))
		{
			$model->attributes=$_POST['Adverts'];
			$model->author_id=Yii::app()->user->id;
			$uploadedFile = CUploadedFile::getInstancesByName('images');
			if($model->save()){
				if (isset($uploadedFile) && count($uploadedFile) > 0) {
					// go through each uploaded image
					foreach ($uploadedFile as $pic) {
						$fileName = md5(rand(0,9999)).'.'.$pic->getExtensionName();
						$pic->saveAs(Yii::app()->basePath.'/../images/obs/ob-'.$id.'/'.$fileName);
					}
				}
				if (isset($_POST['Adverts']['remove']))
					foreach ($_POST['Adverts']['remove'] as $image => $checked)
						if ($checked)
							unlink(Yii::app()->basePath."/../images/obs/ob-".$id.'/'.$image);
				$this->redirect(array('view','id'=>$model->id));
			}
		}
		$this->render('update',array(
			'model'=>$model,
			'images'=>$images
		));
	}

	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();
		$dir = realpath(Yii::app()->basePath.'/../images/obs').'/ob-'.$model->id;
		if (!file_exists($dir) && !is_dir($dir)) {
			mkdir($dir, 0777);
		}
		CFileHelper::removeDirectory(Yii::app()->basePath."/../images/obs/ob-".$id);
		// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
		if(!isset($_GET['ajax'])){
			$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
		}
	}

	public function actionCategory(){
		$actions = explode('/',Yii::app()->request->pathInfo);
		if ($actions[0] == 'buy') {
			$type = 1;
		}elseif($actions[0] == 'cell'){
			$type = 0;
		}
		if (isset($_GET['cat_url']) AND $_GET['cat_url'] != '') {
			$cat_url = trim($_GET['cat_url']);
			//$categories = explode('/', $cat_url);
			$category = AdvertsCategories::model()->findByAttributes(array('cat_url'=>$cat_url));
			if($category){
				if ($category->parent_id == 0) {
					$adverts= Yii::app()->db->createCommand()
					->select('ad.id, ad.title, ad.description')
					->from('adverts ad')
					->leftJoin('categories cat', 'ad.category_id = cat.id')
					->leftJoin('categories pcat', 'cat.parent_id = pcat.id')
					->where('pcat.id = '.$category->id)
					->group('ad.id')
					->queryAll();
				}else{
					$adverts = Adverts::model()->findAll('category_id = '.$category->id);
				}
				$this->render('category',array(
					'category'=>$category,
					'adverts'=>$adverts
				));
				/*if (isset($categories[1])) {
					$child = AdvertsCategories::model()->findByAttributes(array('cat_url'=>$categories[1],'parent_id'=>$category->id));
					if ($child) {
						echo 'child works';
					}else{
						throw new CHttpException(405,'Указанная запись не найдена');
					}
				}*/
			}else{
				throw new CHttpException(405,'Указанная запись не найдена');
			}
		}else{
			echo 'something wrong';
		}
	}
	public function actionAdverts(){
		$actions = explode('/',Yii::app()->request->pathInfo);
		$type = $actions[0] == 'buy' ? 1 : 0;


	}
	public function actionIndex()
	{
		$categories = Categories::model()->getCategories();
		$buyAdverts = Adverts::getAdvertsByType(1);
		$cellAdverts = Adverts::getAdvertsByType(0);
		$this->render('index',array(
			'categories'=>$categories,
			'buyAdverts'=>$buyAdverts,
			'cellAdverts'=>$cellAdverts,
		));
	}

	public function actionAdmin()
	{
		$model=new Adverts('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['Adverts']))
			$model->attributes=$_GET['Adverts'];
		$this->render('admin',array(
			'model'=>$model,
		));
	}
	public function actionMy(){
		$adverts = Yii::app()->db->createCommand()
			->select('ad.id, ad.title, ad.description')
			->from('adverts ad')
			->leftJoin('favorites fav', 'ad.id = fav.ob_id')
			->where('fav.user_id = :user_id', array(':user_id'=>Yii::app()->user->id))
			->group('ad.title')
			->queryAll();
		$this->render('my',array(
			'adverts'=>$adverts,
		));
	}

	public function actionFavorites(){
		$adverts = Yii::app()->db->createCommand()
			->select('ad.id, ad.title, ad.description')
			->from('adverts ad')
			->where('ad.author_id = :user_id', array(':user_id'=>Yii::app()->user->id))
			->group('ad.title')
			->queryAll();
		$this->render('my',array(
			'adverts'=>$adverts,
		));
	}

	public function actionAddToFavorites($ad_id)
	{
		$model = new Favorites();
		$model->user_id = Yii::app()->user->id;
		$model->ob_id = $ad_id;
		$record = Favorites::model()->find('user_id=:user_id AND ob_id=:ob_id',
									array(':user_id'=>Yii::app()->user->id, ':ob_id'=>$ad_id));
		if ($record == null && $model->save()){
			$this->redirect(array('view', 'id'=>$ad_id));
		}else{
			$model->addError('duplicate', 'This advert is into your favorites.');
			echo json_encode($model->getErrors());
		}
	}

	public function actionRemoveFromFavorites($ad_id)
	{
		$model = new Favorites();
		$record = $model->find('user_id=:user_id AND ob_id=:ad_id',
					array(':user_id'=>Yii::app()->user->id, ':ad_id'=>$ad_id));
		if ($record != null && $record->delete()){
			$this->redirect(array('view', 'id'=>$ad_id));
		}else{
			$model->addError('notexist', 'This advert not exist in your favorites.');
			echo print_r($model->getErrors());
		}
	}

	public function actionSetIsActive(){
		$model = $this->loadModel($_POST['item']);
		$model->activate = (int)$_POST['checked'];
		echo $model->save() ? 'success' : false;
	}

	public function verify($verification_code = null){
		if ($verification_code == null && $verification_code == '') throw new CHttpException(404,"Error Processing Request");
		$records = Adverts::model()->findByAttributes(array('verify_code'=>$verification_code));  
		if ($records > 0)
			$message = array( 'success' => "Email Verified Successfully!"); 
		else
			$message = array( 'error' => "Sorry Unable to Verify Your Email!"); 
		$data['message'] = $message; 
		$this->render('activated.php', $data);   
	}
	public function loadModel($id)
	{
		$model=Adverts::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='advert-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
	/**
	 * Allow only the owner to do the action
	 * @return boolean whether or not the user is the owner
	 */
	public function allowOnlyOwner(){
		$model = Adverts::model()->findByPk($_GET["id"]);
		return $model->author_id === Yii::app()->user->id;
	}
}
