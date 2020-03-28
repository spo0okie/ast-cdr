<?php

use yii\db\Migration;

/**
 * Class m200307_104806_add_table_chanEvents
 */
class m200307_104806_add_table_chanEvents extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp()
	{
		if (is_null($table = $this->db->getTableSchema('chan_events'))) {
			$this->createTable('chan_events', [
				'[[id]]'			=> $this->primaryKey(),			//ключ
				'[[uid]]'			=> $this->integer(),			//это поле я так понимаю не сильно уникальное, оно както в рамках вызова чтоли
				'[[uuid]]'			=> $this->string(16)->null()->append(' COLLATE utf8_unicode_ci'),		//это UUID самого вызова
				'[[channel]]'		=> $this->string(128)->null()->append(' COLLATE utf8_unicode_ci'),		//имя канала к которому относится вызов
				'[[channel_id]]'	=> $this->integer(),	//ID канала
				'[[call_state_id]]'	=> $this->integer()->null(),	//ID канала
				'[[data]]'			=> $this->text()->null(),		//сырые данные
				'[[used]]'			=> $this->boolean(),			//данные были использованы для обновления канала
				'[[created_at]]'	=> $this->timestamp()			//дата время события
			], 'DEFAULT CHARSET=utf8');

			$this->createIndex('{{%idx-chan_events_uid}}', 			'{{%chan_events}}', '[[uid]]');
			$this->createIndex('{{%idx-chan_events_channel}}',		'{{%chan_events}}', '[[channel]]');
			$this->createIndex('{{%idx-chan_events_channel_id}}',		'{{%chan_events}}', '[[channel_id]]');
			$this->createIndex('{{%idx-chan_events_call_state_id}}',	'{{%chan_events}}', '[[call_state_id]]');
			//$this->createIndex('{{%idx-chan_events_created_at}}',		'{{%chan_events}}', '[[created_at]]');
		}
	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		if (!is_null($table=$this->db->getTableSchema('{{%chan_events}}'))) $this->dropTable('{{%chan_events}}');
    }

}
