<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use OroCRM\Bundle\AbandonedCartBundle\EventListener\Datagrid\AbandonedCartListener;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
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
     * @var \PHPUnit_Framework_MockObject_MockObject|DatagridConfiguration
     */
    protected $datagridConfig;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @var array
     */
    private $config;

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

        $this->datagridConfig = $this->getMockBuilder('Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration')
            ->disableOriginalConstructor()
            ->getMock();

        $this->config = ['actions' => ['subscribe' => 1]];

        $this->listener = new AbandonedCartListener(
            $this->marketingListHelper,
            $this->abandonedCartCampaignProvider
        );
    }

    public function testOnBuildBeforeWithEmptySubscribeAction()
    {
        $this->buildBefore->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue([]));

        $this->buildBefore->expects($this->never())
            ->method('getDatagrid');

        $this->listener->onBuildBefore($this->buildBefore);
    }

    public function testOnBuildBeforeWithSubscribeActionWhenAbandonedCampaign()
    {
        $this->initDatagridConfig();

        $this->buildBefore->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue($this->datagridConfig));

        $datagrid = $this->getMock('Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface');

        $this->buildBefore->expects($this->once())
            ->method('getDatagrid')
            ->will($this->returnValue($datagrid));

        $gridName = 'gridName';

        $datagrid
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($gridName));

        /** @var \PHPUnit_Framework_MockObject_MockObject|MarketingList $marketingList */
        $marketingList = $this
            ->getMockBuilder('OroCRM\Bundle\MarketingListBundle\Entity\MarketingList')
            ->disableOriginalConstructor()
            ->getMock();

        $this->marketingListHelper
            ->expects($this->once())
            ->method('getMarketingListIdByGridName')
            ->with($this->equalTo($gridName));

        $this->marketingListHelper
            ->expects($this->once())
            ->method('getMarketingList')
            ->will($this->returnValue($marketingList));

        $this->abandonedCartCampaignProvider
            ->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->with($marketingList)
            ->will($this->returnValue(true));

        $this->datagridConfig->expects($this->once())
            ->method('offsetUnsetByPath')
            ->with('[actions][subscribe]');

        $this->listener->onBuildBefore($this->buildBefore);
    }

    public function testOnBuildBeforeWithSubscribeActionWhenNotAbandonedCampaign()
    {
        $this->initDatagridConfig();

        $this->buildBefore->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue($this->datagridConfig));

        $datagrid = $this->getMock('Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface');

        $this->buildBefore->expects($this->once())
            ->method('getDatagrid')
            ->will($this->returnValue($datagrid));

        $gridName = 'gridName';

        $datagrid
            ->expects($this->once())
            ->method('getName')
            ->will($this->returnValue($gridName));

        /** @var \PHPUnit_Framework_MockObject_MockObject|MarketingList $marketingList */
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

        $this->abandonedCartCampaignProvider
            ->expects($this->once())
            ->method('getAbandonedCartCampaign')
            ->with($marketingList)
            ->will($this->returnValue(false));

        $this->datagridConfig->expects($this->never())
            ->method('offsetUnsetByPath');

        $this->listener->onBuildBefore($this->buildBefore);
    }

    protected function initDatagridConfig()
    {
        $offsetResult = $this->config['actions'];

        $this->datagridConfig->expects($this->any())
            ->method('offsetExists')
            ->with('actions')
            ->willReturn(true);

        $this->datagridConfig->expects($this->any())
            ->method('offsetGet')
            ->with('actions')
            ->will($this->returnValue($offsetResult));
    }
}
