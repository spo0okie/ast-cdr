<?php

use yii\db\Migration;

/**
 * Class m201012_153502_create_table_schedules
 */
class m201012_153502_create_table_schedules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (is_null($table = $this->db->getTableSchema('schedules'))) {
            $this->createTable('schedules', [
                '[[id]]' => $this->primaryKey(),
                '[[name]]' => $this->string(64)->append(' COLLATE utf8_unicode_ci'),
                '[[comment]]' => $this->string(255)->append(' COLLATE utf8_unicode_ci'),
                '[[created_at]]' => $this->timestamp(),
                //'[[updated_at]]'	=> $this->timestamp()
            ], 'DEFAULT CHARSET=utf8');
        }

        if (is_null($table = $this->db->getTableSchema('schedules_days'))) {
            $this->createTable('schedules_days', [
                '[[id]]' => $this->primaryKey(),
                '[[schedule_id]]' => $this->integer(),
                '[[date]]' => $this->string(64)->append(' COLLATE utf8_unicode_ci'),
                '[[schedule]]' => $this->string(64)->append(' COLLATE utf8_unicode_ci'),
                '[[comment]]' => $this->string(255)->append(' COLLATE utf8_unicode_ci'),
                '[[created_at]]' => $this->timestamp(),
                //'[[updated_at]]'	=> $this->timestamp()
            ], 'DEFAULT CHARSET=utf8');

            $this->createIndex('{{%idx-schedules_days_org_id}}', '{{%schedules_days}}', '[[schedule_id]]');
            $this->createIndex('{{%idx-schedules_days_date}}', '{{%schedules_days}}', '[[date]]');
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (!is_null($table=$this->db->getTableSchema('schedules_days'))) $this->dropTable('{{%schedules_days}}');
        if (!is_null($table=$this->db->getTableSchema('schedules'))) $this->dropTable('{{%schedules}}');
        $this->dropIndex('{{%idx-schedules_days_org_id}}', '{{%schedules_days}}');
        $this->dropIndex('{{%idx-schedules_days_date}}', '{{%schedules_days}}');

    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201012_153502_create_table_schedules cannot be reverted.\n";

        return false;
    }
    */
}
