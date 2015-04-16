<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\ExtendedMergeVar;

use OroCRM\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar\CartItemsMergeVarProvider;
use OroCRM\Bundle\AbandonedCartBundle\Model\MarketingList\AbandonedCartSource;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CartItemsMergeVarProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CartItemsMergeVarProvider
     */
    protected $provider;

    protected function setUp()
    {
        $this->provider = new CartItemsMergeVarProvider();
    }

    public function testProvideWhenMarketingListIsNotAbandonedCart()
    {
        $marketingList = new MarketingList();

        $this->assertEmpty($this->provider->provideExtendedMergeVars($marketingList));
    }

    public function testProvide()
    {
        $marketingList = new MarketingList();
        $marketingList->setSource(AbandonedCartSource::SOURCE_CODE);

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
