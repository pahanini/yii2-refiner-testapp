<?php

use tests\unit\DbTestCase;
use pahanini\refiner\common\Base;

class BaseTest extends DbTestCase
{
    use \Codeception\Specify;

    /**
     * @var pahanini\refiner\common\Base
     */
    private $base;

    public function testParamsParsing()
    {
        $this->base = new Base();
        $this->base->name = 'category';

        $this->specify("parse params", function() {
                $_GET['category'] = [1,2];
                $this->assertSame([1,2], $this->base->getParams());
                $this->base->paramType = 'string';
                $this->assertSame(['1','2'], $this->base->getParams());
                $_GET['category'] = 1;
                $this->assertSame('1', $this->base->getParams());
                $this->base->paramToArray = true;
                $this->assertSame(['1'], $this->base->getParams());
            });
    }
}
