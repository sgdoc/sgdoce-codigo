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
 * Sgdoce\Model\Entity\Solicitacao
 *
 * @ORM\Table(name="solicitacao")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Solicitacao")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Solicitacao extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqSolicitacao
     *
     * @ORM\Column(name="sq_solicitacao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqSolicitacao;

    /**
     * @var Artefato $sqArtefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefato;

    /**
     * @var TipoAssuntoSolicitacao $sqTipoAssuntoSolicitacao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoAssuntoSolicitacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_assunto_solicitacao", referencedColumnName="sq_tipo_assunto_solicitacao")
     * })
     */
    private $sqTipoAssuntoSolicitacao;

    /**
     * @var string $dsSolicitacao
     *
     * @ORM\Column(name="ds_solicitacao", type="string", length=500, nullable=false)
     */
    private $dsSolicitacao;

    /**
     * @var VwPessoa $sqPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var VwUnidadeOrg $sqUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_org", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrg;

    /**
     * @var \Zend_Date $dtSolicitacao
     *
     * @ORM\Column(name="dt_solicitacao", type="zenddate", nullable=false)
     */
    private $dtSolicitacao;

    /**
     *
     * @return integer
     */
    public function getSqSolicitacao ()
    {
        return $this->sqSolicitacao;
    }

    /**
     *
     * @return Artefato
     */
    public function getSqArtefato ()
    {
        return $this->sqArtefato;
    }

    /**
     *
     * @return TipoAssuntoSolicitacao
     */
    public function getSqTipoAssuntoSolicitacao ()
    {
        return $this->sqTipoAssuntoSolicitacao;
    }

    /**
     *
     * @return string
     */
    public function getDsSolicitacao ()
    {
        return $this->dsSolicitacao;
    }

    /**
     *
     * @return VwPessoa
     */
    public function getSqPessoa ()
    {
        return $this->sqPessoa;
    }

    /**
     *
     * @return VwUnidadeOrg
     */
    public function getSqUnidadeOrg ()
    {
        return $this->sqUnidadeOrg;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getDtSolicitacao ()
    {
        return $this->dtSolicitacao;
    }

    public function setSqSolicitacao ($sqSolicitacao)
    {
        $this->sqSolicitacao = $sqSolicitacao;
        return $this;
    }

    public function setSqArtefato (Artefato $sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    public function setSqTipoAssuntoSolicitacao (TipoAssuntoSolicitacao $sqTipoAssuntoSolicitacao)
    {
        $this->sqTipoAssuntoSolicitacao = $sqTipoAssuntoSolicitacao;
        return $this;
    }

    public function setDsSolicitacao ($dsSolicitacao)
    {
        $this->assert('dsSolicitacao', $dsSolicitacao, $this);
        $this->dsSolicitacao = $dsSolicitacao;
        return $this;
    }

    public function setSqPessoa (VwPessoa $sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    public function setSqUnidadeOrg (VwUnidadeOrg $sqUnidadeOrg)
    {
        $this->sqUnidadeOrg = $sqUnidadeOrg;
        return $this;
    }

    public function setDtSolicitacao (\Zend_Date $dtSolicitacao)
    {
        $this->dtSolicitacao = $dtSolicitacao;
        return $this;
    }


}