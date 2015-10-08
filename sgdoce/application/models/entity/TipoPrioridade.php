<?php
/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */
namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sgdoce\Model\Entity\TipoPrioridade
 *
 * @ORM\Table(name="tipo_prioridade")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\TipoPrioridade")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoPrioridade extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoPrioridade
     *
     * @ORM\Column(name="sq_tipo_prioridade", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoPrioridade;

    /**
     * @var string $txTipoPrioridade
     *
     * @ORM\Column(name="tx_tipo_prioridade", type="string", length=200, nullable=false)
     */
    private $txTipoPrioridade;

    /**
     * @var Sgdoce\Model\Entity\Prioridade
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Prioridade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_prioridade", referencedColumnName="sq_prioridade")
     * })
     */
    private $sqPrioridade;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * Get sqTipoPrioridade
     *
     * @return integer
     */
    public function setSqTipoPrioridade($sqTipoPrioridade)
    {
        $this->sqTipoPrioridade = $sqTipoPrioridade;
        return $this;
    }

    /**
     * Get sqTipoPrioridade
     *
     * @return integer
     */
    public function getSqTipoPrioridade()
    {
        return $this->sqTipoPrioridade;
    }

    /**
     * Set txTipoPrioridade
     *
     * @param string $txTipoPrioridade
     * @return TipoPrioridade
     */
    public function setTxTipoPrioridade($txTipoPrioridade)
    {
        $this->txTipoPrioridade = $txTipoPrioridade;
        return $this;
    }

    /**
     * Get txTipoPrioridade
     *
     * @return string
     */
    public function getTxTipoPrioridade()
    {
        return $this->txTipoPrioridade;
    }

    /**
     * Set sqPrioridade
     *
     * @param Sgdoce\Model\Entity\Prioridade $sqPrioridade
     * @return TipoPrioridade
     */
    public function setSqPrioridade(\Sgdoce\Model\Entity\Prioridade $sqPrioridade = NULL)
    {
        $this->sqPrioridade = $sqPrioridade;
        return $this;
    }

    /**
     * Get sqPrioridade
     *
     * @return Sgdoce\Model\Entity\Prioridade
     */
    public function getSqPrioridade()
    {
        return $this->sqPrioridade ? $this->sqPrioridade : new Prioridade();
    }

    /**
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return TipoPrioridade
     */
    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * Get stRegistroAtivo
     *
     * @return boolean
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }
}