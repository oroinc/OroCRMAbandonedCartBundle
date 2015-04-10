<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\Model\AbandonedCartList;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use OroCRM\Bundle\AbandonedCartBundle\Entity\CampaignAbandonedCartRelation;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartList\CampaignAbandonedCartRelationManager;
use OroCRM\Bundle\CampaignBundle\Entity\Campaign;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;

class CampaignAbandonedCartRelationManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CampaignAbandonedCartRelationManager
     */
    private $campaignAbandonedCartRelationManager;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var EntityRepository
     */
    private $repository;

    /**
     * @var MarketingList
     */
    private $marketingList;

    protected function setUp()
    {
        $this->em = $this->getMockBuilder('Doctrine\ORM\EntityManager')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->marketingList = $this->getMockBuilder('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList')
            ->disableOriginalConstructor()
            ->getMock();

        $this->campaignAbandonedCartRelationManager = new CampaignAbandonedCartRelationManager($this->em);
    }

    public function testGetCampaignByMarketingListIfRelationExists()
    {
        $campaign = new Campaign();

        $campaignAbandonedCartRelation = new CampaignAbandonedCartRelation();
        $campaignAbandonedCartRelation->setMarketingList($this->marketingList);
        $campaignAbandonedCartRelation->setCampaign($campaign);

        $this->em
            ->expects($this->once())->method('getRepository')
            ->with('OroCRMAbandonedCartBundle:CampaignAbandonedCartRelation')
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

        $campaignAbandonedCartRelation = new CampaignAbandonedCartRelation();
        $campaignAbandonedCartRelation->setMarketingList($this->marketingList);
        $campaignAbandonedCartRelation->setCampaign($campaign);

        $this->em
            ->expects($this->once())->method('getRepository')
            ->with('OroCRMAbandonedCartBundle:CampaignAbandonedCartRelation')
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
