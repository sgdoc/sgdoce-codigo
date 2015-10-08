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

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM,
    Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\DadoBancario
 *
 * @ORM\Table(name="vw_dado_bancario")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\DadoBancario", readOnly=true)
 * @OWM\Endpoint(configKey="libcorp" , repositoryClass="Sica\Model\Repository\DadoBancarioWs")
 */
class DadoBancario extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqDadoBancario
     *
     * @ORM\Column(name="sq_dado_bancario", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqDadoBancario;

    /**
     * @var string $nuConta
     *
     * @ORM\Column(name="nu_conta", type="string", length=10, nullable=false)
     */
    private $nuConta;

    /**
     * @var string $nuContaDv
     *
     * @ORM\Column(name="nu_conta_dv", type="string", length=3, nullable=true)
     */
    private $nuContaDv;

    /**
     * @var string $coOperacao
     *
     * @ORM\Column(name="co_operacao", type="string", length=3, nullable=true)
     */
    private $coOperacao;

    /**
     * @var Sica\Model\Entity\TipoDadoBancario
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\TipoDadoBancario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_dado_bancario", referencedColumnName="sq_tipo_dado_bancario")
     * })
     */
    private $sqTipoDadoBancario;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Pessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var Sica\Model\Entity\Agencia
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Agencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_agencia", referencedColumnName="sq_agencia")
     * })
     */
    private $sqAgencia;

    /**
     * Get sqDadoBancario
     *
     * @return integer
     */
    public function getSqDadoBancario()
    {
        return $this->sqDadoBancario;
    }

    /**
     * Set nuConta
     *
     * @param string $nuConta
     * @return DadoBancario
     */
    public function setNuConta($nuConta)
    {
        $this->nuConta = $nuConta;
        return $this;
    }

    /**
     * Get nuConta
     *
     * @return string
     */
    public function getNuConta()
    {
        return $this->nuConta;
    }

    /**
     * Set nuContaDv
     *
     * @param string $nuContaDv
     * @return DadoBancario
     */
    public function setNuContaDv($nuContaDv)
    {
        $this->nuContaDv = $nuContaDv;
        return $this;
    }

    /**
     * Get nuContaDv
     *
     * @return string
     */
    public function getNuContaDv()
    {
        return $this->nuContaDv;
    }

    /**
     * Set coOperacao
     *
     * @param string $coOperacao
     * @return DadoBancario
     */
    public function setCoOperacao($coOperacao)
    {
        $this->coOperacao = $coOperacao;
        return $this;
    }

    /**
     * Get coOperacao
     *
     * @return string
     */
    public function getCoOperacao()
    {
        return $this->coOperacao;
    }

    /**
     * Set sqTipoDadoBancario
     *
     * @param Sica\Model\Entity\TipoDadoBancario $sqTipoDadoBancario
     * @return DadoBancario
     */
    public function setSqTipoDadoBancario(TipoDadoBancario $sqTipoDadoBancario = NULL)
    {
        $this->sqTipoDadoBancario = $sqTipoDadoBancario;
        return $this;
    }

    /**
     * Get sqTipoDadoBancario
     *
     * @return Sica\Model\Entity\TipoDadoBancario
     */
    public function getSqTipoDadoBancario()
    {
        if (NULL === $this->sqTipoDadoBancario) {
            $this->setSqTipoDadoBancario(new TipoDadoBancario());
        }

        return $this->sqTipoDadoBancario;
    }

    /**
     * Set sqPessoa
     *
     * @param Sica\Model\Entity\Pessoa $sqPessoa
     * @return DadoBancario
     */
    public function setSqPessoa(Pessoa $sqPessoa = NULL)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return Sica\Model\Entity\Pessoa
     */
    public function getSqPessoa()
    {
        if (NULL === $this->sqPessoa) {
            $this->setSqPessoa(new Pessoa());
        }

        return $this->sqPessoa;
    }

    /**
     * Set sqAgencia
     *
     * @param Sica\Model\Entity\Agencia $sqAgencia
     * @return DadoBancario
     */
    public function setSqAgencia(Agencia $sqAgencia = NULL)
    {
        $this->sqAgencia = $sqAgencia;
        return $this;
    }

    /**
     * Get sqAgencia
     *
     * @return Sica\Model\Entity\Agencia
     */
    public function getSqAgencia()
    {
        if (NULL === $this->sqAgencia) {
            $this->setSqAgencia(new Agencia());
        }

        return $this->sqAgencia;
    }

}
