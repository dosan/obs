<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.extensions.phaActiveColumn.*',
		'application.modules.user.models.*',
        'application.modules.user.components.*',
	),
	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'questions',
		'user'=>array(
            # encrypting method (php hash function)
            'hash' => 'md5',
 
            # send activation email
            'sendActivationMail' => true,
 
            # allow access for non-activated users
            'loginNotActiv' => false,
 
            # activate user on registration (only sendActivationMail = false)
            'activeAfterRegister' => false,
 
            # automatically login from registration
            'autoLogin' => true,
 
            # registration path
            'registrationUrl' => array('/user/registration'),
 
            # recovery password path
            'recoveryUrl' => array('/user/recovery'),
 
            # login form path
            'loginUrl' => array('/user/login'),
 
            # page after login
            'returnUrl' => array('/user/profile'),
 
            # page after logout
            'returnLogoutUrl' => array('/user/login'),
        ),
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'12345',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
		
	),

	// application components
	'components'=>array(

  		'request' => array(
            'baseUrl' => 'http://localhost/yii/blog/',
        ),
        'user'=>array(
            // enable cookie-based authentication
            'class' => 'WebUser',
            'allowAutoLogin'=>true,
            'loginUrl' => array('/user/login'),
        ),
		'mailer' => array(
		  'class' => 'application.extensions.mailer.EMailer',
		  'pathViews' => 'application.views.email',
		  'pathLayouts' => 'application.views.email.layouts'
		),
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				//'adverts/<title>' => 'adverts/category/<title>',
				// site.com/adverts/printers/home/laser/15
                'cell/category/<cat_url:.+>/<id:\d+>'=>'adverts/category/',
                'buy/category/<cat_url:.+>/<id:\d+>'=>'adverts/category/',
                'cell/category/<cat_url:.+>'=>'adverts/category/',
                'buy/category/<cat_url:.+>'=>'adverts/category/',
                
				//'cell/<_a>'=>'adverts/<_a>',
				//'buy/<_a>'=>'adverts/<_a>',
                // site.com/adverts/printers/home/laser

				//'cell/<cat_name:\w+>'=>'adverts/category/?type=1',
				//'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				//'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		
/*		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),*/
		// uncomment the following to use a MySQL database
		'db'=>array(
			'connectionString' => 'mysql:host=localhost;dbname=marketplace',
			'emulatePrepare' => true,
			'username' => 'root',
			'password' => 'password',
			'charset' => 'utf8',
		),
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
		// this is used in contact page
		'adminEmail'=>'webmaster@example.com',
	),
);