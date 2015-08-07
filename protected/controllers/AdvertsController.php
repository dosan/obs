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
				'actions'=>array('index','view', 'adverts','verify'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','addtofavorites', 'removefromfavorites', 'my', 'favorites', 'category', 'activate'),
				'users'=>array('@'),
			),
			array('allow',
				'actions'=>array('update', 'delete'),
				'users'=>array(Yii::app()->user->name),
				'expression'=>array('AdvertsController', 'allowOnlyOwner'),
				),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete', 'setIsActive', 'update', 'lastactivated'),
				'users'=>array('donald', 'dosan'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionView($id)
	{
		$model = $this->loadModel($id);
		//find in favorites 
		foreach ($model->author->favorites as $favs)
			$favs['id'] == $id AND  $inFavs = true;
		
		$dir = realpath(Yii::app()->basePath.'/../images/obs').'/ob-'.$model->id;
		if (!file_exists($dir) && !is_dir($dir)) {
			mkdir($dir, 0777);
		}
		$inFavs = Favorites::model()->find('user_id=:user_id AND ob_id=:ad_id', array(':user_id'=>Yii::app()->user->id, ':ad_id'=>$id));
		$this->render('view',array(
			'advert'=>$model,
			'inFavs'=>$inFavs,
		));
		// Просмотры
		$model->watches++;
		$model->save();
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
				$dir = realpath(Yii::app()->basePath.'/../images/obs').'/ob-'.$model->id;
				if (!file_exists($dir) && !is_dir($dir)) {
					$data = mkdir($dir, 0777);
				}
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

	public function actionUpdate($id)
	{
		$model=$this->loadModel($id);
		$user = $model->author_id;
		//change user_id to Yii::app()->user-id

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['Adverts']))
		{
			$dir = realpath(Yii::app()->basePath.'/../images/obs').'/ob-'.$model->id;
			if (!file_exists($dir) && !is_dir($dir)) {
				$data = mkdir($filePath, 0777);
			}
			$model->attributes=$_POST['Adverts'];
			$model->author_id=Yii::app()->user->id;
			$uploadedFile = CUploadedFile::getInstancesByName('images');
			$model->updated_at = new CDbExpression('NOW()');
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
		));
	}

	public function actionDelete($id)
	{
		$this->loadModel($id)->delete();
		$dir = realpath(Yii::app()->basePath.'/../images/obs').'/ob-'.$id;
		if (!file_exists($dir) && !is_dir($dir)) {
			rmdir($dir);
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
					//die(print_r($category->childs[0]->adverts));
					$adverts= Yii::app()->db->createCommand()
					->select('ad.id, ad.title, ad.description')
					->from('adverts ad')
					->leftJoin('categories cat', 'ad.category_id = cat.id')
					->leftJoin('categories pcat', 'cat.parent_id = pcat.id')
					->where('pcat.id = :cat_id AND ad.type = :type AND pcat.parent_id = :parent_id', array(':cat_id'=>$category->id, ':type'=>$type, ':parent_id'=>0))
					->group('ad.id')
					->queryAll();
				}else{
					//die(print_r($category->adverts));
					$adverts = Adverts::model()->findAll('category_id = :cat_id AND type = :type', array(':cat_id'=>$category->id,':type'=>$type));
				}
				$this->render('category',array(
					'category'=>$category,
					'adverts'=>$adverts
				));
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
		$buyAdverts = Adverts::model()->getAdvertsByType(1, 10);
		$cellAdverts = Adverts::model()->getAdvertsByType(0, 10);
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
			->where('ad.author_id = :user_id', array(':user_id'=>Yii::app()->user->id))
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
			->leftJoin('favorites fav', 'ad.id = fav.ob_id')
			->where('fav.user_id = :user_id', array(':user_id'=>Yii::app()->user->id))
			->group('ad.title')
			->queryAll();
		$this->render('favorites',array(
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
			//echo json_encode($model->getErrors());
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
			//echo print_r($model->getErrors());
		}
	}
	public function actionSetIsActive(){
		$model = $this->loadModel($_POST['item']);
		$model->activate = (int)$_POST['checked'];
		if ($model->save()) {
			echo 'success';
		}else{
			throw new CHttpException(405, "Unknown Error");
		}
	}

	public function actionVerify($ver_code = null){
		if ($ver_code == null && $ver_code == '') throw new CHttpException(404,"Error Processing Request");
		$model = Adverts::model()->findByAttributes(array('verify_code'=>$ver_code));
		if ($model){

			$model->status = 1;
			$model->activated_at = new CDbExpression('NOW()');
			$model->verify_code = null;
			if ($model->save()){
				$message = array( 'success' => "Email Verified Successfully!"); 
			} 
			else
				throw new CHttpException(404,"Error Processing Request");
		}else
			throw new CHttpException(404,"This advert already actvated");
		$data['message'] = $message; 
		$this->render('activated', array('data' => $data));   
	}

	public function actionActivate($id){
		if ($id != null && is_numeric($id)) {
			$model = $this->loadModel($id);
			if ($model->author_id == Yii::app()->user->id || $model->author_id == $user_id) {
				$model->status = $model->status ? 0 : 1;
				$model->activated_at = new CDbExpression('NOW()');
				if ($model->save()){
					if($model->status)
						$this->redirect(array('view', 'id'=>$id));
					else
						$this->redirect(array('view', 'id'=>$id));
				}else{
					throw new CHttpException(404, "Unknown error!");
				}
			}else{
				throw new CHttpException(404, "I am so sorry bro!");
			}
		}else{
			throw new CHttpException(404, "Error Processing Request");
		}
	}
	public function actionLastActivated(){
		$model = Yii::app()->db->createCommand
		//change 1 minute to one week;
		("SELECT a.id, a.title, a.author_id, u.username, u.email FROM `adverts` AS a LEFT JOIN `users` AS u ON a.author_id=u.id WHERE activated_at BETWEEN NOW() - INTERVAL 1 MONTH AND NOW()- INTERVAL 1 MINUTE")->queryAll();
		foreach ($model as $advert) {
			$this->sendToReactivate($advert['username'], $advert['email'], $advert['title'], $advert['id']);
		}
	}
	public function sendToReactivate($user_name, $user_email, $advert_title, $advert_id){
		$message = 'Hello '.$user_name.'! your advert '.$advert_title.' deactivated.'. 
		CHtml::link($advert_title,array('adverts/view', 'id'=>$advert_id));
		//die($message);
		$mailer = Yii::createComponent('application.extensions.mailer.EMailer');
		//$mailer->SMTPDebug = 4;   
		$mailer->IsSMTP();
		$mailer->SMTPAuth = true;
		$mailer->SMTPSecure = 'ssl';
		$mailer->Host = 'smtp.yandex.ru';
		$mailer->Port = 465;
		$mailer->From = 'test@myastana.kz';
		$mailer->Username = "test@myastana.kz";
		$mailer->Password = "test123";
		$mailer->AddAddress($email);
		$mailer->FromName = 'MyAstana';
		$mailer->CharSet = 'UTF-9';
		$mailer->Subject = 'oeuaoeaoe';
		$mailer->IsHTML(true);
		$mailer->Body = $message;
		$mailer->Send();
	}
	public function sendActivationCode($email, $name, $verification_code){
		$message = 'Hello '.$name.'! your advert created. Activation link here '.Yii::app()->baseUrl.'index.php/adverts/verify/ver_code/'.$verification_code;
		//die($message);
		$mailer = Yii::createComponent('application.extensions.mailer.EMailer');
		//$mailer->SMTPDebug = 4;   
		$mailer->IsSMTP();
		$mailer->SMTPAuth = true;
		$mailer->SMTPSecure = 'ssl';
		$mailer->Host = 'smtp.yandex.ru';
		$mailer->Port = 465;
		$mailer->From = 'test@myastana.kz';
		$mailer->Username = "test@myastana.kz";
		$mailer->Password = "test123";
		$mailer->AddAddress($email);
		$mailer->FromName = 'MyAstana';
		$mailer->CharSet = 'UTF-9';
		$mailer->Subject = 'Activate your advert';
		$mailer->IsHTML(true);
		$mailer->Body = $message;
		$mailer->Send();
	}

	public function actionDeactivate($id){
		$model = $this->loadModel($id);
		$model->status = 0;
		if ($model->save()) {
			return true;
		}else{
			return false;
		}
	}

	public function loadModel($id){
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
	public static function allowOnlyOwner(){
		$model = Adverts::model()->findByPk($_GET["id"]);
		return $model->author_id === Yii::app()->user->id;
	}
}
