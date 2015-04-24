<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use Doctrine\Common\Persistence\ManagerRegistry;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\AbandonedCartConversionManager;
use OroCRM\Bundle\MailChimpBundle\Entity\StaticSegment;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationManager;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking\TrackingStatProviderFactory;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class AbandonedCartConversionManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartConversionManager
     */
    protected $conversionManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ManagerRegistry
     */
    protected $managerRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $em;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|MarketingList
     */
    protected $marketingList;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|CampaignAbandonedCartRelationManager
     */
    protected $campaignRelationManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $campaign;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|TrackingStatProviderFactory
     */
    protected $statProviderFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $statResult;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $trackingStatProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartConversion
     */
    protected $conversion;

    /**
     * @var string
     */
    protected $magentoOrderClassName;

    /**
     * @var string
     */
    protected $campaignClassName;

    /**
     * @var string
     */
    protected $staticSegmentClassName;

    /**
     * @var string
     */
    protected $abandonedCartConversion;

    /**
     * @var string
     */
    protected $trackingVisitEventClassName;

    protected function setUp()
    {
        $this->managerRegistry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->marketingList = $this->getMockBuilder('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList')
            ->disableOriginalConstructor()
            ->getMock();

        $this->campaign = $this->getMockBuilder('OroCRM\Bundle\CampaignBundle\Entity\Campaign')
            ->disableOriginalConstructor()
            ->getMock();

        $this->conversion = $this->getMockBuilder('OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartConversion')
            ->disableOriginalConstructor()
            ->getMock();

        $this->statProviderFactory = $this->getMockBuilder(
            'OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking\TrackingStatProviderFactory'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->statResult = $this->getMockBuilder(
            'OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking\StatResultInterface'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->trackingStatProvider = $this->getMockBuilder(
            'OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\Tracking\TrackingStatProviderInterface'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->campaignRelationManager = $this->getMockBuilder(
            'OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationManager'
        )
            ->disableOriginalConstructor()
            ->getMock();

        $this->magentoOrderClassName = 'MagentoOrderClassName';
        $this->campaignClassName = 'CampaignClassName';
        $this->staticSegmentClassName = 'StaticSegmentClassName';
        $this->abandonedCartConversion = 'AbandonedCartConversionClassName';
        $this->trackingVisitEventClassName = 'TrackingVisitEventClassName';

        $this->conversionManager = new AbandonedCartConversionManager(
            $this->managerRegistry,
            $this->campaignRelationManager,
            $this->statProviderFactory,
            $this->magentoOrderClassName,
            $this->campaignClassName,
            $this->staticSegmentClassName,
            $this->abandonedCartConversion,
            $this->trackingVisitEventClassName
        );
    }

    public function testFindConversionByMarketingList()
    {
        $this->managerRegistry
            ->expects($this->once())->method('getRepository')
            ->with($this->abandonedCartConversion)
            ->will($this->returnValue($this->repository));

        $this->marketingList
            ->expects($this->once())->method('getId')
            ->will($this->returnValue('testId'));

        $this->repository
            ->expects($this->once())->method('findOneBy')
            ->with(['marketingList' => 'testId'])
            ->will($this->returnValue($this->conversion));

        $this->conversionManager->findConversionByMarketingList($this->marketingList);
    }

    public function testFindStaticSegment()
    {
        $conversion = new AbandonedCartConversion();
        $conversion->setMarketingList($this->marketingList);
        $staticSegment = new StaticSegment();

        $this->managerRegistry
            ->expects($this->once())->method('getRepository')
            ->with($this->staticSegmentClassName)
            ->will($this->returnValue($this->repository));

        $this->repository
            ->expects($this->once())->method('findOneBy')
            ->with(['marketingList' => $this->marketingList])
            ->will($this->returnValue($staticSegment));

        $this->conversionManager->findStaticSegment($conversion);
    }

    public function testFindAbandonedCartRelatedStatistic()
    {
        $this->campaignRelationManager
            ->expects($this->once())->method('getCampaignByMarketingList')
            ->with($this->equalTo($this->marketingList))
            ->will($this->returnValue($this->campaign));

        $this->statProviderFactory
            ->expects($this->once())->method('create')
            ->will($this->returnValue($this->trackingStatProvider));

        $this->trackingStatProvider
            ->expects($this->once())->method('getStatResult')
            ->with($this->equalTo($this->campaign))
            ->will($this->returnValue($this->statResult));

        $this->conversionManager->findAbandonedCartRelatedStatistic($this->marketingList);
    }
}
