<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\ExtendedMergeVar;

use Symfony\Component\Translation\TranslatorInterface;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;
use OroCRM\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar\CampaignCodeMergeVarProvider;

class CampaignCodeMergeVarProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CampaignCodeMergeVarProvider
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
        $this->provider = new CampaignCodeMergeVarProvider($this->abandonedCartCampaignProvider, $this->translator);
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
    public function testProvide(array $expectedTranslations, $expectedMerVars)
    {
        $marketingList = new MarketingList();

        $this->abandonedCartCampaignProvider->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->with($marketingList)
            ->will($this->returnValue(new AbandonedCartCampaign()));

        $this->translator->expects($this->exactly(count($expectedTranslations)))->method('trans')
            ->will($this->returnValueMap($expectedTranslations));

        $actualExtendedMergeVars = $this->provider->provideExtendedMergeVars($marketingList);

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
                    ['orocrm.abandonedcart.campaign_code_mergevar.label', [], null, null, 'Campaign Code'],
                ],
                [
                    [
                        'name' => 'campaign_code',
                        'label' => 'Campaign Code'
                    ]
                ]
            ]
        ];
    }
}
