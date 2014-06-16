<?php

use yii\db\Schema;

class m140612_172300_init extends \yii\db\Migration
{
    public function safeUp()
    {
        $this->createTable(
            'product',
            [
                'id' => Schema::TYPE_PK,
                'name' => Schema::TYPE_STRING,
                'price' => Schema::TYPE_MONEY,
                'weight' => Schema::TYPE_FLOAT,
                'has_discount' => Schema::TYPE_INTEGER,
                'category_id' => Schema::TYPE_INTEGER,
                'balance' => Schema::TYPE_INTEGER,
            ]
        );
    }

    public function safeDown()
    {
        $this->dropTable('product');
    }
}
