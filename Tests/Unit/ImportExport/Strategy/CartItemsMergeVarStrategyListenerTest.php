<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\ImportExport\Strategy;

use Doctrine\Common\Collections\ArrayCollection;
use Oro\Bundle\SegmentBundle\Entity\Segment;
use OroCRM\Bundle\MagentoBundle\Entity\Cart;
use OroCRM\Bundle\MagentoBundle\Entity\CartItem;
use OroCRM\Bundle\MailChimpBundle\Entity\ExtendedMergeVar;
use OroCRM\Bundle\MailChimpBundle\Entity\MemberExtendedMergeVar;
use OroCRM\Bundle\AbandonedCartBundle\ImportExport\Strategy\CartItemsMergeVarStrategyListener;
use OroCRM\Bundle\MailChimpBundle\Entity\StaticSegment;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CartItemsMergeVarStrategyListenerTest extends \PHPUnit_Framework_TestCase
{
    const SEGMENT_ENTITY_CLASS = 'EntityClass';

    /**
     * @var CartItemsMergeVarStrategyListener
     */
    protected $listener;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $doctrineHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $twig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $entityRepository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $strategyEvent;

    /**
     * @var string
     */
    protected $template;

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
        $this->twig = $this->getMockBuilder('\Twig_Environment')->getMock();
        $this->entityRepository = $this
            ->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $this->strategyEvent = $this
            ->getMockBuilder('Oro\Bundle\ImportExportBundle\Event\StrategyEvent')
            ->disableOriginalConstructor()
            ->getMock();
        $this->template = 'cartItem.txt.twig';

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
            $this->twig,
            $this->template
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
        $strategyEvent = $this
            ->getMockBuilder('Oro\Bundle\ImportExportBundle\Event\StrategyEvent')
            ->disableOriginalConstructor()
            ->getMock();

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
        $firstCartItemMergeVar->setName('item_1')->markSynced();

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
        $firstCartItemMergeVar->setName('item_1')->markSynced();

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
        $cartItems = new ArrayCollection([$cartItem1, $cartItem2]);

        $cart->setCartItems($cartItems);

        $this->entity->setMergeVarValuesContext(['entity_id' => 1]);

        $firstCartItemMergeVar = new ExtendedMergeVar();
        $secondCartItemMergeVar = new ExtendedMergeVar();
        $thirdCartItemMergeVar = new ExtendedMergeVar();
        $emailMergeVar = new ExtendedMergeVar();

        $firstCartItemMergeVar->setName('item_1')->markSynced();
        $secondCartItemMergeVar->setName('item_2')->markSynced();
        $thirdCartItemMergeVar->setName('item_3')->markSynced();
        $emailMergeVar->setName('email');

        $this->staticSegment->addExtendedMergeVar($firstCartItemMergeVar);
        $this->staticSegment->addExtendedMergeVar($secondCartItemMergeVar);
        $this->staticSegment->addExtendedMergeVar($thirdCartItemMergeVar);
        $this->staticSegment->addExtendedMergeVar($emailMergeVar);

        $this->entityRepository->expects($this->once())->method('find')
            ->with($cartEntityId)->will($this->returnValue($cart));

        $this->twig->expects($this->at(0))->method('render')
            ->with($this->template, ['item' => $cartItem1, 'index' => 0])
            ->will($this->returnValue('rendered_template_of_cart_item_1'));

        $this->twig->expects($this->at(1))->method('render')
            ->with($this->template, ['item' => $cartItem2, 'index' => 1])
            ->will($this->returnValue('rendered_template_of_cart_item_2'));

        $this->listener->onProcessAfter($this->strategyEvent);

        $mergeVarValues = $this->entity->getMergeVarValues();

        $this->assertNotEmpty($mergeVarValues);
        $this->assertArrayHasKey($firstCartItemMergeVar->getTag(), $mergeVarValues);
        $this->assertEquals('rendered_template_of_cart_item_1', $mergeVarValues[$firstCartItemMergeVar->getTag()]);

        $this->assertArrayHasKey($secondCartItemMergeVar->getTag(), $mergeVarValues);
        $this->assertEquals('rendered_template_of_cart_item_2', $mergeVarValues[$secondCartItemMergeVar->getTag()]);

        $this->assertArrayNotHasKey($thirdCartItemMergeVar->getTag(), $mergeVarValues);
        $this->assertArrayNotHasKey($emailMergeVar->getTag(), $mergeVarValues);
    }
}
