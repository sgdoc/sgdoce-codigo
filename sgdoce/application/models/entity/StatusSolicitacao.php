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
 * Sgdoce\Model\Entity\StatusSolicitacao
 *
 * @ORM\Table(name="status_solicitacao")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\StatusSolicitacao")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class StatusSolicitacao extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqStatusSolicitacao
     *
     * @ORM\Column(name="sq_status_solicitacao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqStatusSolicitacao;

    /**
     * @var Solicitacao $sqSolicitacao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Solicitacao", cascade={"persist"} )
     * @ORM\JoinColumn(name="sq_solicitacao", referencedColumnName="sq_solicitacao")
     */
    private $sqSolicitacao;

    /**
     * @var TipoStatusSolicitacao $sqTipoStatusSolicitacao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoStatusSolicitacao", cascade={"persist"} )
     * @ORM\JoinColumn(name="sq_tipo_status_solicitacao", referencedColumnName="sq_tipo_status_solicitacao")
     */
    private $sqTipoStatusSolicitacao;

    /**
     * @var VwPessoa $sqPessoaTriagem
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_triagem", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaTriagem;

    /**
     * @var VwPessoa $sqPessoaResponsavel
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_responsavel", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaResponsavel;

    /**
     * @var string $txComentario
     *
     * @ORM\Column(name="tx_comentario", type="string", nullable=true)
     */
    private $txComentario;

    /**
     * @var \Zend_Date $dtOperacao
     *
     * @ORM\Column(name="dt_operacao", type="zenddate", nullable=false)
     */
    private $dtOperacao;


    public function getSqStatusSolicitacao ()
    {
        return $this->sqStatusSolicitacao;
    }

    /**
     *
     * @return Solicitacao
     */
    public function getSqSolicitacao ()
    {
        return $this->sqSolicitacao;
    }

    /**
     *
     * @return TipoStatusSolicitacao
     */
    public function getSqTipoStatusSolicitacao ()
    {
        return $this->sqTipoStatusSolicitacao;
    }

    /**
     *
     * @return VwPessoa
     */
    public function getSqPessoaTriagem ()
    {
        return $this->sqPessoaTriagem;
    }

    /**
     *
     * @return VwPessoa
     */
    public function getSqPessoaResponsavel ()
    {
        return $this->sqPessoaResponsavel;
    }

    /**
     *
     * @return string
     */
    public function getTxComentario ()
    {
        return $this->txComentario;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getDtOperacao ()
    {
        return $this->dtOperacao;
    }

    public function setSqStatusSolicitacao ($sqStatusSolicitacao)
    {
        $this->sqStatusSolicitacao = $sqStatusSolicitacao;
        return $this;
    }

    public function setSqSolicitacao (Solicitacao $sqSolicitacao)
    {
        $this->sqSolicitacao = $sqSolicitacao;
        return $this;
    }

    public function setSqTipoStatusSolicitacao (TipoStatusSolicitacao $sqTipoStatusSolicitacao)
    {
        $this->sqTipoStatusSolicitacao = $sqTipoStatusSolicitacao;
        return $this;
    }

    public function setSqPessoaTriagem (VwPessoa $sqPessoaTriagem)
    {
        $this->sqPessoaTriagem = $sqPessoaTriagem;
        return $this;
    }

    public function setSqPessoaResponsavel (VwPessoa $sqPessoaResponsavel)
    {
        $this->sqPessoaResponsavel = $sqPessoaResponsavel;
        return $this;
    }

    public function setTxComentario ($txComentario)
    {
        $this->txComentario = $txComentario;
        return $this;
    }

    public function setDtOperacao (\Zend_Date $dtOperacao)
    {
        $this->dtOperacao = $dtOperacao;
        return $this;
    }


}