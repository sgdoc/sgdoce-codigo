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

use Doctrine\ORM\Mapping as ORM;

/**
 * VinculoSistemico
 *
 * @ORM\Table(name="vw_vinculo_sistemico")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\VinculoSistemico")
 */
class VinculoSistemico extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqVinculoSistemico
     *
     * @ORM\Column(name="sq_vinculo_sistemico", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqVinculoSistemico;

    /**
     * @var datetime $dtInicioVinculo
     *
     * @ORM\Column(name="dt_inicio_vinculo", type="zenddate", nullable=false)
     */
    private $dtInicioVinculo;

    /**
     * @var datetime $dtFimVinculo
     *
     * @ORM\Column(name="dt_fim_vinculo", type="zenddate", nullable=true)
     */
    private $dtFimVinculo;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Pessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_relacionamento", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaRelacionamento;

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
     * @var Sica\Model\Entity\TipoVinculoSistemico
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\TipoVinculoSistemico")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_vinculo_sistemico", referencedColumnName="sq_tipo_vinculo_sistemico")
     * })
     */
    private $sqTipoVinculoSistemico;

    /**
     * Get sqVinculoSistemico
     *
     * @return integer
     */
    public function getSqVinculoSistemico()
    {
        return $this->sqVinculoSistemico;
    }

    /**
     * Set dtInicioVinculo
     *
     * @param datetime $dtInicioVinculo
     * @return VinculoSistemico
     */
    public function setDtInicioVinculo($dtInicioVinculo)
    {
        $this->dtInicioVinculo = $dtInicioVinculo;
        return $this;
    }

    /**
     * Get dtInicioVinculo
     *
     * @return datetime
     */
    public function getDtInicioVinculo()
    {
        return $this->dtInicioVinculo;
    }

    /**
     * Set dtFimVinculo
     *
     * @param datetime $dtFimVinculo
     * @return VinculoSistemico
     */
    public function setDtFimVinculo($dtFimVinculo)
    {
        $this->dtFimVinculo = $dtFimVinculo;
        return $this;
    }

    /**
     * Get dtFimVinculo
     *
     * @return datetime
     */
    public function getDtFimVinculo()
    {
        return $this->dtFimVinculo;
    }

    /**
     * Set sqPessoaRelacionamento
     *
     * @param Sica\Model\Entity\Pessoa $sqPessoaRelacionamento
     * @return VinculoSistemico
     */
    public function setSqPessoaRelacionamento(\Sica\Model\Entity\Pessoa $sqPessoaRelacionamento = NULL)
    {
        $this->sqPessoaRelacionamento = $sqPessoaRelacionamento;
        return $this;
    }

    /**
     * Get sqPessoaRelacionamento
     *
     * @return Sica\Model\Entity\Pessoa
     */
    public function getSqPessoaRelacionamento()
    {
        return $this->sqPessoaRelacionamento ? $this->sqPessoaRelacionamento : new \Sica\Model\Entity\Pessoa();
    }

    /**
     * Set sqPessoa
     *
     * @param Sica\Model\Entity\Pessoa $sqPessoa
     * @return VinculoSistemico
     */
    public function setSqPessoa(\Sica\Model\Entity\Pessoa $sqPessoa = NULL)
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
        return $this->sqPessoa ? $this->sqPessoa : new \Sica\Model\Entity\Pessoa();
    }

    /**
     * Set sqTipoVinculoSistemico
     *
     * @param Sica\Model\Entity\TipoVinculoSistemico $sqTipoVinculoSistemico
     * @return VinculoSistemico
     */
    public function setSqTipoVinculoSistemico(\Sica\Model\Entity\TipoVinculoSistemico $sqTipoVinculoSistemico = NULL)
    {
        $this->sqTipoVinculoSistemico = $sqTipoVinculoSistemico;
        return $this;
    }

    /**
     * Get sqTipoVinculoSistemico
     *
     * @return Sica\Model\Entity\TipoVinculoSistemico
     */
    public function getSqTipoVinculoSistemico()
    {
        return $this->sqTipoVinculoSistemico ? $this->sqTipoVinculoSistemico : new TipoVinculoSistemico();
    }

}