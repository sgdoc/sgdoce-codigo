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

use Doctrine\DBAL\Types\BigIntType;
use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sgdoce\Model\Entity\ProcessoVolume
 *
 * @ORM\Table(name="processo_volume")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ProcessoVolume")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ProcessoVolume extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqVolume
     *
     * @ORM\Id
     * @ORM\Column(name="sq_volume", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqVolume;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_abertura", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaAbertura;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_org_abertura", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrgAbertura;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_encerramento", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaEncerramento;    

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_org_encerramento", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrgEncerramento;
    
    /**
     * @var integer $nuVolume
     *
     * @ORM\Column(name="nu_volume", type="integer", nullable=true)
     */
    private $nuVolume;
        
    /**
     * @var \Zend_Date $dtAbertura
     * 
     * @ORM\Column(name="dt_abertura", type="zenddate", nullable=true)
     */
    private $dtAbertura;
    
    /**
     * @var integer $nuFolhaInicial
     *
     * @ORM\Column(name="nu_folha_inicial", type="integer", nullable=true)
     */
    private $nuFolhaInicial;
        
    /**
     * @var \Zend_Date $dtEncerramento
     * 
     * @ORM\Column(name="dt_encerramento", type="zenddate", nullable=true)
     */
    private $dtEncerramento;
        
    /**
     * @var integer $nuFolhaFinal
     *
     * @ORM\Column(name="nu_folha_final", type="integer", nullable=true)
     */
    private $nuFolhaFinal;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_assinatura_abertura", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaAssinaturaAbertura;

    /**
     * @var Sgdoce\Model\Entity\VwCargo
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwCargo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_cargo_assinatura_abertura", referencedColumnName="sq_cargo")
     * })
     */
    private $sqCargoAssinaturaAbertura;

    /**
     * @var Sgdoce\Model\Entity\VwFuncao
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwFuncao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_funcao_assinatura_abertura", referencedColumnName="sq_funcao")
     * })
     */
    private $sqFuncaoAssinaturaAbertura;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_assinatura_encerramento", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaAssinaturaEncerramento;

    /**
     * @var Sgdoce\Model\Entity\VwCargo
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwCargo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_cargo_assinatura_encerramento", referencedColumnName="sq_cargo")
     * })
     */
    private $sqCargoAssinaturaEncerramento;

    /**
     * @var Sgdoce\Model\Entity\VwFuncao
     *
     * @ORM\ManyToOne(targetEntity="\Sgdoce\Model\Entity\VwFuncao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_funcao_assinatura_encerramento", referencedColumnName="sq_funcao")
     * })
     */
    private $sqFuncaoAssinaturaEncerramento;
    
    /**
     * Getter $sqVolume
     * 
     * @return integer
     */
    public function getSqVolume() 
    {
        return $this->sqVolume;
    }

    /**
     * Getter $sqArtefato
     * 
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function getSqArtefato() 
    {
        return $this->sqArtefato;
    }
    
    /**
     * Getter $sqPessoaAbertura
     * 
     * @return Sgdoce\Model\Entity\VwPessoa
     */
    public function getSqPessoaAbertura()
    {
        return $this->sqPessoaAbertura;
    }
    
    /**
     * Getter $sqUnidadeOrgAbertura
     * 
     * @return Sgdoce\Model\Entity\VwUnidadeOrg
     */
    public function getSqUnidadeOrgAbertura() {
        return $this->sqUnidadeOrgAbertura;
    }

    /**
     * Getter $sqPessoaEncerramento
     * 
     * @return Sgdoce\Model\Entity\VwPessoa
     */
    public function getSqPessoaEncerramento() 
    {
        return $this->sqPessoaEncerramento;
    }
    
    /**
     * Getter $sqUnidadeOrgEncerramento
     * 
     * @return Sgdoce\Model\Entity\VwUnidadeOrg
     */
    public function getSqUnidadeOrgEncerramento() {
        return $this->sqUnidadeOrgEncerramento;
    }

    /**
     * Getter $nuVolume
     * 
     * @return integer
     */
    public function getNuVolume() 
    {
        return $this->nuVolume;
    }

    /**
     * Getter $dtAbertura
     * 
     * @return string
     */
    public function getDtAbertura() 
    {
        return $this->dtAbertura;
    }

    /**
     * Getter $nuFolhaInicial
     * 
     * @return integer
     */
    public function getNuFolhaInicial() 
    {
        return $this->nuFolhaInicial;
    }

    /**
     * Getter $dtEncerramento
     * 
     * @return string
     */
    public function getDtEncerramento() 
    {
        return $this->dtEncerramento;
    }

    /**
     * Getter $nuFolhaFinal
     * 
     * @return integer
     */
    public function getNuFolhaFinal()
    {
        return $this->nuFolhaFinal;
    }

    /**
     * Getter $sqPessoaAssinaturaAbertura
     * 
     * @return Sgdoce\Model\Entity\VwPessoa
     */
    public function getSqPessoaAssinaturaAbertura() 
    {
        return $this->sqPessoaAssinaturaAbertura;
    }

    /**
     * Getter $sqCargoAssinaturaAbertura
     * 
     * @return Sgdoce\Model\Entity\VwCargo
     */
    public function getSqCargoAssinaturaAbertura() 
    {
        return $this->sqCargoAssinaturaAbertura;
    }

    /**
     * Getter $sqFuncaoAssinaturaAbertura
     * 
     * @return Sgdoce\Model\Entity\VwFuncao
     */
    public function getSqFuncaoAssinaturaAbertura() 
    {
        return $this->sqFuncaoAssinaturaAbertura;
    }

    /**
     * Getter $sqPessoaAssinaturaAbertura
     * 
     * @return Sgdoce\Model\Entity\VwPessoa
     */
    public function getSqPessoaAssinaturaEncerramento()
    {
        return $this->sqPessoaAssinaturaEncerramento;
    }

    /**
     * Getter $sqCargoAssinaturaEncerramento
     * 
     * @return Sgdoce\Model\Entity\VwCargo
     */
    public function getSqCargoAssinaturaEncerramento() 
    {
        return $this->sqCargoAssinaturaEncerramento;
    }

    /**
     * Getter $sqFuncaoAssinaturaEncerramento
     * 
     * @return Sgdoce\Model\Entity\VwFuncao
     */
    public function getSqFuncaoAssinaturaEncerramento() 
    {
        return $this->sqFuncaoAssinaturaEncerramento;
    }

    /**
     * Setter $sqVolume
     * 
     * @param integer $sqVolume
     * @return void
     */    
    public function setSqVolume($sqVolume) 
    {
        $this->sqVolume = $sqVolume;
        return $this;
    }
    
    /**
     * Setter $sqArtefato.
     * 
     * @param \Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setSqArtefato($sqArtefato) 
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    /**
     * Setter $sqPessoaAbertura
     * 
     * @param \Sgdoce\Model\Entity\VwPessoa $sqPessoaAbertura
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setSqPessoaAbertura($sqPessoaAbertura) 
    {
        $this->sqPessoaAbertura = $sqPessoaAbertura;
        return $this;
    }

    /**
     * Setter $sqUnidadeOrgAbertura
     * 
     * @param \Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrgAbertura
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setSqUnidadeOrgAbertura($sqUnidadeOrgAbertura) 
    {
        $this->sqUnidadeOrgAbertura = $sqUnidadeOrgAbertura;
        return $this;
    }

    /**
     * Setter $sqPessoaEncerramento
     * 
     * @param \Sgdoce\Model\Entity\VwPessoa $sqPessoaEncerramento
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setSqPessoaEncerramento($sqPessoaEncerramento) 
    {
        $this->sqPessoaEncerramento = $sqPessoaEncerramento;
        return $this;
    }

    /**
     * Setter $sqUnidadeOrgEncerramento
     * 
     * @param \Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrgEncerramento
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setSqUnidadeOrgEncerramento($sqUnidadeOrgEncerramento) 
    {
        $this->sqUnidadeOrgEncerramento = $sqUnidadeOrgEncerramento;
        return $this;
    }

    /**
     * Setter $nuVolume.
     * 
     * @param type $nuVolume
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setNuVolume($nuVolume) 
    {
        $this->nuVolume = $nuVolume;
        return $this;
    }
    
    /**
     * Setter $dtAbertura
     * 
     * @param \Zend_Date $dtAbertura
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setDtAbertura($dtAbertura) 
    {
        $this->dtAbertura = new \Zend_Date($dtAbertura);
        return $this;
    }

    /**
     * Setter $nuFolhaInicial
     * 
     * @param type $nuFolhaInicial
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setNuFolhaInicial($nuFolhaInicial) 
    {
        $this->nuFolhaInicial = $nuFolhaInicial;
        return $this;
    }

    /**
     * Setter $dtEncerramento.
     * 
     * @param \Zend_Date $dtEncerramento
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setDtEncerramento( $dtEncerramento )
    {
        $this->dtEncerramento = ($dtEncerramento) ? new \Zend_Date($dtEncerramento) : NULL;
        return $this;
    }
    
    /**
     * Setter $nuFolhaFinal
     * 
     * @param integer $nuFolhaFinal
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setNuFolhaFinal($nuFolhaFinal) 
    {
        $this->nuFolhaFinal = $nuFolhaFinal;
        return $this;
    }
    
    /**
     * Setter $sqPessoaAssinaturaAbertura
     * 
     * @param \Sgdoce\Model\Entity\VwPessoa $sqPessoa
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setSqPessoaAssinaturaAbertura($sqPessoaAssinaturaAbertura) 
    {
        $this->sqPessoaAssinaturaAbertura = $sqPessoaAssinaturaAbertura;
        return $this;
    }
    
    /**
     * Setter $sqCargoAssinaturaAbertura
     * 
     * @param \Sgdoce\Model\Entity\VwCargo $sqCargo
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setSqCargoAssinaturaAbertura($sqCargoAssinaturaAbertura) 
    {
        $this->sqCargoAssinaturaAbertura = $sqCargoAssinaturaAbertura;
        return $this;
    }
    
    /**
     * Setter $sqFuncaoAssinaturaAbertura
     * 
     * @param \Sgdoce\Model\Entity\VwFuncao $sqFuncao
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setSqFuncaoAssinaturaAbertura($sqFuncaoAssinaturaAbertura) 
    {
        $this->sqFuncaoAssinaturaAbertura = $sqFuncaoAssinaturaAbertura;
        return $this;
    }
    
    /**
     * Setter $sqPessoaAssinaturaEncerramento
     * 
     * @param \Sgdoce\Model\Entity\VwPessoa $sqPessoa
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setSqPessoaAssinaturaEncerramento($sqPessoaAssinaturaEncerramento) 
    {
        $this->sqPessoaAssinaturaEncerramento = $sqPessoaAssinaturaEncerramento;
        return $this;
    }
    
    /**
     * Setter $sqCargoAssinaturaEncerramento
     * 
     * @param \Sgdoce\Model\Entity\VwCargo $sqCargo
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setSqCargoAssinaturaEncerramento($sqCargoAssinaturaEncerramento) 
    {
        $this->sqCargoAssinaturaEncerramento = $sqCargoAssinaturaEncerramento;
        return $this;
    }
    
    /**
     * Setter $sqFuncaoAssinaturaEncerramento
     * 
     * @param \Sgdoce\Model\Entity\VwFuncao $sqFuncao
     * @return \Sgdoce\Model\Entity\ProcessoVolume
     */
    public function setSqFuncaoAssinaturaEncerramento($sqFuncaoAssinaturaEncerramento) 
    {
        $this->sqFuncaoAssinaturaEncerramento = $sqFuncaoAssinaturaEncerramento;
        return $this;
    }
}
