<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventSignup
 *
 * @ORM\Table(name="event_signup")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventSignupRepository")
 */
class EventSignup
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
     * @var WowCharacter
     *
     * @ORM\ManyToOne(targetEntity="WowCharacter", inversedBy="signups")
     * @ORM\JoinColumn(name="wow_character_id", referencedColumnName="id")
     */
    private $wowCharacter;

    /**
     * @var Event
     *
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="signups")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id")
     */
    private $event;


    /**
     * @var integer
     *
     * @ORM\Column(name="roles", type="integer")
     */
    private $roles;

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
     * Set wowCharacter
     *
     * @param \AppBundle\Entity\WowCharacter $wowCharacter
     *
     * @return EventSignup
     */
    public function setWowCharacter(\AppBundle\Entity\WowCharacter $wowCharacter = null)
    {
        $this->wowCharacter = $wowCharacter;

        return $this;
    }

    /**
     * Get wowCharacter
     *
     * @return \AppBundle\Entity\WowCharacter
     */
    public function getWowCharacter()
    {
        return $this->wowCharacter;
    }

    /**
     * Set event
     *
     * @param \AppBundle\Entity\Event $event
     *
     * @return EventSignup
     */
    public function setEvent(\AppBundle\Entity\Event $event = null)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return \AppBundle\Entity\Event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set roles
     *
     * @param integer $roles
     *
     * @return EventSignup
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get roles
     *
     * @return integer
     */
    public function getRoles()
    {
        return $this->roles;
    }
}
