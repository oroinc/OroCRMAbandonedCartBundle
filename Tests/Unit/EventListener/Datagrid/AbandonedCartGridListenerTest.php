<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Event\BuildBefore;
use OroCRM\Bundle\AbandonedCartBundle\EventListener\Datagrid\AbandonedCartGridListener;
use OroCRM\Bundle\MarketingListBundle\Entity\MarketingListType;

class AbandonedCartGridListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbandonedCartGridListener
     */
    protected $listener;

    protected function setUp()
    {
        $this->listener = new AbandonedCartGridListener();
    }

    /**
     * @param array $parameters
     * @param array $expectedUnsets
     * @dataProvider onBuildBeforeDataProvider
     */
    public function testOnBuildBefore(array $parameters, array $expectedUnsets = [])
    {
        $buildBeforeEvent = $this->createBuildBeforeEvent($expectedUnsets, $parameters);
        $this->listener->onBuildBefore($buildBeforeEvent);
    }

    /**
     * @return array
     */
    public function onBuildBeforeDataProvider()
    {
        return array(
            'no filters' => array(
                'parameters' => array(),
            ),
            'filter by type' => array(
                'parameters' => array(
                    'listType' => MarketingListType::TYPE_DYNAMIC,
                ),
                'expectedUnsets' => array(
                    '[columns][listType]',
                    '[filters][columns][listType]',
                    '[sorters][columns][listType]',
                ),
            ),
        );
    }

    /**
     * @param array $expectedUnsets
     * @param array $parameters
     * @return BuildBefore
     */
    protected function createBuildBeforeEvent(array $expectedUnsets, array $parameters)
    {
        $config = $this->getMockBuilder('Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration')
            ->setMethods(array('offsetUnsetByPath'))
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($expectedUnsets as $iteration => $value) {
            $config->expects($this->at($iteration))->method('offsetUnsetByPath')->with($value);
        }

        $dataGrid = $this->getMock('Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface');

        return new BuildBefore($dataGrid, $config);
    }

    protected function tearDown()
    {
        unset($this->listener);
    }
}
