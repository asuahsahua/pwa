<?php

namespace AppBundle\Enums;

class Roles
{
    const ROLE_TANK = 1;
    const ROLE_HEAL = 2;
    const ROLE_DPS = 4;

    protected static $descriptions = [
        self::ROLE_TANK => "Tank",
        self::ROLE_DPS  => "DPS",
        self::ROLE_HEAL => "Heal",
    ];

    public static function getTypes()
    {
        return [
            self::ROLE_TANK,
            self::ROLE_HEAL,
            self::ROLE_DPS,
        ];
    }

    /** @var int */
    protected $mask;

    public function __construct($mask = 0)
    {
        $this->mask = $mask;
    }

    /**
     * @return bool
     */
    public function getIsTank()
    {
        return $this->get(self::ROLE_TANK);
    }

    /**
     * @param int $role
     * @return bool
     */
    protected function get($role)
    {
        return !!($this->mask & $role);
    }

    public function setIsTank($bool)
    {
        $this->set($bool, self::ROLE_TANK);
    }

    protected function set($bool, $role)
    {
        if ($bool) {
            $this->mask |= $role;
        } else {
            $this->mask ^= $role;
        }
    }

    /**
     * @return bool
     */
    public function isDps()
    {
        return $this->get(self::ROLE_DPS);
    }

    public function setIsDps($bool)
    {
        $this->set($bool, self::ROLE_DPS);
    }

    /**
     * @return bool
     */
    public function isHeal()
    {
        return $this->get(self::ROLE_HEAL);
    }

    public function setIsHeal($bool)
    {
        $this->set($bool, self::ROLE_HEAL);
    }

    public function expandRoles()
    {
        $roles = [];
        foreach (self::getTypes() as $type) {
            if ($this->get($type)) {
                $roles [] = $type;
            }
        }
        return $roles;
    }

    public function getRolesDescriptive()
    {
        return \array_map(function ($role) {
            return self::$descriptions[$role];
        }, $this->expandRoles());
    }

    public function getMask()
    {
        return $this->mask;
    }
}