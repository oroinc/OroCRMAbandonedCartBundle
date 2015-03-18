<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Migrations\Schema\v1_0;

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
        $table = $schema->createTable('orocrm_abandonedcart_campaign');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('marketing_list_id', 'integer', []);
        $table->addColumn('campaign_id', 'integer', []);
        $table->setPrimaryKey(['id']);
        $table->addUniqueIndex(['marketing_list_id'], 'UNIQ_3BDE1B0796434D04');
        $table->addUniqueIndex(['campaign_id'], 'UNIQ_3BDE1B07F639F774');
    }
}
