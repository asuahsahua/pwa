<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
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
    const ROLE_TANK = 1;
    const ROLE_HEAL = 2;
    const ROLE_DPS = 4;

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
     * @ORM\Column(name="character_name", type="string", length=255)
     */
    private $characterName;

    /**
     * @var string
     *
     * @ORM\Column(name="server", type="string", length=255)
     */
    private $server;

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
     * @param \AppBundle\Entity\User $user
     *
     * @return WowCharacter
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
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
     * @return array
     */
    public function getRoles() : array
    {
        $roles = [];
        foreach (self::getRoleTypes() as $roleType) {
            if ($this->getRolesMask() & $roleType) {
                $roles []= $roleType;
            }
        }
        return $roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles)
    {
        $mask = 0;
        foreach ($roles as $role) {
            $mask |= $role;
        }
        $this->setRolesMask($mask);
    }

    public static function getRoleTypes()
    {
        return [
            self::ROLE_TANK,
            self::ROLE_HEAL,
            self::ROLE_DPS,
        ];
    }

    /**
     * @return bool
     */
    public function getIsTank()
    {
        return !!($this->getRolesMask() & self::ROLE_TANK);
    }

    public function setIsTank($bool)
    {
        if ($bool) {
            $this->setRolesMask($this->getRolesMask() | self::ROLE_TANK);
        } else {
            $this->setRolesMask($this->getRolesMask() ^ self::ROLE_TANK);
        }
    }

    /**
     * @return bool
     */
    public function isDps()
    {
        return !!($this->getRolesMask() & self::ROLE_DPS);
    }

    public function setIsDps($bool)
    {
        if ($bool) {
            $this->setRolesMask($this->getRolesMask() | self::ROLE_DPS);
        } else {
            $this->setRolesMask($this->getRolesMask() ^ self::ROLE_DPS);
        }
    }

    /**
     * @return bool
     */
    public function isHeal()
    {
        return !!($this->getRolesMask() & self::ROLE_HEAL);
    }

    public function setIsHeal($bool)
    {
        if ($bool) {
            $this->setRolesMask($this->getRolesMask() | self::ROLE_HEAL);
        } else {
            $this->setRolesMask($this->getRolesMask() ^ self::ROLE_HEAL);
        }
    }
}
