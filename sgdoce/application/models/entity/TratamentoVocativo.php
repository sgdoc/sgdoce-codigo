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
 * Sgdoce\Model\Entity\TratamentoVocativo
 *
 * @ORM\Table(name="tratamento_vocativo")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\TratamentoVocativo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TratamentoVocativo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTratamentoVocativo
     *
     * @ORM\Column(name="sq_tratamento_vocativo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTratamentoVocativo;

    /**
     * @var Sgdoce\Model\Entity\Tratamento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Tratamento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tratamento", referencedColumnName="sq_tratamento")
     * })
     */
    private $sqTratamento;

    /**
     * @var Sgdoce\Model\Entity\Vocativo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Vocativo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_vocativo", referencedColumnName="sq_vocativo", nullable=true)
     * })
     */
    private $sqVocativo;

    /**
     * @var string $deEnderecamento
     *
     * @ORM\Column(name="de_enderecamento", type="string", length=300, nullable=true)
     */
    private $deEnderecamento;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=true)
     */
    private $stRegistroAtivo;


    /**
     * Set sqTratamentoVocativo
     *
     * @param integer $sqTratamentoVocativo
     * @return integer
     */
    public function setSqTratamentoVocativo($sqTratamentoVocativo = NULL)
    {
        $this->sqTratamentoVocativo = $sqTratamentoVocativo;
        if(!$sqTratamentoVocativo){
            $this->sqTratamentoVocativo  = NULL;
        }
        return $this;
    }

    /**
     * Get sqTratamento
     *
     * @return integer
     */
    public function getSqTratamentoVocativo()
    {
        return $this->sqTratamentoVocativo;
    }
    /**
     * Set sqTratamento
     *
     * @param integer $sqTratamento
     * @return integer
     */
    public function setSqTratamento($sqTratamento = NULL)
    {
        $this->sqTratamento = $sqTratamento;
        return $this;
    }

    /**
     * Get sqTratamento
     *
     * @return integer
     */
    public function getSqTratamento()
    {
        return $this->sqTratamento ? $this->sqTratamento : new \Sgdoce\Model\Entity\Tratamento();
    }

    public function getSqVocativo()
    {
        return $this->sqVocativo ? $this->sqVocativo : new \Sgdoce\Model\Entity\Vocativo();
    }

    public function setSqVocativo(\Sgdoce\Model\Entity\Vocativo $sqVocativo = NULL)
    {
        $this->sqVocativo = $sqVocativo;
        return $this;
    }

    /**
     * Set deEnderecamento
     *
     * @param integer deEnderecamento
     * @return integer
     */
    public function setDeEnderecamento($deEnderecamento)
    {
        $this->deEnderecamento = $deEnderecamento;
        return $this;
    }

    /**
     * Get deEnderecamento
     *
     * @return string
     */
    public function getDeEnderecamento()
    {
        return $this->deEnderecamento;
    }

    /**
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return TratamentoVocativo
     */
    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * Get inVeiculoPertenceUc
     *
     * @return boolean
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }
}