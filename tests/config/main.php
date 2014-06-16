<?php
return yii\helpers\ArrayHelper::merge(
    [
        'id' => 'yii2-refiner-testapp',
        'basePath' => dirname(__DIR__),
        'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
        'extensions' => require(__DIR__ . '/../../vendor/yiisoft/extensions.php'),
        'components' => [
            'cache' => [
                'class' => 'yii\caching\FileCache',
            ],
            'db' => [
                'class' => 'yii\db\Connection',
                'tablePrefix' => '',
            ],
            'sphinx' => [
                'class' => 'yii\sphinx\Connection',
            ],
        ],
    ],
    require(__DIR__ . '/main-local.php')
);
