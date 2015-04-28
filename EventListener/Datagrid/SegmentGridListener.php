<?php

namespace OroCRM\Bundle\AbandonedCartBundle\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Event\PreBuild;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use OroCRM\Bundle\MarketingListBundle\Model\MarketingListHelper;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;

class SegmentGridListener
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
     * @param PreBuild $event
     */
    public function onPreBuild(PreBuild $event)
    {
        $config     = $event->getConfig();
        $parameters = $event->getParameters();
        $gridName   = $config->getName();

        if (!$parameters->get('grid-mixin', false)) {
            return;
        }

        $marketingListId = $this->marketingListHelper->getMarketingListIdByGridName($gridName);
        if (!$marketingListId) {
            return;
        }

        $marketingList = $this->marketingListHelper->getMarketingList($marketingListId);

        if (!$marketingList) {
            return;
        }

        $abandonedCartCampaign = $this->abandonedCartCampaignProvider->getAbandonedCartCampaign($marketingList);
        if (!$abandonedCartCampaign) {
            return;
        }

        $this->unsetColumn($config, 'contactedTimes');
        $this->unsetColumn($config, 'lastContactedAt');
    }

    /**
     * @param DatagridConfiguration $config
     * @param string $column
     */
    protected function unsetColumn(DatagridConfiguration $config, $column)
    {
        $config->offsetUnsetByPath(sprintf('[columns][%s]', $column));
        $config->offsetUnsetByPath(sprintf('[sorters][columns][%s]', $column));
        $config->offsetUnsetByPath(sprintf('[filters][columns][%s]', $column));
    }
}
