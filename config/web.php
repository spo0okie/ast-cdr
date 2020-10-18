<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'formatter' => [
            'locale' => 'ru-RU',
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '6}d#FmS6+R|4ttknAAq|`}Z$?7Nf2BZ}Q\'eF$LYOG6"[N(+2"nc4`~CD$6/!jz)',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
				/*[
					'class' => 'yii\rest\UrlRule',
					'controller' => [
						'events'	=> 'rest/events',
						'calls'		=> 'rest/calls',
					],
					'pluralize' => false,
					'prefix' => 'rest'
				],*/
				'POST json/events/push'	=> 'json/events/create',
				'POST json/events'		=> 'json/events/create',
				'POST json'				=> 'json/events/create',
				'POST json/chans/push'	=> 'json/chan-events/create',
				'POST json/chan-events/push'=>'json/chan-events/create',
				'json/chans/test'		=> 'json/chan-events/test',
			],
		],
	],
	'modules' => [
		'rest'		=> ['class' => 'app\modules\rest\Rest'],	//нативный REST API Yii2
		'json'		=> ['class' => 'app\modules\json\Json'],	//API по стандарту IS74 Open VPBX v.1
	],
	'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
