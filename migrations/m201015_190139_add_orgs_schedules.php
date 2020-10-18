<?php

use yii\db\Migration;

/**
 * Class m201015_190139_add_orgs_schedules
 */
class m201015_190139_add_orgs_schedules extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('orgs','base_schedule_id',$this->integer()->defaultValue(null));
        $this->addColumn('orgs','private_schedule_id',$this->integer()->defaultValue(null));
        $this->createIndex('{{%idx-orgs_base_schedule_id}}', '{{%orgs}}', '[[base_schedule_id]]');
        $this->createIndex('{{%idx-orgs_private_schedule_id}}', '{{%orgs}}', '[[private_schedule_id]]');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('orgs','base_schedule_id');
        $this->dropColumn('orgs','private_schedule_id');
        $this->dropIndex('{{%idx-orgs_base_schedule_id}}', '{{%orgs}}', '[[base_schedule_id]]');
        $this->dropIndex('{{%idx-orgs_private_schedule_id}}', '{{%orgs}}', '[[private_schedule_id]]');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201015_190139_add_orgs_schedules cannot be reverted.\n";

        return false;
    }
    */
}
