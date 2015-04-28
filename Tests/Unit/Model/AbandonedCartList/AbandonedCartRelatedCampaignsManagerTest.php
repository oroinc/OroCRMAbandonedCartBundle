<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;

use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartRelatedCampaignsManager;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\MailChimpBundle\Entity\StaticSegment;
use OroCRM\Bundle\MailChimpBundle\Entity\Campaign;

class AbandonedCartRelatedCampaignsManagerTest extends \PHPUnit_Framework_TestCase
{
    const STAT_SEGMENT_CLASS_NAME = 'staticSegmentClassName';
    const MAILCHIMP_CAMPAIGN_CLASS_NAME = 'mailchimpCampaignClassName';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartRelatedCampaignsManager
     */
    protected $abandonedCartRelatedCampaignsManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|EntityRepository
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|MarketingList
     */
    protected $marketingList;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|StaticSegment
     */
    protected $staticSegment;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Campaign
     */
    protected $mailchimpCampaign;

    public function setUp()
    {
        $this->managerRegistry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->abandonedCartCampaignProvider = $this
            ->getMock('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface');

        $this->marketingList = $this->getMockBuilder('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList')
            ->disableOriginalConstructor()
            ->getMock();

        $this->staticSegment = $this->getMockBuilder('OroCRM\Bundle\MailChimpBundle\Entity\StaticSegment')
            ->disableOriginalConstructor()
            ->getMock();

        $this->mailchimpCampaign = $this->getMockBuilder('OroCRM\Bundle\MailChimpBundle\Entity\Campaign')
            ->disableOriginalConstructor()
            ->getMock();

        $this->abandonedCartRelatedCampaignsManager = new AbandonedCartRelatedCampaignsManager(
            $this->managerRegistry,
            $this->abandonedCartCampaignProvider,
            self::STAT_SEGMENT_CLASS_NAME,
            self::MAILCHIMP_CAMPAIGN_CLASS_NAME
        );
    }

    protected function tearDown()
    {
        unset($this->managerRegistry);
        unset($this->repository);
        unset($this->abandonedCartCampaignProvider);
        unset($this->marketingList);
        unset($this->staticSegment);
        unset($this->mailchimpCampaign);
        unset($this->placeholderFilter);
    }

    public function testIsApplicableWhenAbandonedCartAndCampaign()
    {
        $this->abandonedCartCampaignProvider
            ->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->will($this->returnValue(true));

        $this->managerRegistry
            ->expects($this->at(0))->method('getRepository')
            ->with(self::STAT_SEGMENT_CLASS_NAME)
            ->will($this->returnValue($this->repository));

        $this->repository
            ->expects($this->at(0))->method('findOneBy')
            ->with(['marketingList' => $this->marketingList])
            ->will($this->returnValue($this->staticSegment));

        $this->managerRegistry
            ->expects($this->at(1))->method('getRepository')
            ->with(self::MAILCHIMP_CAMPAIGN_CLASS_NAME)
            ->will($this->returnValue($this->repository));

        $this->repository
            ->expects($this->at(1))->method('findOneBy')
            ->with(['staticSegment' => $this->staticSegment])
            ->will($this->returnValue($this->mailchimpCampaign));

        $this->assertEquals(
            true,
            $this->abandonedCartRelatedCampaignsManager->isApplicable($this->marketingList)
        );
    }

    public function testIsApplicableWhenAbandonedCartAndNoCampaign()
    {
        $this->abandonedCartCampaignProvider
            ->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->will($this->returnValue(true));

        $this->managerRegistry
            ->expects($this->at(0))->method('getRepository')
            ->with(self::STAT_SEGMENT_CLASS_NAME)
            ->will($this->returnValue($this->repository));

        $this->repository
            ->expects($this->at(0))->method('findOneBy')
            ->with(['marketingList' => $this->marketingList])
            ->will($this->returnValue($this->staticSegment));

        $this->managerRegistry
            ->expects($this->at(1))->method('getRepository')
            ->with(self::MAILCHIMP_CAMPAIGN_CLASS_NAME)
            ->will($this->returnValue($this->repository));

        $this->repository
            ->expects($this->at(1))->method('findOneBy')
            ->with(['staticSegment' => $this->staticSegment])
            ->will($this->returnValue(null));

        $this->assertEquals(
            false,
            $this->abandonedCartRelatedCampaignsManager->isApplicable($this->marketingList)
        );
    }

    public function testIsApplicableWhenMarketingList()
    {
        $this->abandonedCartCampaignProvider
            ->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->will($this->returnValue(false));

        $this->managerRegistry
            ->expects($this->never())->method('getRepository');

        $this->assertEquals(
            false,
            $this->abandonedCartRelatedCampaignsManager->isApplicable($this->marketingList)
        );
    }
}
