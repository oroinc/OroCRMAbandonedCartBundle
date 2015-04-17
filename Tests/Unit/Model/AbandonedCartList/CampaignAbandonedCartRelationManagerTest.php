<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationManager;
use OroCRM\Bundle\CampaignBundle\Entity\Campaign;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CampaignAbandonedCartRelationManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CampaignAbandonedCartRelationManager
     */
    protected $campaignAbandonedCartRelationManager;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $managerRegistry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $marketingList;

    protected function setUp()
    {
        $this->managerRegistry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->marketingList = $this->getMockBuilder('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList')
            ->disableOriginalConstructor()
            ->getMock();

        $this->campaignAbandonedCartRelationManager = new CampaignAbandonedCartRelationManager($this->managerRegistry);
    }

    public function testGetCampaignByMarketingListIfRelationExists()
    {
        $campaign = new Campaign();

        $campaignAbandonedCartRelation = new AbandonedCartCampaign();
        $campaignAbandonedCartRelation->setMarketingList($this->marketingList);
        $campaignAbandonedCartRelation->setCampaign($campaign);

        $this->managerRegistry
            ->expects($this->once())->method('getRepository')
            ->with('OroCRMAbandonedCartBundle:AbandonedCartCampaign')
            ->will($this->returnValue($this->repository));

        $this->marketingList
            ->expects($this->once())->method('getId')
            ->will($this->returnValue('testId'));

        $this->repository
            ->expects($this->once())->method('findOneBy')
            ->with(array('marketingList' => 'testId'))
            ->will($this->returnValue($campaignAbandonedCartRelation));


        $returnedCampaign = $this->campaignAbandonedCartRelationManager
            ->getCampaignByMarketingList($this->marketingList);

        $this->assertNotNull($returnedCampaign);
        $this->assertEquals($returnedCampaign, $campaignAbandonedCartRelation->getCampaign());
    }

    public function testGetCampaignByMarketingListIfRelationDoesNotExist()
    {
        $campaign = new Campaign();

        $campaignAbandonedCartRelation = new AbandonedCartCampaign();
        $campaignAbandonedCartRelation->setMarketingList($this->marketingList);
        $campaignAbandonedCartRelation->setCampaign($campaign);

        $this->managerRegistry
            ->expects($this->once())->method('getRepository')
            ->with('OroCRMAbandonedCartBundle:AbandonedCartCampaign')
            ->will($this->returnValue($this->repository));

        $this->marketingList
            ->expects($this->once())->method('getId')
            ->will($this->returnValue('testId'));

        $this->repository
            ->expects($this->once())->method('findOneBy')
            ->with(array('marketingList' => 'testId'))
            ->will($this->returnValue(null));


        $returnedCampaign = $this->campaignAbandonedCartRelationManager
            ->getCampaignByMarketingList($this->marketingList);

        $this->assertNull($returnedCampaign);
    }
}
