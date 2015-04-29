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
        $this->createOrocrmAbandcartConvTable($schema);
        $this->addOrocrmAbandcartConvForeignKeys($schema);
        $this->createOrocrmAbandcartConvCampsTable($schema);
        $this->addOrocrmAbandcartConvCampsForeignKeys($schema);

    }

    /**
     * Create orocrm_abandcart_conv table
     *
     * @param Schema $schema
     */
    protected function createOrocrmAbandcartConvTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_abandcart_conv');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('marketing_list_id', 'integer', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['marketing_list_id'], 'UNIQ_A4BAA17896434D04');
    }

    /**
     * Add orocrm_abandcart_conv foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrocrmAbandcartConvForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orocrm_abandcart_conv');
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_marketing_list'),
            ['marketing_list_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }

    /**
     * Create orocrm_abandcart_conv_camps table
     *
     * @param Schema $schema
     */
    protected function createOrocrmAbandcartConvCampsTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_abandcart_conv_camps');
        $table->addColumn('conversion_id', 'integer', []);
        $table->addColumn('mailchimp_campaign_id', 'integer', []);
        $table->setPrimaryKey(['conversion_id', 'mailchimp_campaign_id']);
    }

    /**
     * Add orocrm_abandcart_conv_camps foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrocrmAbandcartConvCampsForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orocrm_abandcart_conv_camps');
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_abandcart_conv'),
            ['conversion_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_mailchimp_campaign'),
            ['mailchimp_campaign_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
    }
}
