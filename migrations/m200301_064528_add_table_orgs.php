<?php

use yii\db\Migration;

/**
 * Class m200301_064528_add_table_orgs
 */
class m200301_064528_add_table_orgs extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		if (is_null($table = $this->db->getTableSchema('{{%orgs}}'))) {
			$this->createTable('{{%orgs}}', [
				'[[id]]'		=> $this->primaryKey(),
				'[[code]]'		=> $this->string(16)->append(' COLLATE utf8_unicode_ci'),
				'[[name]]'		=> $this->string(32)->append(' COLLATE utf8_unicode_ci'),
				'[[comment]]'	=> $this->text()->Null()->append(' COLLATE utf8_unicode_ci'),
			], 'DEFAULT CHARSET=utf8');

			$this->createIndex(	'{{%idx-orgs-code}}',	'{{%orgs}}',	'[[code]]');
		}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		if (!is_null($table=$this->db->getTableSchema('{{%orgs}}'))) $this->dropTable('{{%orgs}}');
    }

}
