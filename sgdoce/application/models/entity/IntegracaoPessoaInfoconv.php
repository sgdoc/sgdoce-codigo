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


use Doctrine\ORM\Mapping as ORM,
    Core\Model\OWM\Mapping as OWM;

/**
 * SISICMBio
 *
 * Classe para Entity IntegracaoPessoaInfoconv
 *
 * @package      Model
 * @subpackage  Entity
 * @name         IntegracaoPessoaInfoconv
 * @version     1.0.0
 * @since       2015-08-11
 */

/**
 * Sgdoce\Model\Entity\IntegracaoPessoaInfoconv
 *
 * @ORM\Table(name="vw_integracao_pessoa_infoconv")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\IntegracaoPessoaInfoconv", readOnly=true)
 * @OWM\Endpoint(configKey="libcorp" , repositoryClass="Sgdoce\Model\Repository\IntegracaoPessoaInfoconvWs")
 */
class IntegracaoPessoaInfoconv extends \Core_Model_Entity_Abstract
{
    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa", inversedBy="sqIntegracaoPessoaInfoconv")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var datetime $dtNascimento
     *
     * @ORM\Column(name="dt_integracao", type="zenddate", nullable=true)
     */
    private $dtIntegracao;

    /**
     * @var string $txJustificativa
     *
     * @ORM\Column(name="tx_justificativa", type="string", length=250, nullable=true)
     */
    private $txJustificativa;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_autora", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaAutora;

    /**
     * Set sqPessoa
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqPessoa
     * @return PessoaFisica
     */
    public function setSqPessoa( $sqPessoa )
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return Sgdoce\Model\Entity\VwPessoa
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa ? $this->sqPessoa : new Pessoa();
    }

    /**
     * Set dtIntegracao
     *
     * @param datetime $dtIntegracao
     * @return IntegracaoPessoaInfoconv
     */
    public function setDtIntegracao ( $dtIntegracao )
    {
        $this->dtIntegracao = $dtIntegracao;
        return $this;
    }

    /**
     * Get dtIntegracao
     *
     * @return datetime
     */
    public function getDtIntegracao()
    {
        return $this->dtIntegracao;
    }

    /**
     * Set txJustificacao
     *
     * @param string txJustificativa
     * @return string
     */
    public function setTxJustificativa ( $txJustificativa )
    {
        $this->txJustificativa = $txJustificativa;
        return $this;
    }

    /**
     * Get txJustificativa
     *
     * @return string
     */
    public function getTxJustificativa()
    {
        return $this->txJustificativa;
    }

    /**
     * Set sqPessoaAutora
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqPessoa
     * @return IntegracaoPessoaInfoconv
     */
    public function setSqPessoaAutora( $sqPessoaAutora )
    {
        $this->sqPessoaAutora = $sqPessoaAutora;
        return $this;
    }

    /**
     * Get sqPessoaAutora
     *
     * @return Sgdoce\Model\Entity\VwPessoa
     */
    public function getSqPessoaAutora()
    {
        return $this->sqPessoaAutora ? $this->sqPessoaAutora : new Pessoa();
    }
}