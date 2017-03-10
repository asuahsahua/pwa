<?php

namespace AppBundle\Entity;

use AppBundle\BattleNet\CharacterParser;
use AppBundle\Enums\Roles;
use AppBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Psr\Http\Message\ResponseInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * WowCharacter
 *
 * @ORM\Table(name="wow_character", uniqueConstraints={ @ORM\UniqueConstraint(name="name_and_server", columns={"character_name", "server"}) } )
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WowCharacterRepository")
 * @UniqueEntity({"characterName", "server"})
 */
class WowCharacter
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
     * @ORM\Column(name="character_name", type="string", length=100)
     */
    private $characterName;

    /**
     * @var string
     *
     * @ORM\Column(name="server", type="string", length=100)
     */
    private $server;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="faction", type="string", length=100)
	 */
	private $faction;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="class", type="string", length=100)
	 */
	private $class;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="level", type="string", length=100)
	 */
	private $level;

	/**
	 * @var string
	 *
	 * @ORM\Column(name="thumbnail", type="string", length=255)
	 */
	private $thumbnail;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="characters")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var int
     *
     * @ORM\Column(name="roles_mask", type="integer")
     */
    private $roles_mask = 0;

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
     * Set characterName
     *
     * @param string $characterName
     *
     * @return WowCharacter
     */
    public function setCharacterName($characterName)
    {
        $this->characterName = $characterName;

        return $this;
    }

    /**
     * Get characterName
     *
     * @return string
     */
    public function getCharacterName()
    {
        return $this->characterName;
    }

    /**
     * Set server
     *
     * @param string $server
     *
     * @return WowCharacter
     */
    public function setServer($server)
    {
        $this->server = $server;

        return $this;
    }

    /**
     * Get server
     *
     * @return string
     */
    public function getServer()
    {
        return $this->server;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return WowCharacter
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    public function getDisplayName() : string
    {
        return $this->getCharacterName() . '-' . $this->getServer();
    }

    /**
     * Set rolesMask
     *
     * @param integer $rolesMask
     *
     * @return WowCharacter
     */
    public function setRolesMask($rolesMask)
    {
        $this->roles_mask = $rolesMask;

        return $this;
    }

    /**
     * Get rolesMask
     *
     * @return integer
     */
    public function getRolesMask()
    {
        return $this->roles_mask;
    }

    /**
     * @return Roles
     */
    public function getRoles() : Roles
    {
    	return new Roles($this->getRolesMask());
    }

    /**
     * @param Roles $roles
     */
    public function setRoles(Roles $roles)
    {
    	$this->setRolesMask($roles->getMask());
    }

	/**
	 * @param $response
	 */
    public function setFieldsFromBattlnetResponse(ResponseInterface $response)
    {
    	$parser = new CharacterParser($response);
    	if ($parser->valid()) {
    		$this->setFaction($parser->getFaction());
    		$this->setLevel($parser->getLevel());
    		$this->setClass($parser->getClass());
    		$this->setThumbnail($parser->getThumbnail());
	    }
    }

    /**
     * Set faction
     *
     * @param string $faction
     *
     * @return WowCharacter
     */
    public function setFaction($faction)
    {
        $this->faction = $faction;

        return $this;
    }

    /**
     * Get faction
     *
     * @return string
     */
    public function getFaction()
    {
        return $this->faction;
    }

    /**
     * Set class
     *
     * @param string $class
     *
     * @return WowCharacter
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Set level
     *
     * @param string $level
     *
     * @return WowCharacter
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set thumbnail
     *
     * @param string $thumbnail
     *
     * @return WowCharacter
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     *
     * @return string
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }
}
