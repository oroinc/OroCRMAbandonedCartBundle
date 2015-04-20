<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Placeholder;

use OroCRM\Bundle\AbandonedCartBundle\Placeholder\CampaignsPlaceholderFilter;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CampaignsPlaceholderFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CampaignsPlaceholderFilter
     */
    protected $placeholderFilter;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    public function setUp()
    {
        $this->abandonedCartCampaignProvider = $this
            ->getMock('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface');

        $this->placeholderFilter = new CampaignsPlaceholderFilter($this->abandonedCartCampaignProvider);
    }

    public function testIsApplicableWhenAbandonedCart()
    {
        $this->abandonedCartCampaignProvider
            ->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->will($this->returnValue(true));

        $this->assertEquals(
            true,
            $this->placeholderFilter->isApplicable(new MarketingList())
        );
    }

    public function testIsApplicableWhenNotAbandonedCart()
    {
        $this->abandonedCartCampaignProvider
            ->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->will($this->returnValue(false));

        $this->assertEquals(
            false,
            $this->placeholderFilter->isApplicable(new MarketingList())
        );
    }

    protected function tearDown()
    {
        unset($this->placeholderFilter);
    }
}
