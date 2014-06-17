<?php

use tests\unit\DbTestCase;
use tests\models\Product;
use pahanini\refiner\Set;
use tests\unit\fixtures\ProductFixture;

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
                ]
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
        $_GET['price'] = ['max' => 400, 'min' => 200];
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
}
