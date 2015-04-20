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

        $this->assertCount(3, $actualExtendedMergeVars);
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
                    ['orocrm.abandonedcart.cart_item_mergevar.label', ['%index%' => 1], null, null, 'Cart Item (1)'],
                    ['orocrm.abandonedcart.cart_item_mergevar.label', ['%index%' => 2], null, null, 'Cart Item (2)'],
                    ['orocrm.abandonedcart.cart_item_mergevar.label', ['%index%' => 3], null, null, 'Cart Item (3)']
                ],
                [
                    [
                        'name' => 'item_1',
                        'label' => 'Cart Item (1)'
                    ],
                    [
                        'name' => 'item_2',
                        'label' => 'Cart Item (2)'
                    ],
                    [
                        'name' => 'item_3',
                        'label' => 'Cart Item (3)'
                    ]
                ]
            ]
        ];
    }
}
