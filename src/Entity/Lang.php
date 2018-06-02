<?php
// src/Entity/Lang.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity for database table games
 *
 * @ORM\Entity
 * @ORM\Table(name="langs")
 * @ORM\Entity(repositoryClass="App\Repository\LangRepository")
 */
class Lang
{
    /**
     * @ORM\Column(type="string")
     * @ORM\Id
     * @var string
     */
    private $iso;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $name;

    /**
     * Get the value of Iso
     *
     * @return string
     */
    public function getIso()
    {
        return $this->iso;
    }

    /**
     * Set the value of Iso
     *
     * @param string iso
     *
     * @return self
     */
    public function setIso($iso)
    {
        $this->iso = $iso;

        return $this;
    }

    /**
     * Get the value of Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set the value of Name
     *
     * @param string name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

}
