<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Event
 *
 * @ORM\Table(name="event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 */
class Event
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_time", type="datetimetz")
     * @Assert\GreaterThanOrEqual("now", message="Start time must be in the future")
     */
    private $startTime;

    /**
     * @var int
     *
     * @ORM\Column(name="duration_minutes", type="integer")
     */
    private $durationMinutes;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="oraganizer", referencedColumnName="id")
     */
    private $organizer;

    /**
     * @var int
     *
     * @ORM\Column(name="slots", type="integer")
     */
    private $slots;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Event
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return Event
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set durationMinutes
     *
     * @param integer $durationMinutes
     *
     * @return Event
     */
    public function setDurationMinutes($durationMinutes)
    {
        $this->durationMinutes = $durationMinutes;

        return $this;
    }

    /**
     * Get durationMinutes
     *
     * @return int
     */
    public function getDurationMinutes()
    {
        return $this->durationMinutes;
    }

    /**
     * Set organizer
     *
     * @param User $organizer
     *
     * @return Event
     */
    public function setOrganizer($organizer)
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * Get organizer
     *
     * @return User
     */
    public function getOrganizer()
    {
        return $this->organizer;
    }

    /**
     * Set slots
     *
     * @param integer $slots
     *
     * @return Event
     */
    public function setSlots($slots)
    {
        $this->slots = $slots;

        return $this;
    }

    /**
     * Get slots
     *
     * @return int
     */
    public function getSlots()
    {
        return $this->slots;
    }

    public function getDurationInterval()
    {
        $hours = (int)($this->durationMinutes / 60);
        $minutes = $this->durationMinutes % 60;

        return new \DateInterval("PT{$hours}H{$minutes}M");
    }

    public function setDurationInterval(\DateInterval $interval)
    {
        $this->durationMinutes = $interval->i;
    }
}
