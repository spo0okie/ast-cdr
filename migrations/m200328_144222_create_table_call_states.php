<?php

use yii\db\Migration;

/**
 * Class m200328_144222_create_table_call_states
 */
class m200328_144222_create_table_call_states extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		if (is_null($table = $this->db->getTableSchema('call_states'))) {
			$this->createTable('call_states', [
				'[[id]]'			=> $this->primaryKey(),
				'[[call_id]]'		=> $this->integer(),
				//'[[src]]'			=> $this->string(64)->append(' COLLATE utf8_unicode_ci'),
				'[[name]]'			=> $this->string(64)->append(' COLLATE utf8_unicode_ci'),
				'[[state]]'			=> $this->string(64)->append(' COLLATE utf8_unicode_ci'),
				'[[created_at]]'	=> $this->timestamp(),
				'[[updated_at]]'	=> $this->timestamp()
			], 'DEFAULT CHARSET=utf8');

			$this->createIndex('{{%idx-call_states_call_id}}',	'{{%call_states}}', '[[call_id]]');
			$this->createIndex('{{%idx-call_states_name}}',		'{{%call_states}}', '[[name]]');
			//$this->createIndex('{{%idx-chans_deleted}}',			'{{%chans}}', '[[deleted]]');
			//$this->createIndex('{{%idx-chan_events_name}}',			'{{%chan_events}}', '[[channel]]');
			//$this->createIndex('{{%idx-chan_events_channel_id}}',		'{{%chan_events}}', '[[channel_id]]');
			//$this->createIndex('{{%idx-chans_created_at}}',		'{{%chans}}', '[[created_at]]');
		}

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		if (!is_null($table=$this->db->getTableSchema('call_states'))) $this->dropTable('{{%call_states}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200328_144222_create_table_call_states cannot be reverted.\n";

        return false;
    }
    */
}
