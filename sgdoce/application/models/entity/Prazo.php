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
 * Sgdoce\Model\Entity\Prazo
 *
 * @ORM\Table(name="prazo")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\Prazo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Prazo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqPrazo
     *
     * @ORM\Column(name="sq_prazo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPrazo;

    /**
     * @var Sgdoce\Model\Entity\Prazo $sqPrazoPai
     * 
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\Prazo", mappedBy="sqPrazo")
     * @ORM\JoinColumn(name="sq_prazo_pai", referencedColumnName="sq_prazo")
     */
    private $sqPrazoPai;

    /**
     * @var Sgdoce\Model\Entity\Artefato $sqArtefato
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\Artefato", mappedBy="sqArtefato")
     * @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa $sqPessoaPrazo
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa", mappedBy="sqPessoa")
     * @ORM\JoinColumn(name="sq_pessoa_prazo", referencedColumnName="sq_pessoa")
     */
    private $sqPessoaPrazo;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrgPessoaPrazo
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg", mappedBy="sqUnidadeOrg")
     * @ORM\JoinColumn(name="sq_unidade_org_pessoa_prazo", referencedColumnName="sq_pessoa")
     */
    private $sqUnidadeOrgPessoaPrazo;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa $sqPessoaResposta
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa", mappedBy="sqPessoa")
     * @ORM\JoinColumn(name="sq_pessoa_resposta", referencedColumnName="sq_pessoa")
     */
    private $sqPessoaResposta;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrgPessoaResposta
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg", mappedBy="sqUnidadeOrg")
     * @ORM\JoinColumn(name="sq_unidade_org_pessoa_resposta", referencedColumnName="sq_pessoa")
     */
    private $sqUnidadeOrgPessoaResposta;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa $sqPessoaDestino
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa", mappedBy="sqPessoa")
     * @ORM\JoinColumn(name="sq_pessoa_destino", referencedColumnName="sq_pessoa")
     */
    private $sqPessoaDestino;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrgPessoaDestino
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg", mappedBy="sqUnidadeOrg")
     * @ORM\JoinColumn(name="sq_unidade_org_pessoa_destino", referencedColumnName="sq_pessoa")
     */
    private $sqUnidadeOrgPessoaDestino;
    
    /**
     * @var zenddate $dtPrazo
     *
     * @ORM\Column(name="dt_prazo", type="zenddate", nullable=false)
     */
    private $dtPrazo;

    /**
     * @var string $txSolicitacao
     *
     * @ORM\Column(name="tx_solicitacao", type="string", nullable=false)
     */
    private $txSolicitacao;

    /**
     * @var string $txResposta
     *
     * @ORM\Column(name="tx_resposta", type="string", nullable=true)
     */
    private $txResposta;

    /**
     * @var zenddate $dtResposta
     *
     * @ORM\Column(name="dt_resposta", type="zenddate", nullable=true)
     */
    private $dtResposta;

    /**
     * @var zenddate $dtCadastro
     *
     * @ORM\Column(name="dt_cadastro", type="zenddate", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var Sgdoce\Model\Entity\Artefato $sqArtefatoResposta
     *      
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\Artefato", mappedBy="sqArtefato")
     * @ORM\JoinColumn(name="sq_artefato_resposta", referencedColumnName="sq_artefato", nullable=true)
     */
    private $sqArtefatoResposta;
    
    /**
     * Getter $sqPrazo
     * 
     * @return type
     */
    public function getSqPrazo() {
        return $this->sqPrazo;
    }

    /**
     * Getter $sqPrazoPai
     * 
     * @return type
     */
    public function getSqPrazoPai() {
        return $this->sqPrazoPai;
    }

    /**
     * Getter $sqArtefato
     * 
     * @return type
     */
    public function getSqArtefato() {
        return $this->sqArtefato;
    }

    /**
     * Getter $sqPessoaPrazo
     * 
     * @return type
     */
    public function getSqPessoaPrazo() {
        return $this->sqPessoaPrazo;
    }

    /**
     * Getter $sqUnidadeOrgPessoaPrazo
     * 
     * @return type
     */
    public function getSqUnidadeOrgPessoaPrazo() {
        return $this->sqUnidadeOrgPessoaPrazo;
    }

    /**
     * Getter $sqPessoaResposta
     * 
     * @return type
     */
    public function getSqPessoaResposta() {
        return $this->sqPessoaResposta;
    }

    /**
     * Getter $sqPessoaResposta
     * 
     * @return type
     */
    public function getSqPessoaDestino() {
        return $this->sqPessoaDestino;
    }

    /**
     * Getter $sqUnidadeOrgPessoaResposta
     * 
     * @return type
     */
    public function getSqUnidadeOrgPessoaResposta() {
        return $this->sqUnidadeOrgPessoaResposta;
    }

    /**
     * Getter $sqUnidadeOrgPessoaResposta
     * 
     * @return type
     */
    public function getSqUnidadeOrgPessoaDestino() {
        return $this->sqUnidadeOrgPessoaDestino;
    }

    /**
     * Getter $dtPrazo
     * 
     * @return type
     */
    public function getDtPrazo() {
        return $this->dtPrazo;
    }

    /**
     * Getter $txSolicitacao
     * 
     * @return type
     */
    public function getTxSolicitacao() {
        return $this->txSolicitacao;
    }

    /**
     * Getter $txResposta
     * 
     * @return type
     */
    public function getTxResposta() {
        return $this->txResposta;
    }

    /**
     * Getter $dtResposta
     * 
     * @return type
     */
    public function getDtResposta() {
        return $this->dtResposta;
    }

    /**
     * Getter $dtCadastro
     * 
     * @return type
     */
    public function getDtCadastro() {
        return $this->dtCadastro;
    }

    /**
     * Getter $sqArtefatoResposta
     * 
     * @return type
     */
    public function getSqArtefatoResposta() {
        return $this->sqArtefatoResposta;
    }

    /**
     * Setter $sqPrazo
     * 
     * @return type
     */
    public function setSqPrazo($sqPrazo) {
        $this->sqPrazo = $sqPrazo;
    }

    /**
     * Setter $sqPrazoPai
     * 
     * @return type
     */
    public function setSqPrazoPai($sqPrazoPai) {
        $this->sqPrazoPai = $sqPrazoPai;
    }

    /**
     * Setter $sqArtefato
     * 
     * @return type
     */
    public function setSqArtefato($sqArtefato) {
        $this->sqArtefato = $sqArtefato;
    }

    /**
     * Setter $sqPessoaPrazo
     * 
     * @return type
     */
    public function setSqPessoaPrazo($sqPessoaPrazo) {
        $this->sqPessoaPrazo = $sqPessoaPrazo;
    }

    /**
     * Setter $sqUnidadeOrgPessoaPrazo
     * 
     * @return type
     */
    public function setSqUnidadeOrgPessoaPrazo($sqUnidadeOrgPessoaPrazo) {
        $this->sqUnidadeOrgPessoaPrazo = $sqUnidadeOrgPessoaPrazo;
    }

    /**
     * Setter $sqPessoaResposta
     * 
     * @return type
     */
    public function setSqPessoaResposta($sqPessoaResposta) {
        $this->sqPessoaResposta = $sqPessoaResposta;
    }

    /**
     * Setter $sqPessoaResposta
     * 
     * @return type
     */
    public function setSqPessoaDestino($sqPessoaDestino) {
        $this->sqPessoaDestino = $sqPessoaDestino;
    }

    /**
     * Setter $sqUnidadeOrgPessoaResposta
     * 
     * @return type
     */
    public function setSqUnidadeOrgPessoaResposta($sqUnidadeOrgPessoaResposta) {
        $this->sqUnidadeOrgPessoaResposta = $sqUnidadeOrgPessoaResposta;
    }

    /**
     * Setter $sqUnidadeOrgPessoaDestino
     * 
     * @return type
     */
    public function setSqUnidadeOrgPessoaDestino($sqUnidadeOrgPessoaDestino) {
        $this->sqUnidadeOrgPessoaDestino = $sqUnidadeOrgPessoaDestino;
    }

    /**
     * Setter $dtPrazo
     * 
     * @return type
     */
    public function setDtPrazo($dtPrazo) {
        $this->dtPrazo = $dtPrazo;
    }

    /**
     * Setter $txSolicitacao
     * 
     * @return type
     */
    public function setTxSolicitacao($txSolicitacao) {
        $this->txSolicitacao = $txSolicitacao;
    }

    /**
     * Setter $txResposta
     * 
     * @return type
     */
    public function setTxResposta($txResposta) {
        $this->txResposta = $txResposta;
    }

    /**
     * Setter $dtResposta
     * 
     * @return type
     */
    public function setDtResposta($dtResposta) {
        $this->dtResposta = $dtResposta;
    }

    /**
     * Setter $dtCadastro
     * 
     * @return type
     */
    public function setDtCadastro($dtCadastro) {
        $this->dtCadastro = $dtCadastro;
    }

    /**
     * Setter $sqArtefatoResposta
     * 
     * @return type
     */
    public function setSqArtefatoResposta($sqArtefatoResposta) {
        $this->sqArtefatoResposta = $sqArtefatoResposta;
    }



}