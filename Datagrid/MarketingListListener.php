<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Datagrid;

use Doctrine\ORM\Query\Expr\Join;

use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
use Oro\Bundle\DataGridBundle\Datasource\Orm\OrmDatasource;
use Oro\Bundle\DataGridBundle\Event\BuildAfter;

class MarketingListListener
{
    const ABANDONEDCART_CAMPAIGN_ALIAS = 'acc';

    /**
     * @var string
     */
    protected $gridName;

    /**
     * @var string
     */
    protected $abandonedCartCampaignClass;

    /**
     * @param $gridName
     * @param $abandonedCartCampaignClass
     */
    public function __construct($gridName, $abandonedCartCampaignClass)
    {
        if (!is_string($gridName) || empty($gridName)) {
            throw new \InvalidArgumentException('The grid name should be provided.');
        }
        if (!is_string($abandonedCartCampaignClass) || empty($abandonedCartCampaignClass)) {
            throw new \InvalidArgumentException('AbandonedCartCampaign class name should be provided.');
        }
        $this->gridName = $gridName;
        $this->abandonedCartCampaignClass = $abandonedCartCampaignClass;
    }

    /**
     * Exclude Abandoned Cart Campaigns marketing list
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
                ->leftJoin(
                    $this->abandonedCartCampaignClass,
                    self::ABANDONEDCART_CAMPAIGN_ALIAS,
                    Join::WITH,
                    sprintf('%s.marketingList = %s.id', self::ABANDONEDCART_CAMPAIGN_ALIAS, $rootAlias)
                )
                ->andWhere(
                    $qb->expr()->andX(
                        $qb->expr()->isNull('acc.marketingList')
                    )
                );
        }
    }

    /**
     * @param DatagridInterface $grid
     * @return bool
     */
    protected function isApplicable(DatagridInterface $grid)
    {
        return $grid->getName() === $this->gridName;
    }
}
