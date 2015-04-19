<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use OroCRM\Bundle\AbandonedCartBundle\EventListener\Datagrid\AbandonedCartListener;
use OroCRM\Bundle\MarketingListBundle\Model\MarketingListHelper;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;

class AbandonedCartListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartListener
     */
    protected $listener;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|MarketingListHelper
     */
    protected $marketingListHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|BuildBefore
     */
    protected $buildBefore;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $datagridConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    protected function setUp()
    {
        $this->marketingListHelper = $this
            ->getMockBuilder('OroCRM\Bundle\MarketingListBundle\Model\MarketingListHelper')
            ->disableOriginalConstructor()
            ->getMock();

        $this->buildBefore = $this->getMockBuilder('Oro\Bundle\DataGridBundle\Event\BuildBefore')
            ->disableOriginalConstructor()
            ->getMock();

        $this->abandonedCartCampaignProvider = $this
            ->getMock('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface');

        $this->listener = new AbandonedCartListener(
            $this->marketingListHelper,
            $this->abandonedCartCampaignProvider
        );
    }

    public function testOnBuildBeforeWithEmptySubscribeAction()
    {
        $this->datagridConfig = $this->getMockBuilder('Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration')
            ->disableOriginalConstructor()
            ->getMock();

        $this->buildBefore->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue(array()));

        $this->buildBefore->expects($this->never())
            ->method('getDatagrid');

        $this->listener->onBuildBefore($this->buildBefore);
    }

    public function testOnBuildBeforeWithSubscribeAction()
    {
        $this->datagridConfig = $this->getMockBuilder('Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration')
            ->disableOriginalConstructor()
            ->getMock();

        $this->buildBefore->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue(array('actions' => array('subscribe' => 1))));

        $datagrid = $this->getMock('Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface');

        $this->buildBefore->expects($this->once())
            ->method('getDatagrid')
            ->will($this->returnValue($datagrid));

        $gridName = 'gridName';

        $datagrid
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($gridName));

        $marketingList = $this
            ->getMockBuilder('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList')
            ->disableOriginalConstructor()
            ->getMock();

        $this->marketingListHelper
            ->expects($this->once())
            ->method('getMarketingListIdByGridName')
            ->with($this->equalTo($gridName))
            ->will($this->returnValue($marketingList->getId()));

        $this->marketingListHelper
            ->expects($this->once())
            ->method('getMarketingList')
            ->with($this->equalTo($marketingList->getId()))
            ->will($this->returnValue($marketingList));

        $marketingList
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(1));

        $this->listener->onBuildBefore($this->buildBefore);
    }
}
