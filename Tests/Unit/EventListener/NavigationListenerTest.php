<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Tests\Unit\EventListener;

use Knp\Menu\MenuItem;
use Knp\Menu\MenuFactory;

use Oro\Bundle\NavigationBundle\Event\ConfigureMenuEvent;

use OroCRM\Bundle\AbandonedCartBundle\EventListener\NavigationListener;
use OroCRM\Bundle\MagentoBundle\Provider\ChannelType;

class NavigationListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var NavigationListener
     */
    protected $navigationListener;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $registry;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $repository;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $event;

    protected function setUp()
    {
        $this->registry = $this->getMockBuilder('Doctrine\Common\Persistence\ManagerRegistry')
            ->disableOriginalConstructor()
            ->getMock();

        $this->repository = $this->getMockBuilder('Doctrine\ORM\EntityRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->event = $this->getMockBuilder('Symfony\Component\EventDispatcher\Event')
            ->disableOriginalConstructor()
            ->getMock();

        $this->navigationListener = new NavigationListener($this->registry);
    }

    public function testOnNavigationConfigureWhenNoMagentoActiveChannel()
    {
        $this->registry
            ->expects($this->once())->method('getRepository')
            ->with('OroIntegrationBundle:Channel')
            ->will($this->returnValue($this->repository));

        $this->repository
            ->expects($this->once())->method('findOneBy')
            ->with(['type' => ChannelType::TYPE, 'enabled' => true])
            ->will($this->returnValue(true));

        $factory   = new MenuFactory();
        $menu      = new MenuItem('test_menu', $factory);
        $eventData = new ConfigureMenuEvent($factory, $menu);

        $this->navigationListener->onNavigationConfigure($eventData);
    }

    public function testOnNavigationConfigureWhenExistMagentoActiveChannel()
    {
        $this->registry
            ->expects($this->once())->method('getRepository')
            ->with('OroIntegrationBundle:Channel')
            ->will($this->returnValue($this->repository));

        $this->repository
            ->expects($this->once())->method('findOneBy')
            ->with(['type' => ChannelType::TYPE, 'enabled' => true])
            ->will($this->returnValue(null));

        $factory   = new MenuFactory();
        $menu      = new MenuItem('test', $factory);
        $menu->addChild('marketing_tab');
        $eventData = new ConfigureMenuEvent($factory, $menu);

        $this->navigationListener->onNavigationConfigure($eventData);
    }
}
