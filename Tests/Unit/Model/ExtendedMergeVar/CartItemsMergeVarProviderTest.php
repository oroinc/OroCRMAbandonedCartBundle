<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\ExtendedMergeVar;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar\CartItemsMergeVarProvider;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CartItemsMergeVarProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CartItemsMergeVarProvider
     */
    protected $provider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $abandonedCartCampaignProvider;

    protected function setUp()
    {
        $this->abandonedCartCampaignProvider = $this
            ->getMockBuilder('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface')
            ->getMock();
        $this->provider = new CartItemsMergeVarProvider($this->abandonedCartCampaignProvider);
    }

    public function testProvideForNotAbandonedCartCampaign()
    {
        $marketingList = new MarketingList();

        $this->abandonedCartCampaignProvider->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->with($marketingList)
            ->will($this->returnValue(null));

        $this->assertEmpty($this->provider->provideExtendedMergeVars($marketingList));
    }

    public function testProvide()
    {
        $marketingList = new MarketingList();

        $this->abandonedCartCampaignProvider->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->with($marketingList)
            ->will($this->returnValue(new AbandonedCartCampaign()));

        $actualExtendedMergeVars = $this->provider->provideExtendedMergeVars($marketingList);

        $this->assertCount(3, $actualExtendedMergeVars);
        $this->assertEquals($this->getExpectedExtendedMergeVars(), $actualExtendedMergeVars);
    }

    protected function getExpectedExtendedMergeVars()
    {
        return [
            [
                'name' => CartItemsMergeVarProvider::CART_ITEM_1_NAME,
                'label' => CartItemsMergeVarProvider::CART_ITEM_1_LABEL
            ],
            [
                'name' => CartItemsMergeVarProvider::CART_ITEM_2_NAME,
                'label' => CartItemsMergeVarProvider::CART_ITEM_2_LABEL
            ],
            [
                'name' => CartItemsMergeVarProvider::CART_ITEM_3_NAME,
                'label' => CartItemsMergeVarProvider::CART_ITEM_3_LABEL
            ]
        ];
    }
}
