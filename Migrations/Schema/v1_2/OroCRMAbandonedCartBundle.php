<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Migrations\Schema\v1_2;

use Doctrine\DBAL\Schema\Schema;

use Oro\Bundle\MigrationBundle\Migration\Migration;
use Oro\Bundle\MigrationBundle\Migration\ParametrizedSqlMigrationQuery;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\MagentoBundle\Provider\ChannelType;

class OroCRMAbandonedCartBundle implements Migration
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
