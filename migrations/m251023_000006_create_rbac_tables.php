<?php

use yii\db\Migration;


class m251023_000006_create_rbac_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $authManager = Yii::$app->authManager;
        $this->db = $authManager->db;

        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        // Create rule table
        $this->createTable($authManager->ruleTable, [
            'name' => $this->string(64)->notNull(),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        // Create item table
        $this->createTable($authManager->itemTable, [
            'name' => $this->string(64)->notNull(),
            'type' => $this->smallInteger()->notNull(),
            'description' => $this->text(),
            'rule_name' => $this->string(64),
            'data' => $this->binary(),
            'created_at' => $this->integer(),
            'updated_at' => $this->integer(),
            'PRIMARY KEY ([[name]])',
        ], $tableOptions);

        $this->createIndex('idx-auth_item-type', $authManager->itemTable, 'type');

        $this->addForeignKey(
            'fk-auth_item-rule_name',
            $authManager->itemTable,
            'rule_name',
            $authManager->ruleTable,
            'name',
            'SET NULL',
            'CASCADE'
        );

        // Create item child table
        $this->createTable($authManager->itemChildTable, [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
            'PRIMARY KEY ([[parent]], [[child]])',
        ], $tableOptions);

        $this->addForeignKey(
            'fk-auth_item_child-parent',
            $authManager->itemChildTable,
            'parent',
            $authManager->itemTable,
            'name',
            'CASCADE',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-auth_item_child-child',
            $authManager->itemChildTable,
            'child',
            $authManager->itemTable,
            'name',
            'CASCADE',
            'CASCADE'
        );

        // Create assignment table
        $this->createTable($authManager->assignmentTable, [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->string(64)->notNull(),
            'created_at' => $this->integer(),
            'PRIMARY KEY ([[item_name]], [[user_id]])',
        ], $tableOptions);

        $this->addForeignKey(
            'fk-auth_assignment-item_name',
            $authManager->assignmentTable,
            'item_name',
            $authManager->itemTable,
            'name',
            'CASCADE',
            'CASCADE'
        );

        $this->createIndex('idx-auth_assignment-user_id', $authManager->assignmentTable, 'user_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $authManager = Yii::$app->authManager;

        $this->dropTable($authManager->assignmentTable);
        $this->dropTable($authManager->itemChildTable);
        $this->dropTable($authManager->itemTable);
        $this->dropTable($authManager->ruleTable);
    }
}
