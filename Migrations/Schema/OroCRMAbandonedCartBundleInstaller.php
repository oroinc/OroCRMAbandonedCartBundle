<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

class OroCRMAbandonedCartBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_2';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        /** Tables generation **/
        $this->createOrocrmAbandonedCartCampaignTable($schema);
        $this->createOrocrmAbandcartConvTable($schema);
        $this->createOrocrmAbandcartConvCampsTable($schema);

        /** Foreign keys generation **/
        $this->addOrocrmAbandonedCartCampaignForeignKeys($schema);
        $this->addOrocrmAbandcartConvForeignKeys($schema);
        $this->addOrocrmAbandcartConvCampsForeignKeys($schema);
    }

    /**
     * Create orocrm_abandonedcart_campaign table
     *
     * @param Schema $schema
     */
    protected function createOrocrmAbandonedCartCampaignTable(Schema $schema)
    {
        $table = $schema->createTable('orocrm_abandonedcart_campaign');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('campaign_id', 'integer', []);
        $table->addColumn('marketing_list_id', 'integer', []);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['marketing_list_id'], 'UNIQ_3BDE1B0796434D04');
        $table->addUniqueIndex(['campaign_id'], 'UNIQ_3BDE1B07F639F774');
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
     * Add orocrm_abandonedcart_campaign foreign keys.
     *
     * @param Schema $schema
     */
    protected function addOrocrmAbandonedCartCampaignForeignKeys(Schema $schema)
    {
        $table = $schema->getTable('orocrm_abandonedcart_campaign');
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_campaign'),
            ['campaign_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
        $table->addForeignKeyConstraint(
            $schema->getTable('orocrm_marketing_list'),
            ['marketing_list_id'],
            ['id'],
            ['onDelete' => 'CASCADE', 'onUpdate' => null]
        );
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
