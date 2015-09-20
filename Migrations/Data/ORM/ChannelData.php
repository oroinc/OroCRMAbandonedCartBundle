<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Migrations\Data\ORM;

use Doctrine\Common\Persistence\ObjectManager;

use OroCRM\Bundle\ChannelBundle\Entity\Channel;
use OroCRM\Bundle\ChannelBundle\Migrations\Data\ORM\AbstractDefaultChannelDataFixture;

class ChannelData extends AbstractDefaultChannelDataFixture
{
    const PREFERABLE_CHANNEL_TYPE = 'magento';

    /**
     * {@inheritdoc}
     */
    public function load(ObjectManager $manager)
    {
        $entity = 'OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign';

        /** @var Channel|null $channel */
        $channel = $this->em->getRepository('OroCRMChannelBundle:Channel')
            ->findOneBy(['channelType' => self::PREFERABLE_CHANNEL_TYPE]);

        if (!$channel) {
            $builder = $this->container->get('orocrm_channel.builder.factory')->createBuilder();
        } else {
            $builder = $this->container->get('orocrm_channel.builder.factory')->createBuilderForChannel($channel);
        }

        $builder->setStatus(Channel::STATUS_ACTIVE);
        $builder->addEntity($entity);

        $channel = $builder->getChannel();


        $this->em->persist($channel);
        $this->em->flush();

    }
}
