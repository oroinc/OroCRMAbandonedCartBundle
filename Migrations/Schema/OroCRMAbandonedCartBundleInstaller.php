<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Migrations\Schema;

use Doctrine\DBAL\Schema\Schema;
use Oro\Bundle\MigrationBundle\Migration\Installation;
use Oro\Bundle\MigrationBundle\Migration\QueryBag;
use OroCRM\Bundle\AbandonedCartBundle\Migrations\Schema\v1_0\OroCRMAbandonedCartBundle;

class OroCRMAbandonedCartBundleInstaller implements Installation
{
    /**
     * {@inheritdoc}
     */
    public function getMigrationVersion()
    {
        return 'v1_0';
    }

    /**
     * {@inheritdoc}
     */
    public function up(Schema $schema, QueryBag $queries)
    {
        $migration = new OroCRMAbandonedCartBundle();
        $migration->up($schema, $queries);
    }
}
