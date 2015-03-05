<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;

class PredefinedMarketingListFactory
{
    const ENTITY_CART_FULL_NAME = 'OroCRM\Bundle\MagentoBundle\Entity\Cart';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @return MarketingList
     */
    public function create()
    {
        $marketingList = new MarketingList();
        $marketingList->setEntity(self::ENTITY_CART_FULL_NAME);

        $type = $this->objectManager->find('OroCRMMarketingListBundle:MarketingListType',
            MarketingListType::TYPE_DYNAMIC);

        $marketingList->setType($type);

        return $marketingList;
    }
}