<?php
namespace tests\models;

use yii\db\ActiveRecord;

class Product extends ActiveRecord
{
    public function fields()
    {
        ['id','name','weight','price','has_discount'];
    }

    public static function tableName()
    {
        return '{{%product}}';
    }
}