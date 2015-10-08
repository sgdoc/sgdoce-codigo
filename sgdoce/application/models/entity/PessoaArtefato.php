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
 * SISICMBio
 *
 * Classe para Entity PessoaArtefato
 *
 * @package     Model
 * @subpackage  Entity
 * @name         PessoaArtefato
 * @version     1.0.0
 * @since       2013-02-07
 */

/**
 * Sgdoce\Model\Entity\PessoaArtefato
 *
 * @ORM\Table(name="pessoa_artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\PessoaArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class PessoaArtefato extends \Core_Model_Entity_Abstract
{
    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\PessoaSgdoce
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\PessoaSgdoce")
     * @ORM\JoinColumn(name="sq_pessoa_sgdoce", referencedColumnName="sq_pessoa_sgdoce")
     */
    private $sqPessoaSgdoce;

    /**
     * @var Sgdoce\Model\Entity\PessoaFuncao
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\PessoaFuncao")
     * @ORM\JoinColumn(name="sq_pessoa_funcao", referencedColumnName="sq_pessoa_funcao")
     */
    private $sqPessoaFuncao;

    /**
     * @var Sgdoce\Model\Entity\TratamentoVocativo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TratamentoVocativo")
     * @ORM\JoinColumn(name="sq_tratamento_vocativo", referencedColumnName="sq_tratamento_vocativo")
     */
    private $sqTratamentoVocativo;

    /**
     * @var Sgdoce\Model\Entity\EnderecoSgdoce
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\EnderecoSgdoce")
     * @ORM\JoinColumn(name="sq_endereco_sgdoce", referencedColumnName="sq_endereco_sgdoce")
     */
    private $sqEnderecoSgdoce;

    /**
     * @var Sgdoce\Model\Entity\TelefoneSgdoce
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TelefoneSgdoce")
     * @ORM\JoinColumn(name="sq_telefone_sgdoce", referencedColumnName="sq_telefone_sgdoce")
     */
    private $sqTelefoneSgdoce;

    /**
     * @var Sgdoce\Model\Entity\EmailSgdoce
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\EmailSgdoce")
     * @ORM\JoinColumn(name="sq_email_sgdoce", referencedColumnName="sq_email_sgdoce")
     */
    private $sqEmailSgdoce;

    /**
     * @var Sgdoce\Model\Entity\PessoaUnidadeOrg
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\PessoaUnidadeOrg")
     * @ORM\JoinColumn(name="sq_pessoa_unidade_org", referencedColumnName="sq_pessoa_unidade_org")
     */
    private $sqPessoaUnidadeOrg;

    /**
     * @var string $txPosVocativo
     *
     * @ORM\Column(name="tx_pos_vocativo", type="string", nullable=true)
     */
    private $txPosVocativo;

    /**
     * @var string $txPosTratamento
     *
     * @ORM\Column(name="tx_pos_tratamento", type="string", nullable=true)
     */
    private $txPosTratamento;

    /**
     * @var Sgdoce\Model\Entity\PessoaSgdoce
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PessoaSgdoce")
     * @ORM\JoinColumn(name="sq_pessoa_encaminhado", referencedColumnName="sq_pessoa_sgdoce")
     */
    private $sqPessoaEncaminhado;

    /**
     * @var string $noCargoEncaminhado
     *
     * @ORM\Column(name="no_cargo_encaminhado", type="string", nullable=true)
     */
    private $noCargoEncaminhado;

    /**
     * @var boolean $stProcedencia
     * @ORM\Column(name="st_procedencia", type="boolean", nullable=true)
     */
    private $stProcedencia;

    public function getSqArtefato()
    {
        return $this->sqArtefato;
    }

    public function setSqArtefato($sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
    }

    public function getSqPessoaSgdoce()
    {
        return $this->sqPessoaSgdoce ? $this->sqPessoaSgdoce : new PessoaSgdoce();
    }

    public function setSqPessoaSgdoce(PessoaSgdoce $sqPessoaSgdoce)
    {
        $this->sqPessoaSgdoce = $sqPessoaSgdoce;
    }

    public function getSqPessoaFuncao()
    {
        return $this->sqPessoaFuncao;
    }

    public function setSqPessoaFuncao($sqPessoaFuncao)
    {
        $this->sqPessoaFuncao = $sqPessoaFuncao;
    }

    public function getSqTratamentoVocativo()
    {
        return $this->sqTratamentoVocativo;
    }

    public function setSqTratamentoVocativo($sqTratamentoVocativo)
    {
        $this->sqTratamentoVocativo = $sqTratamentoVocativo;
    }

    public function getSqEnderecoSgdoce()
    {
        return $this->sqEnderecoSgdoce ? $this->sqEnderecoSgdoce : new EnderecoSgdoce();
    }

    public function setSqEnderecoSgdoce(EnderecoSgdoce $sqEnderecoSgdoce  = NULL)
    {
        $this->sqEnderecoSgdoce = $sqEnderecoSgdoce;
        return $this;
    }

    public function getSqTelefoneSgdoce()
    {
        return $this->sqTelefoneSgdoce ? $this->sqTelefoneSgdoce : new TelefoneSgdoce();
    }

    public function setSqTelefoneSgdoce(TelefoneSgdoce $sqTelefoneSgdoce  = NULL)
    {
        $this->sqTelefoneSgdoce = $sqTelefoneSgdoce;
    }

    public function getSqEmailSgdoce()
    {
        return $this->sqEmailSgdoce ? $this->sqEmailSgdoce : new EmailSgdoce();
    }

    public function setSqEmailSgdoce(EmailSgdoce $sqEmailSgdoce = NULL)
    {
        $this->sqEmailSgdoce = $sqEmailSgdoce;
    }

    public function getSqPessoaUnidadeOrg()
    {
        return $this->sqPessoaUnidadeOrg;
    }

    public function setSqPessoaUnidadeOrg($sqPessoaUnidadeOrg)
    {
        $this->sqPessoaUnidadeOrg = $sqPessoaUnidadeOrg;
    }

    public function getTxPosVocativo()
    {
        return $this->txPosVocativo;
    }

    public function setTxPosVocativo($txPosVocativo)
    {
        $this->txPosVocativo = $txPosVocativo;
    }

    public function getTxPosTratamento()
    {
        return $this->txPosTratamento;
    }

    public function setTxPosTratamento($txPosTratamento)
    {
        $this->txPosTratamento = $txPosTratamento;
    }

    public function getSqPessoaEncaminhado()
    {
        return $this->sqPessoaEncaminhado ? $this->sqPessoaEncaminhado : new PessoaSgdoce();
    }

    public function setSqPessoaEncaminhado(PessoaSgdoce $sqPessoaEncaminhado = NULL)
    {
        $this->sqPessoaEncaminhado = $sqPessoaEncaminhado;
    }

    public function getNoCargoEncaminhado()
    {
        return $this->noCargoEncaminhado;
    }

    public function setNoCargoEncaminhado($noCargoEncaminhado)
    {
        $this->noCargoEncaminhado = $noCargoEncaminhado;
    }

    public function getStProcedencia ()
    {
        return $this->stProcedencia;
    }

    public function setStProcedencia ($stProcedencia = NULL)
    {
        $this->stProcedencia = $stProcedencia;
        return $this;
    }


}