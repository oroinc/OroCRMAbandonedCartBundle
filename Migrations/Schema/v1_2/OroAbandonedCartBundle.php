<?php

namespace Oro\Bundle\AbandonedCartBundle\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedSqlMigrationQuery;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use OroCRM\Bundle\MagentoBundle\Provider\ChannelType;

class OroAbandonedCartBundle implements Migration
{
    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $queries->addPreQuery(
            new ParametrizedSqlMigrationQuery(
                'INSERT INTO orocrm_channel_entity_name (channel_id, name) '
                . 'SELECT id, :entity FROM orocrm_channel '
                . 'WHERE channel_type = :type',
                ['entity' => AbandonedCartCampaign::CLASS_NAME, 'type' => ChannelType::TYPE]
            )
        );
    }
}
