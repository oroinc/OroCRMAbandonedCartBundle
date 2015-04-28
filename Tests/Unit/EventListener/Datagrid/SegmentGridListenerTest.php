<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datagrid\ParameterBag;
use Oro\Bundle\DataGridBundle\Event\PreBuild;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingList;
use OroCRM\Bundle\MarketingListBundle\Model\MarketingListHelper;
use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\AbandonedCartBundle\EventListener\Datagrid\SegmentGridListener;
use OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface;

class SegmentGridListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SegmentGridListener
     */
    protected $listener;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|MarketingListHelper
     */
    protected $marketingListHelper;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|AbandonedCartCampaignProviderInterface
     */
    protected $abandonedCartCampaignProvider;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|DatagridConfiguration
     */
    protected $config;

    protected function setUp()
    {
        $this->marketingListHelper = $this
            ->getMockBuilder('OroCRM\Bundle\MarketingListBundle\Model\MarketingListHelper')
            ->disableOriginalConstructor()
            ->getMock();
        $this->abandonedCartCampaignProvider = $this
            ->getMock('OroCRM\Bundle\AbandonedCartBundle\Model\AbandonedCartCampaignProviderInterface');
        $this->config = $this
            ->getMockBuilder('Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration')
            ->disableOriginalConstructor()
            ->getMock();
        $this->config->expects($this->once())->method('getName')->will($this->returnValue('dummy_grid_name'));
        $this->listener = new SegmentGridListener(
            $this->marketingListHelper,
            $this->abandonedCartCampaignProvider
        );
    }

    protected function tearDown()
    {
        unset($this->listener, $this->marketingListHelper, $this->abandonedCartCampaignProvider);
    }

    public function testOnPreBuildWhenIsNotMixin()
    {
        $parameters = new ParameterBag([]);

        $this->marketingListHelper->expects($this->never())->method('getMarketingListIdByGridName');

        $event = new PreBuild($this->config, $parameters);
        $this->listener->onPreBuild($event);
    }

    public function testOnPreBuildWhenGridIsNotForMarketingList()
    {
        $parameters = new ParameterBag(['grid-mixin' => true]);

        $this->marketingListHelper->expects($this->once())->method('getMarketingListIdByGridName')
            ->with('dummy_grid_name')->will($this->returnValue(null));

        $event = new PreBuild($this->config, $parameters);
        $this->listener->onPreBuild($event);
    }

    public function testOnPreBuildWhenGridIsNotForAbandonedCart()
    {
        $marketingList = new MarketingList();

        $parameters = new ParameterBag(['grid-mixin' => true]);

        $this->marketingListHelper->expects($this->once())->method('getMarketingListIdByGridName')
            ->with('dummy_grid_name')->will($this->returnValue(1));

        $this->marketingListHelper->expects($this->once())->method('getMarketingList')
            ->with(1)->will($this->returnValue($marketingList));

        $this->abandonedCartCampaignProvider->expects($this->once())->method('getAbandonedCartCampaign')
            ->with($marketingList)->will($this->returnValue(null));

        $this->config->expects($this->never())->method('offsetUnsetByPath');

        $event = new PreBuild($this->config, $parameters);
        $this->listener->onPreBuild($event);
    }

    /**
     * @dataProvider unsetColumnsDataProvider
     * @param array $expectedConfigUnsetPaths
     */
    public function testOnPreBuild(array $expectedConfigUnsetPaths)
    {
        $marketingList = new MarketingList();
        $abandonedCartCampaign = new AbandonedCartCampaign();

        $parameters = new ParameterBag(['grid-mixin' => true]);

        $this->marketingListHelper->expects($this->once())->method('getMarketingListIdByGridName')
            ->with('dummy_grid_name')->will($this->returnValue(1));

        $this->marketingListHelper->expects($this->once())->method('getMarketingList')
            ->with(1)->will($this->returnValue($marketingList));

        $this->abandonedCartCampaignProvider->expects($this->once())->method('getAbandonedCartCampaign')
            ->with($marketingList)->will($this->returnValue($abandonedCartCampaign));

        foreach ($expectedConfigUnsetPaths as $index => $path) {
            $this->config->expects($this->at($index + 1))->method('offsetUnsetByPath')
                ->with($path)->will($this->returnSelf());
        }

        $event = new PreBuild($this->config, $parameters);
        $this->listener->onPreBuild($event);
    }

    /**
     * @return array
     */
    public function unsetColumnsDataProvider()
    {
        return [
            [
                [
                    '[columns][contactedTimes]',
                    '[sorters][columns][contactedTimes]',
                    '[filters][columns][contactedTimes]',
                    '[columns][lastContactedAt]',
                    '[sorters][columns][lastContactedAt]',
                    '[filters][columns][lastContactedAt]'
                ]
            ]
        ];
    }
}
