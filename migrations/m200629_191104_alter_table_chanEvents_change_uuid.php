<?php

use yii\db\Migration;

/**
 * Class m200629_191104_alter_table_chanEvents_change_uuid
 */
class m200629_191104_alter_table_chanEvents_change_uuid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->alterColumn('chanEvents','uuid',$this->string(32)->defaultValue(null)	);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->alterColumn('chanEvents','uuid',$this->string(16)->defaultValue(null)	);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200629_191104_alter_table_chanEvents_change_uuid cannot be reverted.\n";

        return false;
    }
    */
}
