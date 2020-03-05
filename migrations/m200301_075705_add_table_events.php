<?php

use yii\db\Migration;

/**
 * Class m200301_075705_add_table_events
 */
class m200301_075705_add_table_events extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
	{
		if (is_null($table = $this->db->getTableSchema('events'))) {
			$this->createTable('events', [
				'[[id]]'			=> $this->primaryKey(),
				'[[type]]'			=> $this->integer(),
				'[[source]]'		=> $this->string(16)->append(' COLLATE utf8_unicode_ci'),
				'[[destination]]'	=> $this->string(16)->Null()->append(' COLLATE utf8_unicode_ci'),
				'[[trunk]]'			=> $this->string(16)->Null()->append(' COLLATE utf8_unicode_ci'),
				'[[call_id]]'		=> $this->integer(),
				'[[created_at]]'	=> $this->timestamp()
			], 'DEFAULT CHARSET=utf8');

			$this->createIndex('{{%idx-events_type}}', 		'{{%events}}', '[[type]]');
			$this->createIndex('{{%idx-events_call_id}}',		'{{%events}}', '[[call_id]]');
			$this->createIndex('{{%idx-events_created_at}}',	'{{%events}}', '[[created_at]]');
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		if (!is_null($table=$this->db->getTableSchema('{{%events}}'))) $this->dropTable('{{%events}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200301_075705_add_table_events cannot be reverted.\n";

        return false;
    }
    */
}
