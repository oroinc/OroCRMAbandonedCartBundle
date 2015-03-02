<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Oro\Bundle\SegmentBundle\Entity\Segment;
use Oro\Bundle\UserBundle\Entity\User;

/**
 * Abandoned Cart List
 *
 * @ORM\Table(name="orocrm_abandoned_cart_list")
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class AbandonedCartList
{
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
     * Retrieves description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Retrieves Segment
     *
     * @return Segment
     */
    public function getSegment()
    {
        return $this->segment;
    }

    /**
     * Retrieves owner, User object
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
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
}
