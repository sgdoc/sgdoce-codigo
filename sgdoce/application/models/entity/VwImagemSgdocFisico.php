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
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301, USA
 * */

namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sgdoce\Model\Entity\VwImagemSgdocFisico
 *
 *
 * @ORM\Table(name="vw_imagem_sgdoc_fisico")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwImagemSgdocFisico")
 */
class VwImagemSgdocFisico extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="sq_artefato", type="integer", nullable=false)
     */
    private $sqArtefato;
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="integer", nullable=false)
     */
    private $sqImagem;

    /**
     *
     * @ORM\Column(name="digital", type="string", nullable=false)
     */
    private $nuDigital;

    /**
     * @var integer $stPublico
     *
     * @ORM\Column(name="flg_publico", type="integer", nullable=false)
     */
    private $stPublico;

    /**
     * @var integer $dtInclusao
     *
     * @ORM\Column(name="dat_inclusao", type="integer", nullable=false)
     */
    private $dtInclusao;

    /**
     * @var string $txNomeArquivo
     *
     * @ORM\Column(name="md5", type="string", nullable=false)
     */
    private $txNomeArquivo;

    /**
     * @var integer $nuOrdem
     *
     * @ORM\Column(name="ordem", type="integer", nullable=false)
     */
    private $nuOrdem;

    /**
     * @var integer $inTipoArquivo
     *
     * @ORM\Column(name="img_type", type="integer", nullable=false)
     */
    private $inTipoArquivo;

    /**
     * @var integer $inQtdePagina
     *
     * @ORM\Column(name="total_paginas", type="integer", nullable=false)
     */
    private $inQtdePagina;

    public function getSqArtefato ()
    {
        return $this->sqArtefato;
    }

    public function getSqImagem ()
    {
        return $this->sqImagem;
    }

    public function getNuDigital ()
    {
        return $this->nuDigital;
    }

    public function getStPublico ()
    {
        return $this->stPublico;
    }

    public function getDtInclusao ()
    {
        return $this->dtInclusao;
    }

    public function getTxNomeArquivo ()
    {
        return $this->txNomeArquivo;
    }

    public function getNuOrdem ()
    {
        return $this->nuOrdem;
    }

    public function getInTipoArquivo ()
    {
        return $this->inTipoArquivo;
    }

    public function getInQtdePagina ()
    {
        return $this->inQtdePagina;
    }

    public function setSqArtefato ($sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    public function setSqImagem ($sqImagem)
    {
        $this->sqImagem = $sqImagem;
        return $this;
    }

    public function setNuDigital ($nuDigital)
    {
        $this->nuDigital = $nuDigital;
        return $this;
    }

    public function setStPublico ($stPublico)
    {
        $this->stPublico = $stPublico;
        return $this;
    }

    public function setDtInclusao ($dtInclusao)
    {
        $this->dtInclusao = $dtInclusao;
        return $this;
    }

    public function setTxNomeArquivo ($txNomeArquivo)
    {
        $this->txNomeArquivo = $txNomeArquivo;
        return $this;
    }

    public function setNuOrdem ($nuOrdem)
    {
        $this->nuOrdem = $nuOrdem;
        return $this;
    }

    public function setInTipoArquivo ($inTipoArquivo)
    {
        $this->inTipoArquivo = $inTipoArquivo;
        return $this;
    }

    public function setInQtdePagina ($inQtdePagina)
    {
        $this->inQtdePagina = $inQtdePagina;
        return $this;
    }

}
