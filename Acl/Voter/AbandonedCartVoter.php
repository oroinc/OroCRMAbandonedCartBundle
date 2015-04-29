<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Acl\Voter;

use Oro\Bundle\SecurityBundle\Acl\Voter\AbstractEntityVoter;
use Oro\Bundle\EntityBundle\ORM\DoctrineHelper;

use OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartCampaign;
use OroCRM\Bundle\MagentoBundle\Provider\ChannelType;

class AbandonedCartVoter extends AbstractEntityVoter
{
    const ATTRIBUTE_VIEW   = 'VIEW';
    const ATTRIBUTE_CREATE = 'CREATE';
    const ATTRIBUTE_EDIT   = 'EDIT';
    const ATTRIBUTE_DELETE = 'DELETE';

    /**
     * @var AbandonedCartCampaign
     */
    protected $object;

    /**
     * @var string
     */
    protected $integrationChannelClassName;

    /**
     * @var array
     */
    protected $supportedAttributes = [
        self::ATTRIBUTE_VIEW,
        self::ATTRIBUTE_CREATE,
        self::ATTRIBUTE_EDIT,
        self::ATTRIBUTE_DELETE
    ];

    /**
     * @param DoctrineHelper $doctrineHelper
     * @param string $integrationChannelClassName
     */
    public function __construct(DoctrineHelper $doctrineHelper, $integrationChannelClassName)
    {
        $this->integrationChannelClassName = $integrationChannelClassName;
        parent::__construct($doctrineHelper);
    }

    /**
     * {@inheritdoc}
     */
    protected function getPermissionForAttribute($class, $identifier, $attribute)
    {
        $enabledMagentoChannel = $this->doctrineHelper
            ->getEntityRepository($this->integrationChannelClassName)
            ->findOneBy(['type' => ChannelType::TYPE, 'enabled' => true]);

        if (!$enabledMagentoChannel) {
            return self::ACCESS_DENIED;
        }

        return self::ACCESS_ABSTAIN;
    }

    /**
     * {@inheritdoc}
     */
    protected function getEntityIdentifier($object)
    {
        $identifier = parent::getEntityIdentifier($object);

        // create actions does not contain identifier
        if (!$identifier) {
            return false;
        }

        return $identifier;
    }
}
