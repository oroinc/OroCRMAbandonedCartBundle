<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Migrations\Data\Demo\ORM;

use Doctrine\Common\Persistence\ObjectManager;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\ChannelBundle\Entity\Channel;
use OroCRM\Bundle\ChannelBundle\Migrations\Data\ORM\AbstractDefaultChannelDataFixture;
use OroCRM\Bundle\MagentoBundle\Provider\ChannelType;

class LoadMagentoChannelDependData extends AbstractDefaultChannelDataFixture
{
    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
            'Oro\Bundle\OrganizationBundle\Migrations\Data\ORM\LoadOrganizationAndBusinessUnitData',
            'OroCRM\Bundle\DemoDataBundle\Migrations\Data\Demo\ORM\LoadMagentoData'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var Channel|null $channel */
        $channels = $this->em->getRepository('OroCRMChannelBundle:Channel')
            ->findBy(['channelType' => ChannelType::TYPE]);

        if ($channels) {
            foreach ($channels as $channel) {
                $builder = $this->container->get('orocrm_channel.builder.factory')->createBuilderForChannel($channel);
                $builder->addEntity(AbandonedCartCampaign::CLASS_NAME);
                $channel = $builder->getChannel();
                $this->em->persist($channel);
            }
            $this->em->flush();
        }
    }
}
