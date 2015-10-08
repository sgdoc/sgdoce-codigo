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
* Sgdoce\Model\Entity\VwUltimaImagemArtefato
* Representação da ultima imagem do artefato:
*     classe: Sgdoce\Model\Repository\ArtefatoImagem
*     tabela: artefato_imagem
*
* @ORM\Table(name="vw_ultima_imagem_artefato")
* @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwUltimaImagemArtefato")
*/
class VwUltimaImagemArtefato extends \Core_Model_Entity_Abstract
{
    /**
     * @var Sgdoce\Model\Entity\Artefato $sqArtefato
     *
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato", nullable=false)
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\ArtefatoImagem $sqArtefatoImagem
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\ArtefatoImagem")
     * @ORM\JoinColumn(name="sq_artefato_imagem", referencedColumnName="sq_artefato_imagem", nullable=false)
     */
    private $sqArtefatoImagem;

    /**
     * @var integer $nuQtdePaginas
     *
     * @ORM\Column(name="nu_qtde_paginas", type="integer", nullable=false)
     */
    private $nuQtdePaginas;

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setSqArtefato (\Sgdoce\Model\Entity\Artefato $sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    /**
     * Get sqArtefato
     *
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function getSqArtefato ()
    {
        return $this->sqArtefato;
    }

    /**
     * Set sqArtefatoImagem
     *
     *
     * @param Sgdoce\Model\Entity\ArtefatoImagem $sqArtefatoImagem
     * @return Sgdoce\Model\Entity\ArtefatoImagem
     */
    public function setSqArtefatoImagem (\Sgdoce\Model\Entity\Artefato $sqArtefatoImagem)
    {
        $this->sqArtefatoImagem = $sqArtefatoImagem;
        return $this;
    }

    /**
     * Get sqArtefatoImagem
     *
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function getSqArtefatoImagem ()
    {
        return $this->sqArtefatoImagem;
    }

    public function getNuQtdePaginas ()
    {
        return $this->nuQtdePaginas;
    }

    public function setNuQtdePaginas ($nuQtdePaginas)
    {
        $this->nuQtdePaginas = $nuQtdePaginas;
        return $this;
    }


}