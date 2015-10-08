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
 * Sica\Model\Entity\VwFuncionalidade
 *
 * @ORM\Table(name="vw_funcionalidade")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Funcionalidade")
 */
class VwFuncionalidade extends \Core_Model_Entity_Abstract
{
    /**
     * @var Sica\Model\Entity\Funcionalidade
     * 
     * @ORM\Id
     * @ORM\Column(name="sq_funcionalidade", type="integer")
     */
    private $sqFuncionalidade;

    /**
     * @var Sica\Model\Entity\Menu
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Menu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_menu", referencedColumnName="sq_menu")
     * })
     */
    private $sqMenu;

    /**
     * @var string $noFuncionalidade
     *
     * @ORM\Column(name="no_funcionalidade", type="string")
     */
    private $noFuncionalidade;

    /**
     * @var boolean $inFuncionalidadePrincipal
     *
     * @ORM\Column(name="in_funcionalidade_principal", type="boolean")
     */
    private $inFuncionalidadePrincipal;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean")
     */
    private $stRegistroAtivo;

    /**
     * @var Sica\Model\Entity\Rota
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\Rota")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_rota_principal", referencedColumnName="sq_rota")
     * })
     */
    private $sqRotaPrincipal;

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
     * @var integer $nuQuantidadePerfil
     *
     * @ORM\Column(name="nu_quantidade_perfil", type="integer")
     */
    private $nuQuantidadePerfil;

    /**
     * Get sqFuncionalidade
     *
     * @return string
     */
    public function setSqFuncionalidade($sqFuncionalidade)
    {
        $this->sqFuncionalidade = $sqFuncionalidade;
        return $this;
    }

    /**
     * Get sqFuncionalidade
     *
     * @return Sica\Model\Entity\Funcionalidade
     */
    public function getSqFuncionalidade()
    {
        return $this->sqFuncionalidade;
    }

    /**
     * Set sqMenu
     *
     * @param Sica\Model\Entity\Menu $sqMenu
     * @return Funcionalidade
     */
    public function setSqMenu(Menu $sqMenu = NULL)
    {
        $this->sqMenu = $sqMenu;
        return $this;
    }

    /**
     * Get sqMenu
     *
     * @return Sica\Model\Entity\Menu
     */
    public function getSqMenu()
    {
        if (NULL === $this->sqMenu) {
            $this->setSqMenu(new Menu());
        }

        return $this->sqMenu;
    }

    /**
     * Set noFuncionalidade
     *
     * @param string $noFuncionalidade
     * @return Funcionalidade
     */
    public function setNoFuncionalidade($noFuncionalidade)
    {
        $this->assert('noFuncionalidade',$noFuncionalidade,$this);
        $this->noFuncionalidade = $noFuncionalidade;
        return $this;
    }

    /**
     * Get noFuncionalidade
     *
     * @return string
     */
    public function getNoFuncionalidade()
    {
        return $this->noFuncionalidade;
    }

    /**
     * Set inFuncionalidadePrincipal
     *
     * @param boolean $inFuncionalidadePrincipal
     * @return Funcionalidade
     */
    public function setInFuncionalidadePrincipal($inFuncionalidadePrincipal)
    {
        $this->inFuncionalidadePrincipal = $inFuncionalidadePrincipal;
        return $this;
    }

    /**
     * Get inFuncionalidadePrincipal
     *
     * @return string
     */
    public function getInFuncionalidadePrincipal()
    {
        return $this->inFuncionalidadePrincipal;
    }

    /**
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return Funcionalidade
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
     * Set sqRotaPrincipal
     *
     * @param Rota $rota
     * @return Funcionalidade
     */
    public function setSqRotaPrincipal($rota)
    {
        $this->sqRotaPrincipal = $rota;
        return $this;
    }

    /**
     * Get sqRotaPrincipal
     *
     * @return string
     */
    public function getSqRotaPrincipal()
    {
        if (NULL === $this->sqRotaPrincipal) {
            $this->setSqRotaPrincipal(new Rota());
        }

        return $this->sqRotaPrincipal;
    }

    /**
     * Set sqSistema
     *
     * @param Sica\Model\Entity\Sistema $sqSistema
     * @return Funcionalidade
     */
    public function setSqSistema(Sistema $sqSistema = NULL)
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
     * Set nuQuantidadePerfil
     *
     * @param integer $nuQuantidadePerfil
     * @return Funcionalidade
     */
    public function setNuQuantidadePerfil($nuQuantidadePerfil)
    {
        $this->nuQuantidadePerfil = $nuQuantidadePerfil;
        return $this;
    }

    /**
     * Get nuQuantidadePerfil
     *
     * @return integer
     */
    public function getNuQuantidadePerfil()
    {
        return $this->nuQuantidadePerfil;
    }
}
