<?php

namespace OroCRM\Bundle\AbandonedCartBundle\EventListener;

use Doctrine\Common\Persistence\ManagerRegistry;
use Oro\Bundle\NavigationBundle\Event\ConfigureMenuEvent;
use OroCRM\Bundle\MagentoBundle\Provider\ChannelType;

class NavigationListener
{
    /**
     * @var ManagerRegistry
     */
    protected $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param ConfigureMenuEvent $event
     */
    public function onNavigationConfigure(ConfigureMenuEvent $event)
    {
        $enableMagentoChannel = $this->registry
            ->getRepository('OroIntegrationBundle:Channel')
            ->findOneBy(['type' => ChannelType::TYPE, 'enabled' => true]);

        $menu = $event->getMenu();

        if (!$enableMagentoChannel) {
            $menu->getChild('marketing_tab')->removeChild('abandoned_cart_list');
        }
    }
}
