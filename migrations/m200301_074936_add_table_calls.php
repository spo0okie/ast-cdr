<?php

use yii\db\Migration;

/**
 * Class m200301_074936_add_table_calls
 */
class m200301_074936_add_table_calls extends Migration
{
    /**
     * {@inheritdoc}
     */
	public function safeUp()
	{
		if (is_null($table = $this->db->getTableSchema('{{%calls}}'))) {
			$this->createTable('{{%calls}}', [
				'[[id]]'		=> $this->primaryKey(),
				'[[key]]'		=> $this->string(48)->append(' COLLATE utf8_unicode_ci'),
				'[[org_id]]'	=> $this->integer(),
				'[[comment]]'	=> $this->text()->Null()->append(' COLLATE utf8_unicode_ci'),
			], 'DEFAULT CHARSET=utf8');

			$this->createIndex('{{%idx-calls_key}}',		'{{%calls}}',	'[[key]]');
			$this->createIndex('{{%idx-calls_org_id}}',	'{{%calls}}',	'[[org_id]]');
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		if (!is_null($table=$this->db->getTableSchema('{{%orgs}}'))) $this->dropTable('{{%orgs}}');
	}

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200301_074936_add_table_calls cannot be reverted.\n";

        return false;
    }
    */
}
