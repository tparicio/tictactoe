<?php
// src/App/Extension/TwigExtension.php
namespace App\Extension;

use Doctrine\ORM\EntityManager;
use App\Entity\Lang;

class TwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * add some variables to twig as global variables
     *
     * @return array
     */
    public function getGlobals() {
        return array(
            'langs' => $this->entityManager->getRepository(Lang::class)->findAll()
        );
    }
}
