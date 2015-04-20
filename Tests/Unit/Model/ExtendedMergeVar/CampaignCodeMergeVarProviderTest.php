<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\ExtendedMergeVar;

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

    protected function setUp()
    {
        $this->abandonedCartCampaignProvider = $this
            ->getMock('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface');
        $this->provider = new CampaignCodeMergeVarProvider($this->abandonedCartCampaignProvider);
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
     * @param array $expected
     */
    public function testProvide($expected)
    {
        $marketingList = new MarketingList();

        $this->abandonedCartCampaignProvider->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->with($marketingList)
            ->will($this->returnValue(new AbandonedCartCampaign()));

        $actualExtendedMergeVars = $this->provider->provideExtendedMergeVars($marketingList);

        $this->assertEquals($expected, $actualExtendedMergeVars);
    }

    /**
     * @return array
     */
    public function getExpectedExtendedMergeVars()
    {
        return [
            [
                [
                    [
                        'name' => CampaignCodeMergeVarProvider::CAMPAIGN_CODE_NAME,
                        'label' => CampaignCodeMergeVarProvider::CAMPAIGN_CODE_LABEL
                    ]
                ]
            ]
        ];
    }
}
