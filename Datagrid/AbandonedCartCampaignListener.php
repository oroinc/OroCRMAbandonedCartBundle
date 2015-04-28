<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Datagrid;

use Doctrine\ORM\Query\Expr\Join;

use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;

class AbandonedCartCampaignListener extends MarketingListListener
{
    /**
     * Filter only Abandoned Cart Campaigns
     *
     * @param BuildAfter $event
     */
    public function onBuildAfter(BuildAfter $event)
    {
        $dataGrid = $event->getDatagrid();
        if (!$this->isApplicable($dataGrid)) {
            return;
        }
        $dataSource = $dataGrid->getDatasource();
        if ($dataSource instanceof OrmDatasource) {
            $qb = $dataSource->getQueryBuilder();

            $rootAliases = $qb->getRootAliases();
            $rootAlias = reset($rootAliases);

            $qb
                ->join(
                    $this->abandonedCartCampaignClass,
                    self::ABANDONEDCART_CAMPAIGN_ALIAS,
                    Join::WITH,
                    sprintf('%s.marketingList = %s.id', self::ABANDONEDCART_CAMPAIGN_ALIAS, $rootAlias)
                );
        }
    }
}
