<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar;

use OroCRM\Bundle\MailChimpBundle\Model\ExtendedMergeVar\ProviderInterface;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;

class CartItemsMergeVarProvider implements ProviderInterface
{
    const CART_ITEMS_LIMIT = 3;

    const NAME_PREFIX = 'item';

    const CART_ITEM_NAME = '%s_%d';
    const CART_ITEM_LABEL = 'Cart Item (%d)';

    /**
     * @var AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @param AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider
     */
    public function __construct(AbandonedCartCampaignProviderInterface $abandonedCartCampaignProvider)
    {
        $this->abandonedCartCampaignProvider = $abandonedCartCampaignProvider;
    }

    /**
     * @param MarketingList $marketingList
     * @return array
     */
    public function provideExtendedMergeVars(MarketingList $marketingList)
    {
        $entity = $this->abandonedCartCampaignProvider
            ->getAbandonedCartCampaign($marketingList);

        if (is_null($entity)) {
            return [];
        }

        $mergeVars = [];
        for ($i = 1; $i <= self::CART_ITEMS_LIMIT; $i++) {
            $mergeVars[] = [
                'name' => sprintf(self::CART_ITEM_NAME, self::NAME_PREFIX, $i),
                'label' => sprintf(self::CART_ITEM_LABEL, $i)
            ];
        }

        return $mergeVars;
    }
}
