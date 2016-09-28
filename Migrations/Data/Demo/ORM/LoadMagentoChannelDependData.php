<?php

namespace Oro\Bundle\AbandonedCartBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\Persistence\ObjectManager;

use Oro\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use Oro\Bundle\ChannelBundle\Entity\Channel;
use Oro\Bundle\ChannelBundle\Migrations\Data\ORM\AbstractDefaultChannelDataFixture;
use Oro\Bundle\MagentoBundle\Provider\ChannelType;

class LoadMagentoChannelDependData extends AbstractDefaultChannelDataFixture
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\LoadOrganizationAndBusinessUnitData',
            'Oro\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM\LoadMagentoData'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var Channel|null $channel */
        $channels = $this->em->getRepository('OroChannelBundle:Channel')
            ->findBy(['channelType' => ChannelType::TYPE]);

        if ($channels) {
            foreach ($channels as $channel) {
                $builder = $this->container->get('oro_channel.builder.factory')->createBuilderForChannel($channel);
                $builder->addEntity(AbandonedCartCampaign::CLASS_NAME);
                $channel = $builder->getChannel();
                $this->em->persist($channel);
            }
            $this->em->flush();
        }
    }
}
