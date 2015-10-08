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
use Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\Perfil
 *
 * @ORM\Table(name="perfil")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Perfil")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Perfil extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqPerfil
     *
     * @ORM\Column(name="sq_perfil", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPerfil;

    /**
     * @var string $noPerfil
     *
     * @ORM\Column(name="no_perfil", type="string", length=50, nullable=false)
     */
    private $noPerfil;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * @var boolean $inPerfilExterno
     *
     * @ORM\Column(name="in_perfil_externo", type="boolean", nullable=false)
     */
    private $inPerfilExterno;

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
     *
     * @var Sica\Model\Entity\UsuarioPerfil
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\UsuarioPerfil", mappedBy="sqPerfil")
     */
    private $sqUsuarioPerfil;

    /**
     * @var Sica\Model\Entity\Sistema
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\TipoPerfil", mappedBy="sqTipoPerfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_perfil", referencedColumnName="sq_tipo_perfil")
     * })
     */
    private $sqTipoPerfil;

    /**
     *
     * @var Sica\Model\Entity\PerfilFuncionalidade
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\PerfilFuncionalidade", mappedBy="sqPerfil")
     */
    private $sqPerfilFuncionalidade;

    /**
     * variável para retornar as funcionalidades checadas para o perfil
     * @var string
     */
    private $funcionalidades = "";

    /**
     *
     */
    public function __construct()
    {
        $this->sqPerfilFuncionalidade = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set sqPerfil
     *
     * * @param string $sqPerfil
     * @return Perfil
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
     * Set noPerfil
     *
     * @param string $noPerfil
     * @return Perfil
     */
    public function setNoPerfil($noPerfil)
    {
        $this->assert('noPerfil',$noPerfil,$this);
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
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return SicaPerfil
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
     * Set sqSistema
     *
     * @param Sica\Model\Entity\Sistema $sqSistema
     * @return Perfil
     */
    public function setSqSistema(\Sica\Model\Entity\Sistema $sqSistema = NULL)
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
        if (NULL === $this->sqSistema) {
            $this->setSqSistema(new Sistema());
        }
        return $this->sqSistema;
    }

    /**
     * Set inPerfilExterno
     *
     * @param boolean $inPerfilExterno
     * @return Perfil
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
     * @return Perfil
     */
    public function setSqTipoPerfil(TipoPerfil $sqTipoPerfil = NULL)
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
        if (NULL === $this->sqTipoPerfil) {
            $this->setSqTipoPerfil(new TipoPerfil());
        }
        return $this->sqTipoPerfil;
    }

    /**
     * Set sqPerfilFuncionalidade
     *
     * @param \Sica\Model\Entity\PerfilFuncionalidade $perfilFuncionalidade
     * @return Perfil
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

    /**
     * Set funcionalidades
     *
     * @param string $funcionalidades
     * @return string
     */
    public function setFuncionalidades($funcionalidades)
    {
        $this->funcionalidades = $funcionalidades;
        return $this;
    }

    /**
     * Get funcionalidades
     *
     * @return string
     */
    public function getFuncionalidades()
    {
        if (!isset($this->funcionalidades)) {
            foreach ($this->sqPerfilFuncionalidade as $k => $funcionalidade) {
                if ($k > 0) {
                    $this->funcionalidades.= ",";
                }
                $this->funcionalidades .= $funcionalidade->getFuncionalidade()->getSqFuncionalidade();
            }
        }
        return $this->funcionalidades;
    }

}