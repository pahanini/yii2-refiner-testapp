Test application for yii2-refiner
=================================

- copy `composer.phar` to project directory
- run `php composer.phar install` to install requited packages
- copy `cp tests/config/main-local.php.sample tests/config/main-local.php`
- edit `tests/config/main-local.php`
- change directory to tests and run `php yii migrate`
- chenge directory to parent `cd ..`
- run `/vendor/bin/codecept build` to prepare tests
- run `/vendor/bin/codecept run` to run tests
