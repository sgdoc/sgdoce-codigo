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
 * Sgdoce\Model\Entity\CaixaHistorico
 *
 * @ORM\Table(name="caixa_historico")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\CaixaHistorico")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class CaixaHistorico extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqCaixaHistorico
     *
     * @ORM\Id
     * @ORM\Column(name="sq_caixa_historico", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqCaixaHistorico;

    /**
     * @var \Sgdoce\Model\Entity\Caixa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Caixa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_caixa", referencedColumnName="sq_caixa")})
     */
    private $sqCaixa;

    /**
     * @var \Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")})
     */
    private $sqArtefato;

    /**
     * @var \Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_operacao", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoaOperacao;

    /**
     * @var \Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_unidade_org_usuario_operacao", referencedColumnName="sq_pessoa")})
     */
    private $sqUnidadeOrgOperacao;

    /**
     * @var \Sgdoce\Model\Entity\TipoHistoricoArquivo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoHistoricoArquivo")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_tipo_historico_arquivo", referencedColumnName="sq_tipo_historico_arquivo")})
     */
    private $sqTipoHistoricoArquivo;

    /**
     * @var zenddate $dtOperacao
     * @ORM\Column(name="dt_operacao", type="zenddate", nullable=false)
     */
    private $dtOperacao;

    /**
     *
     * @return integer
     */
    public function getSqCaixaHistorico ()
    {
        return $this->sqCaixaHistorico;
    }

    /**
     *
     * @return Caixa
     */
    public function getSqCaixa ()
    {
        return $this->sqCaixa;
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
     * @return VwPessoa
     */
    public function getSqPessoaOperacao ()
    {
        return $this->sqPessoaOperacao;
    }

    /**
     *
     * @return VwUnidadeOrg
     */
    public function getSqUnidadeOrgOperacao ()
    {
        return $this->sqUnidadeOrgOperacao;
    }

    /**
     *
     * @return TipoHistoricoArquivo
     */
    public function getSqTipoHistoricoArquivo ()
    {
        return $this->sqTipoHistoricoArquivo;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getDtOperacao ()
    {
        return $this->dtOperacao;
    }



    public function setSqCaixaHistorico ($sqCaixaHistorico)
    {
        $this->sqCaixaHistorico = $sqCaixaHistorico;
        return $this;
    }

    public function setSqCaixa (\Sgdoce\Model\Entity\Caixa $sqCaixa)
    {
        $this->sqCaixa = $sqCaixa;
        return $this;
    }

    public function setSqArtefato (\Sgdoce\Model\Entity\Artefato $sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    public function setSqPessoaOperacao (\Sgdoce\Model\Entity\VwPessoa $sqPessoaOperacao)
    {
        $this->sqPessoaOperacao = $sqPessoaOperacao;
        return $this;
    }

    public function setSqUnidadeOrgOperacao (\Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrgOperacao)
    {
        $this->sqUnidadeOrgOperacao = $sqUnidadeOrgOperacao;
        return $this;
    }

    public function setSqTipoHistoricoArquivo (\Sgdoce\Model\Entity\TipoHistoricoArquivo $sqTipoHistoricoArquivo)
    {
        $this->sqTipoHistoricoArquivo = $sqTipoHistoricoArquivo;
        return $this;
    }

    public function setDtOperacao ($dtOperacao)
    {
        $this->dtOperacao = $dtOperacao;
        return $this;
    }



}