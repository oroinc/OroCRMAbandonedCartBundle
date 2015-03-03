<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Entity;

use Oro\Bundle\EntityConfigBundle\Metadata\Annotation\Config;
use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\OrganizationBundle\Entity\BusinessUnit;
use Oro\Bundle\OrganizationBundle\Entity\Organization;
use Oro\Bundle\SegmentBundle\Entity\Segment;
use Oro\Bundle\UserBundle\Entity\User;
use OroCRM\Bundle\AbandonedCartBundle\Model\ExtendAbandonedCartList;

/**
 * Abandoned Cart List
 *
 * @ORM\Table(name="orocrm_abandoned_cart_list")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @Config(
 *      routeName="orocrm_abandoned_cart_list",
 *      defaultValues={
 *          "entity"={
 *              "icon"="icon-shopping-cart"
 *          },
 *          "ownership"={
 *              "owner_type"="USER",
 *              "owner_field_name"="owner",
 *              "owner_column_name"="owner_id",
 *              "organization_field_name"="organization",
 *              "organization_column_name"="organization_id"
 *          },
 *          "security"={
 *              "type"="ACL",
 *              "group_name"=""
 *          }
 *      }
 * )
 */
class AbandonedCartList extends ExtendAbandonedCartList
{
    const ENTITY_FULL_NAME = 'OroCRM\Bundle\AbandonedCartBundle\Entity\AbandonedCartList';

    /**
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, length=255, nullable=false)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var Segment
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\SegmentBundle\Entity\Segment", cascade={"all"})
     * @ORM\JoinColumn(name="segment_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $segment;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $owner;

    /**
     * @var Organization
     *
     * @ORM\ManyToOne(targetEntity="Oro\Bundle\OrganizationBundle\Entity\Organization")
     * @ORM\JoinColumn(name="organization_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $organization;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->updatedAt = clone $this->createdAt;
    }

    /**
     * Retrieves entity identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retrieves name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return AbandonedCartList
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Retrieves description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AbandonedCartList
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Retrieves segment
     *
     * @return Segment
     */
    public function getSegment()
    {
        return $this->segment;
    }

    public function updateSegment($name, $definition)
    {
        if (is_null($this->segment)) {
            throw new \RuntimeException('The segment does not exist in the AbandonedCartList entity.');
        }
        $this->segment->setName($name);
        $this->segment->setDefinition($definition);
        $this->segment->setOwner($this->getOwnerBusinessUnit());
    }

    /**
     * Set segment
     *
     * @param Segment $segment
     * @return AbandonedCartList
     */
    public function setSegment(Segment $segment)
    {
        $this->segment = $segment;
        return $this;
    }

    /**
     * Retrieves owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    public function setOwner(User $owner)
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * Retrieves owner's business unit
     *
     * @return BusinessUnit
     */
    public function getOwnerBusinessUnit()
    {
        if (is_null($this->owner)) {
            return null;
        }
        return $this->owner->getOwner();
    }

    /**
     * Retrieves organization
     *
     * @return Organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set organization
     *
     * @param Organization $organization
     * @return AbandonedCartList
     */
    public function setOrganization(Organization $organization = null)
    {
        $this->organization = $organization;

        return $this;
    }

    /**
     * Retrieves created at Date and Time
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Retrieves updated at Date and Time
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Get this segment definition in YAML format
     *
     * @return string
     */
    public function getDefinition()
    {
        if ($this->segment) {
            return $this->segment->getDefinition();
        }

        return null;
    }

    /**
     * Set this segment definition in YAML format
     *
     * @param string $definition
     */
    public function setDefinition($definition)
    {
        if ($this->segment) {
            $this->segment->setDefinition($definition);
        }
    }

    /**
     * Get the full name of an entity
     *
     * @return string
     */
    public function getEntity()
    {
        return self::ENTITY_FULL_NAME;
    }
}
