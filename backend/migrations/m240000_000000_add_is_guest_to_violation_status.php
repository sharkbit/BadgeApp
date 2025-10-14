<?php

use yii\db\Migration;

/**
 * Class m240000_000000_add_is_guest_to_violation_status
 */
class m240000_000000_add_is_guest_to_violation_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('violation_status', 'is_guest', $this->boolean()->defaultValue(false)->after('admin_contact_required'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('violation_status', 'is_guest');
    }
} 