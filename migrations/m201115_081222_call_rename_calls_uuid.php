<?php

use yii\db\Migration;

/**
 * Class m201115_081222_call_rename_calls_uuid
 */
class m201115_081222_call_rename_calls_uuid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('chans','uuid',$this->string(32)->defaultValue(null)	);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('chans','uuid',$this->string(16)->defaultValue(null)	);
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201115_081222_call_rename_calls_uuid cannot be reverted.\n";

        return false;
    }
    */
}
