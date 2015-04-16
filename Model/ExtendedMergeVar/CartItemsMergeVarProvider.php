<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar;

use OroCRM\Bundle\AbandonedCartBundle\Model\MarketingList\AbandonedCartSource;
use OroCRM\Bundle\MailChimpBundle\Model\ExtendedMergeVar\ProviderInterface;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CartItemsMergeVarProvider implements ProviderInterface
{
    const CART_ITEM_1_NAME = 'item_1';
    const CART_ITEM_1_LABEL = 'First Cart Item';

    const CART_ITEM_2_NAME = 'item_2';
    const CART_ITEM_2_LABEL = 'Second Cart Item';

    const CART_ITEM_3_NAME = 'item_3';
    const CART_ITEM_3_LABEL = 'Third Cart Item';

    public function provideExtendedMergeVars(MarketingList $marketingList)
    {
        if ($marketingList->getSource() !== AbandonedCartSource::SOURCE_CODE) {
            return [];
        }

        return [
            [
                'name' => self::CART_ITEM_1_NAME,
                'label' => self::CART_ITEM_1_LABEL
            ],
            [
                'name' => self::CART_ITEM_2_NAME,
                'label' => self::CART_ITEM_2_LABEL
            ],
            [
                'name' => self::CART_ITEM_3_NAME,
                'label' => self::CART_ITEM_3_LABEL
            ]
        ];
    }
}
