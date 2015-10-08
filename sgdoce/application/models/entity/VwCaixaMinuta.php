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
use Doctrine\DBAL\Types\BooleanType;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sgdoce\Model\Entity\VwCaixaMinuta
 *
 * @ORM\Table(name="vw_caixa_minuta")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwCaixaMinuta", readOnly=true)
 */
class VwCaixaMinuta extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqArtefato
     *
     * @ORM\Id
     * @ORM\Column(name="sq_artefato", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefatoArtefato;

    /**
     * @var date $dataCriacao
     * @ORM\Column(name="data_criacao", type="zenddate", nullable=false)
     */
    private $dataCriacao;

    /**
     *@var $tipo
     * @ORM\Column(name="tipo", type="string", nullable=false)
     */
    private $tipo;

    /**
     * @var string $origem
     *
     * @ORM\Column(name="origem", type="string", length=120, nullable=true)
     */
    private $origem;

    /**
     * @var string $assunto
     *
     * @ORM\Column(name="assunto", type="string", length=250, nullable=false)
     */
    private $assunto;

    /**
     * @var string $autor
     * @ORM\Column(name="autor", type="string", nullable=true)
     */
    private $autor;

    /**
     * @var Date $prazo
     * @ORM\Column(name="prazo", type="zenddate", nullable=true)
     */
    private $prazo;

    /**
     * @var integer $nuDiasPrazo
     *
     * @ORM\Column(name="nu_dias_prazo", type="integer", nullable=false)
     */
    private $nuDiasPrazo;

    /**
     * @var BooleanType:: $inDiasCorridos
     *
     * @ORM\Column(name="in_dias_corridos", type="boolean", nullable=false)
     */
    private $inDiasCorridos;

    /**
     * @var string $status
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status;
//
//    /**
//     * @var integer $sqStatusArtefato
//     *
//     * @ORM\Column(name="sq_status_artefato", type="integer", nullable=false)
//     */
//    private $sqStatusArtefato;

    /**
     * @var integer $sqPessoa
     *
     * @ORM\Column(name="sq_pessoa", type="integer", nullable=false)
     */
    private $sqPessoa;

    /**
     * @var integer $sqOcorrencia
     *
     * @ORM\Column(name="sq_ocorrencia", type="integer", nullable=false)
     */
    private $sqOcorrencia;

    /**
     * @var integer $sqHistoricoArtefato
     *
     * @ORM\Column(name="sq_historico_artefato", type="integer", nullable=false)
     */
    private $sqHistoricoArtefato;

    /**
     * @return the $sqArtefatoArtefato
     */
    public function getSqArtefatoArtefato()
    {
        return $this->sqArtefatoArtefato;
    }

    /**
     * @param integer $sqArtefatoArtefato
     */
    public function setSqArtefatoArtefato($sqArtefatoArtefato)
    {
        $this->sqArtefatoArtefato = $sqArtefatoArtefato;
    }
//
//    /**
//     * @return the $sqStatusArtefato
//     */
//    public function getSqStatusArtefato()
//    {
//        return $this->sqStatusArtefato;
//    }
//
//    /**
//     * @param integer $sqStatusArtefato
//     */
//    public function setSqStatusArtefato($sqStatusArtefato)
//    {
//        $this->sqStatusArtefato = $sqStatusArtefato;
//    }

    /**
     * @return the $sqArtefato
     */
    public function getSqArtefato()
    {
        return $this->sqArtefato;
    }



    /**
     * @return the $tipo
     */
    public function getTipo()
    {
        return $this->tipo;
    }

    /**
     * @return the $origem
     */
    public function getOrigem()
    {
        return $this->origem;
    }

    /**
     * @return the $assunto
     */
    public function getAssunto()
    {
        return $this->assunto;
    }

    /**
     * @return Date $dataCriacao
     */
    public function getDataCriacao()
    {
        return $this->dataCriacao;
    }

    /**
     * @return the $prazo
     */
    public function getPrazo()
    {
        return $this->prazo;
    }


    /**
     * @return the $autor
     */
    public function getAutor()
    {
        return $this->autor;
    }

    /**
     * @return the $nuDiasPrazo
     */
    public function getNuDiasPrazo()
    {
        return $this->nuDiasPrazo;
    }

    /**
     * @return the $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return the $inDiasCorridos
     */
    public function getInDiasCorridos()
    {
        return $this->inDiasCorridos;
    }

    /**
     * @return the $pessoa
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa;
    }

    /**
     * @param date $dataCriacao
     */
    public function setDataCriacao($dataCriacao)
    {
        $this->dataCriacao = $dataCriacao;
    }

    /**
     * @param $tipo $tipo
     */
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
    }

    /**
     * @param string $origem
     */
    public function setOrigem($origem)
    {
        $this->origem = $origem;
    }

    /**
     * @param string $assunto
     */
    public function setAssunto($assunto)
    {
        $this->assunto = $assunto;
    }

    /**
     * @param string $autor
     */
    public function setAutor($autor)
    {
        $this->autor = $autor;
    }

    /**
     * @param Date $prazo
     */
    public function setPrazo($prazo)
    {
        $this->prazo = $prazo;
    }

    /**
     * @param integer $nuDiasPrazo
     */
    public function setNuDiasPrazo($nuDiasPrazo)
    {
        $this->nuDiasPrazo = $nuDiasPrazo;
    }

    /**
     * @param BooleanType:: $inDiasCorridos
     */
    public function setInDiasCorridos($inDiasCorridos)
    {
        $this->inDiasCorridos = $inDiasCorridos;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @param integer $pessoa
     */
    public function setSqPessoa($sqPessoa)
    {
        $this->pessoa = $sqPessoa;
    }

    /**
     * @return the $sqOcorrencia
     */
    public function getSqOcorrencia()
    {
        return $this->sqOcorrencia;
    }

    /**
     * @param integer $sqOcorrencia
     */
    public function setSqSqOcorrencia($sqOcorrencia)
    {
        $this->sqOcorrencia = $sqOcorrencia;
    }

    /**
     * @return the $sqHistoricoArtefato
     */
    public function getSqHistoricoArtefato()
    {
        return $this->sqHistoricoArtefato;
    }

    /**
     * @param integer $sqHistoricoArtefato
     */
    public function setSqHistoricoArtefato($sqHistoricoArtefato)
    {
        $this->sqHistoricoArtefato = $sqHistoricoArtefato;
    }
}