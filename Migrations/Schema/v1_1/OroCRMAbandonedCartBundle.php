<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Migrations\Schema\v1_1;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

/**
 * @SuppressWarnings(PHPMD.TooManyMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassLength)
 */
class OroCRMAbandonedCartBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createOrocrmAbandonedcartWorkflowTable($schema);
        $this->createOrocrmAbandonedcartConversionTable($schema);
        $this->createOrocrmAbandonedcartStatisticsTable($schema);
        $this->createOrocrmAbandonedcartConversionWorkflowTable($schema);
        $this->addOrocrmAbandonedcartConversionWorkflowForeignKeys($schema);
    }

    /**
     * Create orocrm_abandonedcart_workflow table
     *
     * @param Schema $schema
     */
    protected function createOrocrmAbandonedcartWorkflowTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_abandcart_workflow');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('name', 'text', []);
        $table->setPrimaryKey(['id']);
    }

    /**
     * Create orocrm_abandonedcart_conversion table
     *
     * @param Schema $schema
     */
    protected function createOrocrmAbandonedcartConversionTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_abandcart_conv');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('marketing_list_id', 'integer', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['marketing_list_id'], 'UNIQ_A4BAA17896434D04');
    }

    /**
     * Create orocrm_abandcart_stats table
     *
     * @param Schema $schema
     */
    protected function createOrocrmAbandonedcartStatisticsTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_abandcart_stats');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('workflow_id', 'integer', ['notnull' => false]);
        $table->addColumn('emails_sent', 'integer', ['notnull' => false]);
        $table->addColumn('opens', 'integer', ['notnull' => false]);
        $table->addColumn('unique_opens', 'integer', ['notnull' => false]);
        $table->addColumn('clicks', 'integer', ['notnull' => false]);
        $table->addColumn('unique_clicks', 'integer', ['notnull' => false]);
        $table->addColumn('converted_to_orders', 'integer', ['notnull' => false]);
        $table->addColumn('total_sum', 'text', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['workflow_id'], 'UNIQ_524B5102C7C2CBA');
    }

    /**
     * Create orocrm_abandcart_conversion_workflow table
     *
     * @param Schema $schema
     */
    protected function createOrocrmAbandonedcartConversionWorkflowTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_abandcart_conv_workflow');
        $table->addColumn('workflow_id', 'integer', []);
        $table->addColumn('conversion_id', 'integer', []);
        $table->setPrimaryKey(['workflow_id', 'conversion_id']);
        $table->addIndex(['workflow_id'], 'IDX_601118BF2C7C2CBA', []);
        $table->addIndex(['conversion_id'], 'IDX_601118BF4C1FF126', []);
    }

    /**
     * Add orocrm_abandcart_conversion_workflow foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrocrmAbandonedcartConversionWorkflowForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orocrm_abandcart_conv_workflow');
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_abandcart_workflow'),
            ['workflow_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_abandcart_conv'),
            ['conversion_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }


}
