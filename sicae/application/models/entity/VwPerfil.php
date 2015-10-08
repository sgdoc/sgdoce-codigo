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
 * Sica\Model\Entity\VwPerfil
 *
 * @ORM\Table(name="vw_perfil")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Perfil")
 */
class VwPerfil extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqPerfil
     *
     * @ORM\Id
     * @ORM\Column(name="sq_perfil", type="integer")
     */
    private $sqPerfil;

    /**
     * @var Sica\Model\Entity\Sistema
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Sistema")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_sistema", referencedColumnName="sq_sistema")
     * })
     */
    private $sqSistema;

    /**
     * @var string $noPerfil
     *
     * @ORM\Column(name="no_perfil", type="string", length=50, nullable=false)
     */
    private $noPerfil;

    /**
     * @var boolean $inPerfilExterno
     *
     * @ORM\Column(name="in_perfil_externo", type="boolean", nullable=false)
     */
    private $inPerfilExterno;

    /**
     * @var Sica\Model\Entity\TipoPerfil
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\TipoPerfil", mappedBy="sqTipoPerfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_perfil", referencedColumnName="sq_tipo_perfil")
     * })
     */
    private $sqTipoPerfil;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * @var integer $nuQuantidadeUsuario
     *
     * @ORM\Column(name="nu_quantidade_usuario", type="integer")
     */
    private $nuQuantidadeUsuario;

    /**
     * @var integer $nuQuantidadeUsuarioExterno
     *
     * @ORM\Column(name="nu_quantidade_usuario_externo", type="integer")
     */
    private $nuQuantidadeUsuarioExterno;

    /**
     * @var Sica\Model\Entity\PerfilFuncionalidade
     *
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\PerfilFuncionalidade", mappedBy="sqPerfil")
     */
    private $sqPerfilFuncionalidade;

    /**
     * Set sqPerfil
     *
     * * @param string $sqPerfil
     * @return VwPerfil
     */
    public function setSqPerfil($sqPerfil)
    {
        $this->sqPerfil = $sqPerfil;
        return $this;
    }

    /**
     * Get sqPerfil
     *
     * @return integer
     */
    public function getSqPerfil()
    {
        return $this->sqPerfil;
    }

    /**
     * Set sqSistema
     *
     * @param Sica\Model\Entity\Sistema $sqSistema
     * @return VwPerfil
     */
    public function setSqSistema($sqSistema = NULL)
    {
        $this->sqSistema = $sqSistema;
        return $this;
    }

    /**
     * Get sqSistema
     *
     * @return Sica\Model\Entity\Sistema
     */
    public function getSqSistema()
    {
        return $this->sqSistema;
    }

    /**
     * Set noPerfil
     *
     * @param string $noPerfil
     * @return VwPerfil
     */
    public function setNoPerfil($noPerfil)
    {
        $this->noPerfil = $noPerfil;
        return $this;
    }

    /**
     * Get noPerfil
     *
     * @return string
     */
    public function getNoPerfil()
    {
        return $this->noPerfil;
    }

    /**
     * Set inPerfilExterno
     *
     * @param boolean $inPerfilExterno
     * @return VwPerfil
     */
    public function setInPerfilExterno($inPerfilExterno)
    {
        $this->inPerfilExterno = $inPerfilExterno;
        return $this;
    }

    /**
     * Get $inPerfilExterno
     *
     * @return string
     */
    public function getInPerfilExterno()
    {
        return $this->inPerfilExterno;
    }

    /**
     * Set sqTipoPerfil
     *
     * @param integer $sqTipoPerfil
     * @return VwPerfil
     */
    public function setSqTipoPerfil($sqTipoPerfil = NULL)
    {
        $this->sqTipoPerfil = $sqTipoPerfil;
        return $this;
    }

    /**
     * Get sqPerfil
     *
     * @return integer
     */
    public function getSqTipoPerfil()
    {
        return $this->sqTipoPerfil;
    }

    /**
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return VwPerfil
     */
    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * Get stRegistroAtivo
     *
     * @return string
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }

    /**
     * Set nuQuantidadeUsuario
     *
     * @param integer $nuQuantidadeUsuario
     * @return VwPerfil
     */
    public function setNuQuantidadeUsuario($nuQuantidadeUsuario)
    {
        $this->nuQuantidadeUsuario = $nuQuantidadeUsuario;
        return $this;
    }

    /**
     * Get nuQuantidadeUsuario
     *
     * @return integer
     */
    public function getNuQuantidadeUsuario()
    {
        return $this->nuQuantidadeUsuario;
    }

    /**
     * Set nuQuantidadeUsuarioExterno
     *
     * @param integer $nuQuantidadeUsuarioExterno
     * @return VwPerfil
     */
    public function setNuQuantidadeUsuarioExterno($nuQuantidadeUsuarioExterno)
    {
        $this->nuQuantidadeUsuarioExterno = $nuQuantidadeUsuarioExterno;
        return $this;
    }

    /**
     * Get nuQuantidadeUsuarioExterno
     *
     * @return integer
     */
    public function getNuQuantidadeUsuarioExterno()
    {
        return $this->nuQuantidadeUsuarioExterno;
    }

    /**
     * Set sqPerfilFuncionalidade
     *
     * @param \Sica\Model\Entity\PerfilFuncionalidade $perfilFuncionalidade
     * @return VwPerfil
     */
    public function addSqPerfilFuncionalidade(\Sica\Model\Entity\PerfilFuncionalidade $perfilFuncionalidade)
    {
        $this->sqPerfilFuncionalidade[] = $perfilFuncionalidade;
        $perfilFuncionalidade->setSqPerfil($this);
        return $this;
    }

    /**
     * Get sqPerfilFuncionalidade
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSqPerfilFuncionalidade()
    {
        return $this->sqPerfilFuncionalidade;
    }
}