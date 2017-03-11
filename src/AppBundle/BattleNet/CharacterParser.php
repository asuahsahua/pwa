<?php

namespace AppBundle\BattleNet;

use Psr\Http\Message\ResponseInterface;

class CharacterParser
{
    /** @var ResponseInterface */
    protected $_response;
    protected $parsed = [];

    public function __construct(ResponseInterface $response)
    {
        $this->_response = $response;

        if ($this->valid()) {
            $this->parsed = json_decode($response->getBody());
        }
    }

    public function valid()
    {
        return $this->_response->getStatusCode() == 200;
    }

    public function getClass()
    {
        static $classMapping = [
            1  => "Warrior",
            2  => "Paladin",
            3  => "Hunter",
            4  => "Rogue",
            5  => "Priest",
            6  => "Death Knight",
            7  => "Shaman",
            8  => "Mage",
            9  => "Warlock",
            10 => "Monk",
            11 => "Druid",
            12 => "Demon Hunter",
        ];

        return $classMapping[$this->parsed->class];
    }

    public function getRace()
    {
        static $raceMapping = [
            1  => "Human",
            2  => "Orc",
            3  => "Dwarf",
            4  => "Night Elf",
            5  => "Undead",
            6  => "Tauren",
            7  => "Gnome",
            8  => "Troll",
            9  => "Goblin",
            10 => "Blood Elf",
            11 => "Draenei",
            22 => "Worgen",
            24 => "Pandaren",
            25 => "Pandaren",
            26 => "Pandaren",
        ];

        return $raceMapping[$this->parsed->race];
    }

    public function getThumbnail()
    {
        if ($this->valid()) {
            return "http://render-us.worldofwarcraft.com/character/{$this->parsed->thumbnail}";
        } else {
            return false;
        }
    }

    public function getName()
    {
        return $this->parsed->name;
    }

    public function getRealm()
    {
        return $this->parsed->realm;
    }

    public function getGender()
    {
        static $genderMapping = [
            1 => "Female",
            2 => "Male",
        ];

        return $genderMapping[$this->parsed->gender];
    }

    public function getLevel()
    {
        return $this->parsed->level;
    }

    public function getFaction()
    {
        static $factionMapping = [
            1 => "Horde",
            2 => "Alliance",
        ];

        return $factionMapping[$this->parsed->faction];
    }

}