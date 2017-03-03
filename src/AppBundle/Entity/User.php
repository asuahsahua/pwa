<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 *
 */
class User implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="discord_username", type="string")
     */
    protected $discordUsername;

    /**
     * @var int
     *
     * @ORM\Column(name="discriminator", type="integer")
     */
    protected $discriminator;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255)
     */
    protected $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=true)
     */
    protected $password;

    /**
     * @var string
     *
     * @ORM\Column(name="salt", type="string", length=255, nullable=true)
     */
    protected $salt;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar_id", type="string", length=255)
     */
    protected $avatar_id;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="timezone", type="string", length=255)
	 */
	protected $timezone = 'America/New_York';

    /**
     * @var WowCharacter[]
     *
     * @ORM\OneToMany(targetEntity="WowCharacter", mappedBy="user")
     * @ORM\JoinColumn(name="id", referencedColumnName="user_id")
     */
    private $characters;

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
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Returns the roles granted to the user.
     *
     * <code>
     * public function getRoles()
     * {
     *     return array('ROLE_USER');
     * }
     * </code>
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return (Role|string)[] The user roles
     */
    public function getRoles()
    {
    	$roles = [];

    	if ($this->getUsername()) {
    		$roles []= 'ROLE_USER';
	    }

        return $roles;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->characters = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set discordUsername
     *
     * @param string $discordUsername
     *
     * @return User
     */
    public function setDiscordUsername($discordUsername)
    {
        $this->discordUsername = $discordUsername;

        return $this;
    }

    /**
     * Get discordUsername
     *
     * @return string
     */
    public function getDiscordUsername()
    {
        return $this->discordUsername;
    }

    /**
     * Set discriminator
     *
     * @param integer $discriminator
     *
     * @return User
     */
    public function setDiscriminator($discriminator)
    {
        $this->discriminator = $discriminator;

        return $this;
    }

    /**
     * Get discriminator
     *
     * @return integer
     */
    public function getDiscriminator()
    {
        return $this->discriminator;
    }

    /**
     * Set avatarId
     *
     * @param integer $avatarId
     *
     * @return User
     */
    public function setAvatarId($avatarId)
    {
        $this->avatar_id = $avatarId;

        return $this;
    }

    /**
     * Get avatarId
     *
     * @return integer
     */
    public function getAvatarId()
    {
        return $this->avatar_id;
    }

    /**
     * Add character
     *
     * @param \AppBundle\Entity\WowCharacter $character
     *
     * @return User
     */
    public function addCharacter(\AppBundle\Entity\WowCharacter $character)
    {
        $this->characters[] = $character;

        return $this;
    }

    /**
     * Remove character
     *
     * @param \AppBundle\Entity\WowCharacter $character
     */
    public function removeCharacter(\AppBundle\Entity\WowCharacter $character)
    {
        $this->characters->removeElement($character);
    }

    /**
     * Get characters
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCharacters()
    {
        return $this->characters;
    }

    /**
     * @return string
     */
    public function getFullDiscordUsername()
    {
        return $this->discordUsername . "#" . $this->discriminator;
    }

    public function getAvatarUrl()
    {
        return "https://cdn.discordapp.com/avatars/{$this->getUsername()}/{$this->getAvatarId()}.png?size=256";
    }

    /**
     * Set timezone
     *
     * @param string $timezone
     *
     * @return User
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get timezone
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }
}
