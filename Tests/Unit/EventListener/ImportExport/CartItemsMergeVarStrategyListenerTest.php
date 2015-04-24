<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\EventListener\ImportExport;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;
use Oro\Bundle\ImportExportBundle\Event\StrategyEvent;
use Oro\Bundle\LocaleBundle\Formatter\NumberFormatter;
use Oro\Bundle\SegmentBundle\Entity\Segment;
use OroCRM\Bundle\MagentoBundle\Entity\Cart;
use OroCRM\Bundle\MagentoBundle\Entity\CartItem;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\MailChimpBundle\Entity\ExtendedMergeVar;
use OroCRM\Bundle\MailChimpBundle\Entity\MemberExtendedMergeVar;
use OroCRM\Bundle\MailChimpBundle\Entity\StaticSegment;
use OroCRM\Bundle\AbandonedCartBundle\EventListener\ImportExport\CartItemsMergeVarStrategyListener;

class CartItemsMergeVarStrategyListenerTest extends \PHPUnit_Framework_TestCase
{
    const SEGMENT_ENTITY_CLASS = 'EntityClass';

    /**
     * @var CartItemsMergeVarStrategyListener
     */
    protected $listener;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DoctrineHelper
     */
    protected $doctrineHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|NumberFormatter
     */
    protected $numberFormatter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityRepository
     */
    protected $entityRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StrategyEvent
     */
    protected $strategyEvent;

    /**
     * @var MemberExtendedMergeVar
     */
    protected $entity;

    /**
     * @var StaticSegment
     */
    protected $staticSegment;

    /**
     * @var MarketingList
     */
    protected $marketingList;

    /**
     * @var Segment
     */
    protected $segment;

    protected function setUp()
    {
        $this->doctrineHelper = $this
            ->getMockBuilder('Oro\Bundle\EntityBundle\ORM\DoctrineHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->numberFormatter = $this->getMockBuilder('Oro\Bundle\LocaleBundle\Formatter\NumberFormatter')
            ->disableOriginalConstructor()
            ->getMock();
        $this->entityRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->strategyEvent = $this
            ->getMockBuilder('Oro\Bundle\ImportExportBundle\Event\StrategyEvent')
            ->disableOriginalConstructor()
            ->getMock();

        $this->doctrineHelper->expects($this->any())->method('getEntityRepository')
            ->with(self::SEGMENT_ENTITY_CLASS)->will($this->returnValue($this->entityRepository));

        $this->entity = new MemberExtendedMergeVar();
        $this->staticSegment = new StaticSegment();
        $this->marketingList = new MarketingList();
        $this->segment = new Segment();

        $this->strategyEvent->expects($this->any())
            ->method('getEntity')->will($this->returnValue($this->entity));

        $this->listener = new CartItemsMergeVarStrategyListener(
            $this->doctrineHelper,
            $this->numberFormatter
        );
    }

    protected function tearDown()
    {
        unset($this->listener);
    }

    /**
     * @return void
     */
    protected function prepareMemberExtendedMergeVar()
    {
        $this->segment->setEntity(self::SEGMENT_ENTITY_CLASS);
        $this->marketingList->setSegment($this->segment);
        $this->staticSegment->setMarketingList($this->marketingList);
        $this->entity->setStaticSegment($this->staticSegment);
    }

    public function testOnProcessAfterWhenEntityIsNotMemberExtendedMergeVar()
    {
        /** @var \PHPUnit_Framework_MockObject_MockObject|StrategyEvent $strategyEvent */
        $strategyEvent = $this
            ->getMockBuilder('Oro\Bundle\ImportExportBundle\Event\StrategyEvent')
            ->disableOriginalConstructor()
            ->getMock();

        /**
         * @var \PHPUnit_Framework_MockObject_MockObject|StrategyEvent $strategyEvent
         */
        $strategyEvent->expects($this->once())
            ->method('getEntity')->will($this->returnValue(new ExtendedMergeVar()));

        $this->listener->onProcessAfter($strategyEvent);

        $this->entityRepository->expects($this->never())->method('find');
    }

    public function testOnProcessAfterWhenEntityNotHasStaticSegment()
    {
        $this->assertEmpty($this->entity->getMergeVarValues());

        $this->listener->onProcessAfter($this->strategyEvent);

        $this->assertEmpty($this->entity->getMergeVarValues());
    }

    public function testOnProcessAfterWhenExtendedMergeVarsIsEmpty()
    {
        $this->assertEmpty($this->entity->getMergeVarValues());

        $this->entity->setStaticSegment($this->staticSegment);

        $this->listener->onProcessAfter($this->strategyEvent);

        $this->assertEmpty($this->entity->getMergeVarValues());

        $emailMergeVar = new ExtendedMergeVar();
        $itemCountMergeVar = new ExtendedMergeVar();
        $emailMergeVar->setName('email');
        $itemCountMergeVar->setName('item_count');

        $this->staticSegment->addExtendedMergeVar($emailMergeVar);
        $this->staticSegment->addExtendedMergeVar($itemCountMergeVar);

        $this->listener->onProcessAfter($this->strategyEvent);

        $this->assertEmpty($this->entity->getMergeVarValues());
    }

    public function testOnProcessAfterWhenContextNotHasEntityId()
    {
        $this->assertEmpty($this->entity->getMergeVarValues());

        $this->entity->setStaticSegment($this->staticSegment);

        $firstCartItemMergeVar = new ExtendedMergeVar();
        $firstCartItemMergeVar->setName('item_1');

        $this->staticSegment->addExtendedMergeVar($firstCartItemMergeVar);

        $this->listener->onProcessAfter($this->strategyEvent);

        $this->assertEmpty($this->entity->getMergeVarValues());
    }

    public function testOnProcessAfterWhenCartDoesNotExist()
    {
        $this->prepareMemberExtendedMergeVar();

        $this->assertEmpty($this->entity->getMergeVarValues());

        $cartEntityId = 1;
        $this->entity->setMergeVarValuesContext(['entity_id' => $cartEntityId]);

        $firstCartItemMergeVar = new ExtendedMergeVar();
        $firstCartItemMergeVar->setName('item_1_name')->markSynced();

        $this->staticSegment->addExtendedMergeVar($firstCartItemMergeVar);

        $this->entityRepository->expects($this->once())->method('find')
            ->with($cartEntityId)->will($this->returnValue(null));

        $this->listener->onProcessAfter($this->strategyEvent);

        $this->assertEmpty($this->entity->getMergeVarValues());
    }

    public function testOnProcessAfterWhenCartItemsIsEmpty()
    {
        $this->prepareMemberExtendedMergeVar();

        $this->assertEmpty($this->entity->getMergeVarValues());

        $cartEntityId = 1;
        $cart = new Cart();
        $cart->setId($cartEntityId);

        $this->entity->setMergeVarValuesContext(['entity_id' => $cartEntityId]);

        $firstCartItemMergeVar = new ExtendedMergeVar();
        $firstCartItemMergeVar->setName('item_1_name')->markSynced();

        $this->staticSegment->addExtendedMergeVar($firstCartItemMergeVar);

        $this->entityRepository->expects($this->once())->method('find')
            ->with($cartEntityId)->will($this->returnValue($cart));

        $this->listener->onProcessAfter($this->strategyEvent);

        $this->assertEmpty($this->entity->getMergeVarValues());
    }

    public function testOnProcessAfter()
    {
        $this->prepareMemberExtendedMergeVar();

        $this->assertEmpty($this->entity->getMergeVarValues());

        $cartEntityId = 1;
        $cart = new Cart();
        $cart->setId($cartEntityId);

        $cartItem1 = new CartItem();
        $cartItem2 = new CartItem();
        $cartItem1->setName('CartItem Name 1');
        $cartItem2->setName('CartItem Name 2');

        $cart->setCartItems(new ArrayCollection([$cartItem1, $cartItem2]));

        $this->entity->setMergeVarValuesContext(['entity_id' => 1]);

        $firstCartItemMergeVar = new ExtendedMergeVar();
        $secondCartItemMergeVar = new ExtendedMergeVar();
        $thirdCartItemMergeVar = new ExtendedMergeVar();
        $emailMergeVar = new ExtendedMergeVar();

        $firstCartItemMergeVar->setName('item_1_name')->markSynced();
        $secondCartItemMergeVar->setName('item_2_name')->markSynced();
        $thirdCartItemMergeVar->setName('item_3_name')->markSynced();
        $emailMergeVar->setName('email');

        $this->staticSegment->addExtendedMergeVar($firstCartItemMergeVar);
        $this->staticSegment->addExtendedMergeVar($secondCartItemMergeVar);
        $this->staticSegment->addExtendedMergeVar($thirdCartItemMergeVar);
        $this->staticSegment->addExtendedMergeVar($emailMergeVar);

        $this->entityRepository->expects($this->once())->method('find')
            ->with($cartEntityId)->will($this->returnValue($cart));

        $this->listener->onProcessAfter($this->strategyEvent);

        $mergeVarValues = $this->entity->getMergeVarValues();

        $this->assertNotEmpty($mergeVarValues);
        $this->assertArrayHasKey($firstCartItemMergeVar->getTag(), $mergeVarValues);
        $this->assertEquals('CartItem Name 1', $mergeVarValues[$firstCartItemMergeVar->getTag()]);

        $this->assertArrayHasKey($secondCartItemMergeVar->getTag(), $mergeVarValues);
        $this->assertEquals('CartItem Name 2', $mergeVarValues[$secondCartItemMergeVar->getTag()]);

        $this->assertArrayNotHasKey($thirdCartItemMergeVar->getTag(), $mergeVarValues);
        $this->assertArrayNotHasKey($emailMergeVar->getTag(), $mergeVarValues);
    }
}
