<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\EventListener\Datagrid;

use Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration;
use Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface;
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

    protected function tearDown()
    {
        unset($this->listener);
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
        return [
            'no filters' => [
                'parameters' => [],
            ],
            'filter by type' => [
                'parameters' => [
                    'listType' => MarketingListType::TYPE_DYNAMIC,
                ],
                'expectedUnsets' => [
                    '[columns][listType]',
                    '[filters][columns][listType]',
                    '[sorters][columns][listType]',
                ],
            ],
        ];
    }

    /**
     * @param array $expectedUnsets
     * @param array $parameters
     * @return BuildBefore
     */
    protected function createBuildBeforeEvent(array $expectedUnsets, array $parameters)
    {
        /**
         * @var \PHPUnit_Framework_MockObject_MockObject|DatagridConfiguration $config
         */
        $config = $this->getMockBuilder('Oro\Bundle\DataGridBundle\Datagrid\Common\DatagridConfiguration')
            ->setMethods(['offsetUnsetByPath'])
            ->disableOriginalConstructor()
            ->getMock();

        foreach ($expectedUnsets as $iteration => $value) {
            $config->expects($this->at($iteration))->method('offsetUnsetByPath')->with($value);
        }

        /**
         * @var \PHPUnit_Framework_MockObject_MockObject|DatagridInterface $dataGrid
         */
        $dataGrid = $this->getMock('Oro\Bundle\DataGridBundle\Datagrid\DatagridInterface');

        $dataGrid->expects($this->any())
            ->method('getParameters')
            ->will($this->returnValue($this->createParameterBag($parameters)));

        return new BuildBefore($dataGrid, $config);
    }

    /**
     * @param array $data
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function createParameterBag(array $data)
    {
        $parameters = $this->getMock('Oro\Bundle\DataGridBundle\Datagrid\ParameterBag');

        $parameters->expects($this->any())
            ->method('has')
            ->will(
                $this->returnCallback(
                    function ($key) use ($data) {
                        return isset($data[$key]);
                    }
                )
            );

        $parameters->expects($this->any())
            ->method('get')
            ->will(
                $this->returnCallback(
                    function ($key) use ($data) {
                        return $data[$key];
                    }
                )
            );

        return $parameters;
    }
}
