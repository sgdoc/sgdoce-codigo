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
 * Sgdoce\Model\Entity\LoteEtiqueta
 *
 * @ORM\Table(name="lote_etiqueta")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\LoteEtiqueta")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class LoteEtiqueta extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqLoteEtiqueta
     *
     * @ORM\Id
     * @ORM\Column(name="sq_lote_etiqueta", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqLoteEtiqueta;

     /**
     * @var Sgdoce\Model\Entity\QuantidadeEtiqueta
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\QuantidadeEtiqueta" )
     * @ORM\JoinColumn(name="sq_quantidade_etiqueta", referencedColumnName="sq_quantidade_etiqueta", nullable=false)
     */
    private $sqQuantidadeEtiqueta;

    /**
     * @var Sgdoce\Model\Entity\TipoEtiqueta
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoEtiqueta" )
     * @ORM\JoinColumn(name="sq_tipo_etiqueta", referencedColumnName="sq_tipo_etiqueta", nullable=false)
     */
    private $sqTipoEtiqueta;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg" )
     * @ORM\JoinColumn(name="sq_unidade_org", referencedColumnName="sq_pessoa" , nullable=false)
     */
    private $sqUnidadeOrg;

    /**
     * @var Sgdoce\Model\Entity\VwEtiquetaDisponivelLote
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwEtiquetaDisponivelLote", mappedBy="sqLoteEtiqueta")
     */
    private $sqEtiquetaDisponivelLote;

    /**
     * @var integer $nuInicial
     *
     * @ORM\Column(name="nu_inicial", type="integer", nullable=false)
     */
    private $nuInicial;

    /**
     * @var integer $nuFinal
     *
     * @ORM\Column(name="nu_final", type="integer", nullable=false)
     */
    private $nuFinal;

        /**
     * @var integer $nuAno
     *
     * @ORM\Column(name="nu_ano", type="integer", nullable=false)
     */
    private $nuAno;

    /**
     * @var Sgdoce\Model\Entity\VwUsuario
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUsuario")
     * @ORM\JoinColumn(name="sq_usuario", referencedColumnName="sq_usuario")
     */
    private $sqUsuario;

    /**
     * @var zenddate $dtImpressao
     * @ORM\Column(name="dt_impressao", type="zenddate", nullable=true)
     */
    private $dtImpressao;

    /**
     * @var integer $nuInicialNupSiorg
     *
     * @ORM\Column(name="nu_inicial_nup_siorg", type="integer", nullable=true)
     */
    private $nuInicialNupSiorg;

    /**
     * @var integer $nuFinalNupSiorg
     *
     * @ORM\Column(name="nu_final_nup_siorg", type="integer", nullable=true)
     */
    private $nuFinalNupSiorg;

    /**
     * @var zenddate $dtCriacao
     * @ORM\Column(name="dt_criacao", type="zenddate", nullable=false)
     */
    private $dtCriacao;

    /**
     * @var boolean $inLoteComNupSiorg
     * @ORM\Column(name="in_lote_com_nup_siorg", type="boolean", nullable=false)
     */
    private $inLoteComNupSiorg;

    /**
     * Set sqLoteEtiqueta
     *
     * @param $sqLoteEtiqueta
     * @return self
     */
    public function setSqLoteEtiqueta($sqLoteEtiqueta)
    {
        $this->sqLoteEtiqueta = $sqLoteEtiqueta;
        return $this;
    }

    /**
     * Set sqQuantidadeEtiqueta
     *
     * @param $sqQuantidadeEtiqueta
     * @return self
     */
    public function setSqQuantidadeEtiqueta($sqQuantidadeEtiqueta)
    {
        $this->sqQuantidadeEtiqueta = $sqQuantidadeEtiqueta;
        return $this;
    }

    /**
     * Set sqTipoEtiqueta
     *
     * @param $sqTipoEtiqueta
     * @return self
     */
    public function setSqTipoEtiqueta($sqTipoEtiqueta)
    {
        $this->sqTipoEtiqueta = $sqTipoEtiqueta;
        return $this;
    }

    /**
     * Set sqUnidadeOrg
     *
     * @param $sqUnidadeOrg
     * @return self
     */
    public function setSqUnidadeOrg($sqUnidadeOrg)
    {
        $this->sqUnidadeOrg = $sqUnidadeOrg;
        return $this;
    }

    /**
     * Set nuInicial
     *
     * @param $nuInicial
     * @return self
     */
    public function setNuInicial($nuInicial)
    {
        $this->nuInicial = $nuInicial;
        return $this;
    }

    /**
     * Set nuFinal
     *
     * @param $nuFinal
     * @return self
     */
    public function setNuFinal($nuFinal)
    {
        $this->nuFinal = $nuFinal;
        return $this;
    }

    /**
     * Set nuAno
     *
     * @param $nuAno
     * @return self
     */
    public function setNuAno($nuAno)
    {
        $this->nuAno = $nuAno;
        return $this;
    }

    /**
     * Set sqEtiquetaDisponivelLote
     *
     * @param $sqEtiquetaDisponivelLote
     * @return self
     */
    public function setSqEtiquetaDisponivelLote($sqEtiquetaDisponivelLote) {
        $this->sqEtiquetaDisponivelLote = $sqEtiquetaDisponivelLote;
        return $this;
    }

    /**
     * Set sqUsuario
     *
     * @param $sqUsuario
     * @return self
     */
    public function setSqUsuario($sqUsuario)
    {
        $this->sqUsuario = $sqUsuario;
        return $this;
    }

    /**
     * Set dtImpressao
     *
     * @param $dtImpressao
     * @return self
     */
    public function setDtImpressao($dtImpressao)
    {
        $this->dtImpressao = $dtImpressao;
        return $this;
    }

    /**
     * Set nuInicialNupSiorg
     *
     * @param $nuInicialNupSiorg
     * @return self
     */
    public function setNuInicialNupSiorg ($nuInicialNupSiorg)
    {
        $this->nuInicialNupSiorg = $nuInicialNupSiorg;
        return $this;
    }

    /**
     * Set nuFinalNupSiorg
     *
     * @param $nuFinalNupSiorg
     * @return self
     */
    public function setNuFinalNupSiorg ($nuFinalNupSiorg)
    {
        $this->nuFinalNupSiorg = $nuFinalNupSiorg;
        return $this;
    }

    /**
     * Set dtCriacao
     *
     * @param $dtCriacao
     * @return self
     */
    public function setDtCriacao ($dtCriacao)
    {
        $this->dtCriacao = $dtCriacao;
        return $this;
    }

    /**
     * Set inLoteComNupSiorg
     *
     * @param $inLoteComNupSiorg
     * @return self
     */
    public function setInLoteComNupSiorg ($inLoteComNupSiorg)
    {
        $this->inLoteComNupSiorg = $inLoteComNupSiorg;
        return $this;
    }


    /**
     * Get sqLoteEtiqueta
     *
     * @return integer
     */
    public function getSqLoteEtiqueta()
    {
        return $this->sqLoteEtiqueta;
    }

    /**
     * Get sqQuantidadeEtiqueta
     *
     * @return QuantidadeEtiqueta
     */
    public function getSqQuantidadeEtiqueta()
    {
        return $this->sqQuantidadeEtiqueta;
    }

    /**
     * Get sqTipoEtiqueta
     *
     * @return TipoEtiqueta
     */
    public function getSqTipoEtiqueta()
    {
        return $this->sqTipoEtiqueta;
    }

    /**
     * Get sqUnidadeOrg
     *
     * @return VwUnidadeOrg
     */
    public function getSqUnidadeOrg()
    {
        return $this->sqUnidadeOrg;
    }

    /**
     * Get nuInicial
     *
     * @return integer
     */
    public function getNuInicial()
    {
        return $this->nuInicial;
    }

    /**
     * Get nuFinal
     *
     * @return integer
     */
    public function getNuFinal()
    {
        return $this->nuFinal;
    }

    /**
     * Get nuAno
     *
     * @return integer
     */
    public function getNuAno()
    {
        return $this->nuAno;
    }

    /**
     * Get sqEtiquetaDisponivelLote
     *
     * @return integer
     */
    public function getSqEtiquetaDisponivelLote()
    {
        return $this->sqEtiquetaDisponivelLote;
    }

    /**
     * Get sqUsuario
     *
     * @return VwUsuario
     */
    public function getSqUsuario()
    {
        return $this->sqUsuario;
    }

    /**
     * Get dtImpressao
     *
     * @return \Zend_Date
     */
    public function getDtImpressao()
    {
        return $this->dtImpressao;
    }

    /**
     * Get nuInicialNupSiorg
     *
     * @return integer
     */
    public function getNuInicialNupSiorg ()
    {
        return $this->nuInicialNupSiorg;
    }

    /**
     * Get nuFinalNupSiorg
     *
     * @return integer
     */
    public function getNuFinalNupSiorg ()
    {
        return $this->nuFinalNupSiorg;
    }

    /**
     * Get dtCriacao
     *
     * @return \Zend_Date
     */
    public function getDtCriacao ()
    {
        return $this->dtCriacao;
    }

    /**
     * Get inLoteComNupSiorg
     *
     * @return boolean
     */
    public function getInLoteComNupSiorg ()
    {
        return $this->inLoteComNupSiorg;
    }
}