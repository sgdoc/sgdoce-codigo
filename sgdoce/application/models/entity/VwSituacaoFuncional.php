<?php

namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sgdoce\Model\Entity\VwSituacaoFuncional
 *
 * @ORM\Table(name="vw_situacao_funcional")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwSituacaoFuncional")
 */
class VwSituacaoFuncional extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqSituacaoFuncional
     *
     * @ORM\Id
     * @ORM\Column(name="sq_situacao_funcional", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqSituacaoFuncional;

    /**
     * @var string $noSituacaoFuncional
     *
     * @ORM\Column(name="no_situacao_funcional", type="string", length=50, nullable=false)
     */
    private $noSituacaoFuncional;

    /**
     * Get sqSituacaoFuncional
     *
     * @return integer
     */
    public function getSqSituacaoFuncional()
    {
        return $this->sqSituacaoFuncional;
    }

    /**
     * Set noSituacaoFuncional
     *
     * @param string $noSituacaoFuncional
     * @return SituacaoFuncional
     */
    public function setNoSituacaoFuncional($noSituacaoFuncional)
    {
        $this->noSituacaoFuncional = $noSituacaoFuncional;
        return $this;
    }

    /**
     * Get noSituacaoFuncional
     *
     * @return string
     */
    public function getNoSituacaoFuncional()
    {
        return $this->noSituacaoFuncional;
    }
}