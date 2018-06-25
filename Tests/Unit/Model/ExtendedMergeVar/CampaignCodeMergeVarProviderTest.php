<?php

namespace Oro\Bundle\AbandonedCartBundle\Tests\Unit\Model\ExtendedMergeVar;

use Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;
use Oro\Bundle\AbandonedCartBundle\Model\ExtendedMergeVar\CampaignCodeMergeVarProvider;
use Oro\Bundle\MarketingListBundle\Entity\MarketingList;
use Symfony\Component\Translation\TranslatorInterface;

class CampaignCodeMergeVarProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var CampaignCodeMergeVarProvider
     */
    protected $provider;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @var \PHPUnit\Framework\MockObject\MockObject|TranslatorInterface
     */
    protected $translator;

    protected function setUp()
    {
        $this->abandonedCartCampaignProvider = $this
            ->createMock('Oro\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface');
        $this->translator = $this->createMock('Symfony\Component\Translation\TranslatorInterface');
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
                    ['oro.abandonedcart.campaign_code_mergevar.label', [], null, null, 'Campaign Code'],
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
