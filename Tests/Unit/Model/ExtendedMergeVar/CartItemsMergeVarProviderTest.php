<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\ExtendedMergeVar;

use Symfony\Component\Translation\TranslatorInterface;

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

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TranslatorInterface
     */
    protected $translator;

    protected function setUp()
    {
        $this->abandonedCartCampaignProvider = $this
            ->getMock('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface');
        $this->translator = $this->getMock('Symfony\Component\Translation\TranslatorInterface');
        $this->provider = new CartItemsMergeVarProvider($this->abandonedCartCampaignProvider, $this->translator);
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

    /**
     * @dataProvider getExpectedExtendedMergeVars
     * @param array $expectedTranslations
     * @param array $expectedMerVars
     */
    public function testProvide(array $expectedTranslations, array $expectedMerVars)
    {
        $marketingList = new MarketingList();

        $this->abandonedCartCampaignProvider->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->with($marketingList)
            ->will($this->returnValue(new AbandonedCartCampaign()));

        $this->translator->expects($this->exactly(count($expectedTranslations)))->method('trans')
            ->will($this->returnValueMap($expectedTranslations));

        $actualExtendedMergeVars = $this->provider->provideExtendedMergeVars($marketingList);

        $this->assertCount(15, $actualExtendedMergeVars);
        $this->assertEquals($expectedMerVars, $actualExtendedMergeVars);
    }

    /**
     * @return array
     */
    public function getExpectedExtendedMergeVars()
    {
        return [
            [
                [
                    ['orocrm.abandonedcart.mergevar.cart_item.url.label', ['%index%' => 1], null, null, 'URL (1)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.name.label', ['%index%' => 1], null, null, 'Name (1)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.qty.label', ['%index%' => 1], null, null, 'Qty (1)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.price.label', ['%index%' => 1], null, null, 'Price (1)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.total.label', ['%index%' => 1], null, null, 'Total (1)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.url.label', ['%index%' => 2], null, null, 'URL (2)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.name.label', ['%index%' => 2], null, null, 'Name (2)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.qty.label', ['%index%' => 2], null, null, 'Qty (2)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.price.label', ['%index%' => 2], null, null, 'Price (2)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.total.label', ['%index%' => 2], null, null, 'Total (2)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.url.label', ['%index%' => 3], null, null, 'URL (3)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.name.label', ['%index%' => 3], null, null, 'Name (3)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.qty.label', ['%index%' => 3], null, null, 'Qty (3)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.price.label', ['%index%' => 3], null, null, 'Price (3)'],
                    ['orocrm.abandonedcart.mergevar.cart_item.total.label', ['%index%' => 3], null, null, 'Total (3)']
                ],
                [
                    ['name' => 'item_1_url', 'label' => 'URL (1)'],
                    ['name' => 'item_1_name', 'label' => 'Name (1)'],
                    ['name' => 'item_1_qty', 'label' => 'Qty (1)'],
                    ['name' => 'item_1_price', 'label' => 'Price (1)'],
                    ['name' => 'item_1_total', 'label' => 'Total (1)'],
                    ['name' => 'item_2_url', 'label' => 'URL (2)'],
                    ['name' => 'item_2_name', 'label' => 'Name (2)'],
                    ['name' => 'item_2_qty', 'label' => 'Qty (2)'],
                    ['name' => 'item_2_price', 'label' => 'Price (2)'],
                    ['name' => 'item_2_total', 'label' => 'Total (2)'],
                    ['name' => 'item_3_url', 'label' => 'URL (3)'],
                    ['name' => 'item_3_name', 'label' => 'Name (3)'],
                    ['name' => 'item_3_qty', 'label' => 'Qty (3)'],
                    ['name' => 'item_3_price', 'label' => 'Price (3)'],
                    ['name' => 'item_3_total', 'label' => 'Total (3)']
                ]
            ]
        ];
    }
}
