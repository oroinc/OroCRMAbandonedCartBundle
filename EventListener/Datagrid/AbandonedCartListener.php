<?php

namespace Oro\Bundle\AbandonedCartBundle\EventListener\Datagrid;

use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;
use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use OroCRM\Bundle\MarketingListBundle\Model\MarketingListHelper;

class AbandonedCartListener
{
    /**
     * @var MarketingListHelper
     */
    protected $marketingListHelper;

    /**
     * @var AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @param MarketingListHelper $marketingListHelper
     * @param AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
     */
    public function __construct(
        MarketingListHelper $marketingListHelper,
        AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
    ) {
        $this->marketingListHelper = $marketingListHelper;
        $this->abandonedCartCampaignProvider = $abandonedCartCampaignProvider;
    }

    /**
     * Remove useless fields in case of filtering
     *
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();
        if (empty($config['actions']['subscribe'])) {
            return;
        }

        $dataGrid     = $event->getDatagrid();
        $dataGridName = $dataGrid->getName();

        $marketingListId = $this->marketingListHelper->getMarketingListIdByGridName($dataGridName);
        $marketingList = $this->marketingListHelper->getMarketingList($marketingListId);

        if ($this->abandonedCartCampaignProvider->getAbandonedCartCampaign($marketingList)) {
            $config->offsetUnsetByPath('[actions][subscribe]');
        }
    }
}
