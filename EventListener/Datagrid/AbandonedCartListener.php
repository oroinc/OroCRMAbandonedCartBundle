<?php

namespace OroCRM\Bundle\AbandonedCartBundle\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use OroCRM\Bundle\MarketingListBundle\Model\MarketingListHelper;
use OroCRM\Bundle\AbandonedCartBundle\Model\MarketingList\AbandonedCartSource;

class AbandonedCartListener
{
    /**
     * @var MarketingListHelper
     */
    protected $marketingListHelper;

    /**
     * @param MarketingListHelper $marketingListHelper
     */
    public function __construct(MarketingListHelper $marketingListHelper)
    {
        $this->marketingListHelper = $marketingListHelper;
    }

    /**
     * Remove useless fields in case of filtering
     *
     * @param BuildBefore $event
     */
    public function onBuildBefore(BuildBefore $event)
    {
        $config = $event->getConfig();
        $actions = isset($config['actions']) ? $config['actions'] : array();

        if (empty($actions['subscribe'])) {
            return;
        }

        $dataGrid     = $event->getDatagrid();
        $dataGridName = $dataGrid->getName();

        $marketingListId = $this->marketingListHelper->getMarketingListIdByGridName($dataGridName);
        $marketingList = $this->marketingListHelper->getMarketingList($marketingListId);

        if ($marketingList->getSource() == AbandonedCartSource::SOURCE_CODE) {
            $config->offsetUnsetByPath('[actions][subscribe]');
        }
    }
}
