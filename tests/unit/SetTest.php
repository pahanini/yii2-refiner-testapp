<?php

use tests\unit\DbTestCase;
use tests\models\Product;
use pahanini\refiner\Set;
use tests\unit\fixtures\ProductFixture;
use \yii\caching\ExpressionDependency;

class SetTest extends DbTestCase
{
    use \Codeception\Specify;

    /**
     * @var \pahanini\refiner\Set
     */
    private $set;

    public function fixtures()
    {
        return [
            'product' => ProductFixture::className()
        ];
    }


    public function testAll()
    {
        $this->set = new Set([]);
        $this->set->enableCache = false;
        $this->specify("correct work with empty values", function() {
            $this->assertEquals([], $this->set->getRefiners());
        });

        $baseQuery = Product::find()->andWhere("balance > 0");
        $this->set = new Set([
            'baseQuery' => clone $baseQuery,
            'refiners' => [
                'price' => [
                    'paramType' => 'int',
                    'class' => 'pahanini\refiner\db\Range'
                ],
                'name' => [
                    'class' => 'pahanini\refiner\db\Match'
                ],
                'category_id' => [
                    'class' => 'pahanini\refiner\db\Count'
                ],
                'has_discount' => [
                    'class' => 'pahanini\refiner\db\Checkbox',
                    'valueFilter' => 'has_discount = 1',
                ],
            ]
        ]);

        $this->specify(
            "check refines 1",
            function () {
                $query = $this->set->getRefinedQuery();
                $this->assertEquals(4, count($query->all()));
                $this->assertEquals(
                    [
                        'all' => ['min' => 100, 'max' => 500],
                        'active' => ['min' => 100, 'max' => 500],
                    ],
                    $this->set->getRefiner('price')->getValues()
                );
                $this->assertEquals(
                    [
                        'all' => 3, 'active' => 3
                    ],
                    $this->set->getRefiner('has_discount')->getValues()
                );
                $this->assertEquals(
                    [],
                    $this->set->getRefiner('name')->getValues()
                );
                $this->assertEquals(
                    [
                        ['id' => 100, 'active' => 2, 'all' => 2],
                        ['id' => 200, 'active' => 1, 'all' => 1],
                        ['id' => 300, 'active' => 1, 'all' => 1],
                    ],
                    $this->set->getRefiner('category_id')->getValues()
                );
            }
        );

        $this->set->setBaseQuery(clone $baseQuery);
        $_GET['name'] = ['Product'];
        $_GET['price'] = '200,400';
        $this->specify(
            "check refines 2",
            function () {
                $query = $this->set->getRefinedQuery();
                $this->assertEquals(2, count($query->all()));
                $this->assertEquals(
                    [
                        'all' => ['min' => 100, 'max' => 500],
                        'active' => ['min' => 100, 'max' => 400],
                    ],
                    $this->set->getRefiner('price')->getValues()
                );
                $this->assertEquals(
                    [
                        'all' => 3, 'active' => 1
                    ],
                    $this->set->getRefiner('has_discount')->getValues()
                );
                $this->assertEquals(
                    ['Product'],
                    $this->set->getRefiner('name')->getValues()
                );
            }
        );
    }

    public function testCache()
    {
        Yii::$app->cache->flush();
        //Yii::$app->params->test = 1;

        $baseQuery = Product::find()->andWhere("balance > 0");
        $this->set = new Set([
            'baseQuery' => clone $baseQuery,
            'refiners' => [
                'price' => [
                    'paramType' => 'int',
                    'class' => 'pahanini\refiner\db\Range'
                ],
                'name' => [
                    'class' => 'pahanini\refiner\db\Match'
                ],
                'category_id' => [
                    'class' => 'pahanini\refiner\db\Count'
                ],
                'has_discount' => [
                    'class' => 'pahanini\refiner\db\Checkbox',
                    'valueFilter' => 'has_discount = 1',
                ],
            ]
        ]);

        $expression = new ExpressionDependency();
        //$expression->expression = 'Yii::$app->params->test';
        $this->set->cache(100, new ExpressionDependency());

        $this->specify(
            "check cache params",
            function() {
                $result = $this->set->getCacheInfo();
                $this->assertInstanceOf('\yii\caching\Cache', $result[0]);
                $this->assertEquals(100, $result[1]);
                $this->assertInstanceOf('\yii\caching\Dependency', $result[2]);
            });

        $this->specify(
            "check cache working",
            function() {
                $result1 = $this->set->getRefinerValues();
                Yii::$app->db->close();
                $result2 = $this->set->getRefinerValues();
                $this->assertEquals($result1, $result2);
                $this->assertFalse(Yii::$app->db->isActive);
        });




    }
}
