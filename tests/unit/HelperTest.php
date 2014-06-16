<?php

use tests\unit\TestCase;
use pahanini\refiner\Helper;

class HelperTest extends TestCase
{
    use \Codeception\Specify;

    public function testExpand()
    {
        $this->specify("expands columns", function() {
                $array = [
                    ['id' => 1, 'data' => ['text' => 'string1', 'val' => 'val1']],
                    ['id' => 2],
                    ['id' => 3],
                ];
                $expected = [
                    ['id' => 1, 'text' => 'string1', 'val' => 'val1'],
                    ['id' => 2],
                    ['id' => 3],
                ];
                $result = Helper::expand($array, ['data']);
                $this->assertEquals($expected, $result);
            });
    }


    public function testMerge()
    {
        $this->specify("merge two equal arrays", function() {
                $array1 = [
                    ['_id' => 100, 'all' => 2],
                    ['_id' => 200, 'all' => 1],
                    ['_id' => 300, 'all' => 1],
                ];
                $array2 = [
                    ['_id' => 100, 'active' => 2],
                    ['_id' => 200, 'active' => 1],
                    ['_id' => 300, 'active' => 1],
                ];
                $expected = [
                    ['_id' => 100, 'all' => 2, 'active' => 2],
                    ['_id' => 200, 'all' => 1, 'active' => 1],
                    ['_id' => 300, 'all' => 1, 'active' => 1],
                ];
                $result = Helper::merge($array1, $array2, ['_id' => '_id']);
                $this->assertEquals($expected, $result);
            });
        $this->specify("merge two different arrays", function() {
                $array1 = [
                    ['id1' => 1, 'text' => 'string 1'],
                    ['id1' => 2, 'text' => 'string 2'],
                    ['id1' => 3, 'text' => 'string 3'],
                    ['id1' => 4, 'text' => 'string 4'],
                ];
                $array2 = [
                    ['id2' => 1, 'text' => 'string 1 active'],
                    ['id2' => 2, 'text' => 'string 2 active'],
                    ['id2' => 5, 'text' => 'string 5 active'],
                ];
                $expected = [
                    ['id1' => 1, 'id2' => 1, 'text' => 'string 1 active'],
                    ['id1' => 2, 'id2' => 2, 'text' => 'string 2 active'],
                    ['id1' => 3, 'text' => 'string 3'],
                    ['id1' => 4, 'text' => 'string 4'],
                    ['id2' => 5, 'text' => 'string 5 active'],
                ];
                $result = Helper::merge($array1, $array2, ['id1' => 'id2']);
                $this->assertEquals($expected, $result);
            });

        $this->specify("merge two arrays and replaces keys", function() {
                $array1 = [
                    ['id' => 1, 'text' => 'string 1'],
                    ['id' => 2, 'text' => 'string 2'],
                    ['id' => 3, 'text' => 'string 3'],
                    ['id' => 4, 'text' => 'string 4'],
                ];
                $array2 = [
                    ['id' => 1, 'text' => 'string 1 active'],
                    ['id' => 2, 'text' => 'string 2 active'],
                    ['id' => 5, 'text' => 'string 5 active'],
                ];
                $expected = [
                    ['id' => 1, 'text' => 'string 1 active'],
                    ['id' => 2, 'text' => 'string 2 active'],
                    ['id' => 3, 'text' => 'string 3'],
                    ['id' => 4, 'text' => 'string 4'],
                    ['id' => 5, 'text' => 'string 5 active'],
                ];
                $result = Helper::merge($array1, $array2, ['id' => 'id']);
                $this->assertEquals($expected, $result);
            });
    }

    public function testRename()
    {
        $this->specify("renames or deletes columns", function() {
                $array = [
                    ['id1' => 1, 'id2' => 1, 'text' => 'string 1'],
                    ['id1' => 2, 'id2' => 2, 'text' => 'string 2'],
                    ['id1' => 3, 'text' => 'string 3'],
                    ['id1' => 4, 'text' => 'string 4'],
                    ['id2' => 5, 'text' => 'string 5']
                ];
                $expected = [
                    ['id' => 1, 'text' => 'string 1'],
                    ['id' => 2, 'text' => 'string 2'],
                    ['id' => 3, 'text' => 'string 3'],
                    ['id' => 4, 'text' => 'string 4'],
                    ['text' => 'string 5']
                ];
                $result = Helper::rename($array, ['id1' => 'id', 'id2' => false]);
                $this->assertEquals($expected, $result);
            });
    }
}
