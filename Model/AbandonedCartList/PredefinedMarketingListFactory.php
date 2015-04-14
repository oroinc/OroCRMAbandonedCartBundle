<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ObjectManager;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;
use OroCRM\Bundle\MarketingListBundle\Model\MarketingListSourceInterface;

class PredefinedMarketingListFactory
{
    const ENTITY_CART_FULL_NAME = 'OroCRM\Bundle\MagentoBundle\Entity\Cart';

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var MarketingListSourceInterface
     */
    private $source;

    /**
     * @param ObjectManager $objectManager
     * @param MarketingListSourceInterface $source
     */
    public function __construct(ObjectManager $objectManager, MarketingListSourceInterface $source)
    {
        $this->objectManager = $objectManager;
        $this->source = $source;
    }

    /**
     * @return MarketingList
     */
    public function create()
    {
        $marketingList = new MarketingList();
        $marketingList->setEntity(self::ENTITY_CART_FULL_NAME);

        $type = $this->objectManager
            ->find(
                'OroCRMMarketingListBundle:MarketingListType',
                MarketingListType::TYPE_DYNAMIC
            );

        $marketingList->setType($type);
        $marketingList->setSource($this->source->getCode());

        return $marketingList;
    }
}
