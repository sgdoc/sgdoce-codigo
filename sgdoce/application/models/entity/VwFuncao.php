<?php

namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sgdoce\Model\Entity\Funcao
 *
 * @ORM\Table(name="vw_funcao")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\Funcao")
 */
class VwFuncao extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqFuncao
     *
     * @ORM\Id
     * @ORM\Column(name="sq_funcao", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqFuncao;

    /**
     * @var string $noFuncao
     *
     * @ORM\Column(name="no_funcao", type="string", length=50, nullable=false)
     */
    private $noFuncao;

    /**
     * Get sqFuncao
     *
     * @return integer
     */
    public function getSqFuncao()
    {
        return $this->sqFuncao;
    }

    /**
     * Set sqFuncao
     *
     * @param integer $sqFuncao
     * @return void
     */
    public function setSqFuncao($sqFuncao)
    {
        $this->sqFuncao = $sqFuncao;
        return $this;
    }

    /**
     * Get noFuncao
     *
     * @return string
     */
    public function getNoFuncao()
    {
        return $this->noFuncao;
    }

    /**
     * Set noFuncao
     *
     * @param string $noFuncao
     * @return Funcao
     */
    public function setNoFuncao($noFuncao)
    {
        $this->noFuncao = $noFuncao;
        return $this;
    }

    public function setSqFuncaoAssinatura($sqFuncaoAssinatura)
    {
        $this->sqFuncao = $sqFuncaoAssinatura;
        return $this;
    }
}