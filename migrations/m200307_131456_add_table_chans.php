<?php

use yii\db\Migration;

/**
 * Class m200307_131456_add_table_chans
 */
class m200307_131456_add_table_chans extends Migration
{
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		if (is_null($table = $this->db->getTableSchema('chans'))) {
			$this->createTable('chans', [
				'[[id]]'			=> $this->primaryKey(),
				'[[uuid]]'			=> $this->string(16)->append(' COLLATE utf8_unicode_ci'),
				'[[call_id]]'		=> $this->integer(),
				'[[name]]'			=> $this->string(64)->append(' COLLATE utf8_unicode_ci'),
				'[[state]]'			=> $this->string(64)->append(' COLLATE utf8_unicode_ci'),
				'[[src]]'			=> $this->string(64)->append(' COLLATE utf8_unicode_ci'),
				'[[dst]]'			=> $this->string(64)->append(' COLLATE utf8_unicode_ci'),
				'[[wasRinging]]'	=> $this->boolean(),
				'[[reversed]]'		=> $this->boolean(),
				'[[deleted]]'		=> $this->boolean(),
				'[[vars]]'			=> $this->string(255)->append(' COLLATE utf8_unicode_ci'),
				'[[created_at]]'	=> $this->timestamp(),
				'[[updated_at]]'	=> $this->timestamp()
			], 'DEFAULT CHARSET=utf8');

			$this->createIndex('{{%idx-chans_uuid}}',				'{{%chans}}', '[[uuid]]');
			$this->createIndex('{{%idx-chans_name}}',				'{{%chans}}', '[[name]]');
			$this->createIndex('{{%idx-chans_deleted}}',			'{{%chans}}', '[[deleted]]');
			//$this->createIndex('{{%idx-chan_events_name}}',			'{{%chan_events}}', '[[channel]]');
			//$this->createIndex('{{%idx-chan_events_channel_id}}',		'{{%chan_events}}', '[[channel_id]]');
			$this->createIndex('{{%idx-chans_created_at}}',		'{{%chans}}', '[[created_at]]');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		if (!is_null($table=$this->db->getTableSchema('{{%chans}}'))) $this->dropTable('{{%chans}}');
	}

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200307_131456_add_table_chans cannot be reverted.\n";

        return false;
    }
    */
}
