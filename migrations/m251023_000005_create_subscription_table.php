<?php

use yii\db\Migration;

class m251023_000005_create_subscription_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%subscription}}', [
            'id' => $this->primaryKey(),
            'author_id' => $this->integer()->notNull(),
            'phone' => $this->string(20)->notNull(),
            'status' => $this->tinyInteger()->defaultValue(1)->comment('1=active, 0=inactive'),
            'created_at' => $this->integer()->notNull(),
        ]);

        $this->addForeignKey(
            'fk-subscription-author_id',
            '{{%subscription}}',
            'author_id',
            '{{%author}}',
            'id',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx-subscription-author_id', '{{%subscription}}', 'author_id');
        $this->createIndex('idx-subscription-phone', '{{%subscription}}', 'phone');
        $this->createIndex('idx-subscription-status', '{{%subscription}}', 'status');

        // Unique constraint to prevent duplicate subscriptions
        $this->createIndex(
            'idx-subscription-unique',
            '{{%subscription}}',
            ['author_id', 'phone'],
            true
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-subscription-author_id', '{{%subscription}}');
        $this->dropTable('{{%subscription}}');
    }
}
