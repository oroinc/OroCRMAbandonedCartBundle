<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\ExtendedMergeVar;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar\CartItemsMergeVarProvider;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;

class CartItemsMergeVarProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CartItemsMergeVarProvider
     */
    protected $provider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    protected function setUp()
    {
        $this->abandonedCartCampaignProvider = $this
            ->getMock('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface');
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

    /**
     * @return array
     */
    protected function getExpectedExtendedMergeVars()
    {
        $expectedMergeVars = [];

        for ($i = 1; $i <= CartItemsMergeVarProvider::CART_ITEMS_LIMIT; $i++) {
            $name = sprintf(
                CartItemsMergeVarProvider::CART_ITEM_NAME,
                CartItemsMergeVarProvider::NAME_PREFIX,
                $i
            );
            $label = sprintf(CartItemsMergeVarProvider::CART_ITEM_LABEL, $i);
            $expectedMergeVars[] = [
                'name' => $name,
                'label' => $label
            ];
        }

        return $expectedMergeVars;
    }
}
