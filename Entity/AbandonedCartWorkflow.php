<?php

namespace OroCRM\Bundle\AbandonedCartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use OroCRM\Bundle\MailChimpBundle\Entity\StaticSegment;

/**
 * Abandoned Cart Workflow
 *
 * @ORM\Table(name="orocrm_abandcart_workflow")
 * @ORM\Entity()
 */
class AbandonedCartWorkflow
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var StaticSegment
     *
     * @ORM\ManyToOne(targetEntity="OroCRM\Bundle\MailChimpBundle\Entity\StaticSegment")
     * @ORM\JoinColumn(name="static_segment_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $staticSegment;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="text", nullable=false)
     */
    protected $name;

    /**
     * @var int
     *
     * @ORM\Column(name="emails_sent", type="integer", nullable=true)
     */
    protected $emailsSent;

    /**
     * @var int
     *
     * @ORM\Column(name="opens", type="integer", nullable=true)
     */
    protected $opens;

    /**
     * @var int
     *
     * @ORM\Column(name="clicks", type="integer", nullable=true)
     */
    protected $clicks;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getClicks()
    {
        return $this->clicks;
    }

    /**
     * @param $clicks
     * @return $this
     */
    public function setClicks($clicks)
    {
        $this->clicks = $clicks;
        return $this;
    }

    /**
     * @return int
     */
    public function getEmailsSent()
    {
        return $this->emailsSent;
    }

    /**
     * @param int $emailsSent
     * @return $this
     */
    public function setEmailsSent($emailsSent)
    {
        $this->emailsSent = $emailsSent;
        return $this;
    }

    /**
     * @return int
     */
    public function getOpens()
    {
        return $this->opens;
    }

    /**
     * @param int $opens
     * @return $this
     */
    public function setOpens($opens)
    {
        $this->opens = $opens;
        return $this;
    }

    /**
     * @return StaticSegment
     */
    public function getStaticSegment()
    {
        return $this->staticSegment;
    }

    /**
     * @param StaticSegment $segment
     * @return $this
     */
    public function setStaticSegment(StaticSegment $segment = null)
    {
        $this->staticSegment = $segment;
        return $this;
    }
}
