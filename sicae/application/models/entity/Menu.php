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
 * Sica\Model\Entity\Menu
 *
 * @ORM\Table(name="menu")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Menu")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Menu extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqMenu
     *
     * @ORM\Column(name="sq_menu", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqMenu;

    /**
     * @var Sica\Model\Entity\Menu
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Menu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_menu_pai", referencedColumnName="sq_menu")
     * })
     */
    private $sqMenuPai;

    /**
     * @var Sica\Model\Entity\Sistema
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Sistema", inversedBy="sistema")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_sistema", referencedColumnName="sq_sistema")
     * })
     */
    private $sqSistema;

    /**
     * @var string $noMenu
     *
     * @ORM\Column(name="no_menu", type="string", length=50, nullable=false)
     */
    private $noMenu;

    /**
     * @var integer $nuOrdemApresent
     *
     * @ORM\Column(name="nu_ordem_apresent", type="integer", nullable=true)
     */
    private $nuOrdemApresent;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * Get sqMenu
     *
     * @return integer
     */
    public function setSqMenu($sqMenu)
    {
        $this->sqMenu = $sqMenu;
        return $this;
    }

    /**
     * Get sqMenu
     *
     * @return integer
     */
    public function getSqMenu()
    {
        return $this->sqMenu;
    }

    /**
     * Set noMenu
     *
     * @param string $noMenu
     * @return SicaMenu
     */
    public function setNoMenu($noMenu)
    {
        $this->assert('noMenu',$noMenu,$this);
        $this->noMenu = $noMenu;
        return $this;
    }

    /**
     * Get noMenu
     *
     * @return string
     */
    public function getNoMenu()
    {
        return $this->noMenu;
    }

    /**
     * Set nuOrdemApresent
     *
     * @param integer $nuOrdemApresent
     * @return SicaMenu
     */
    public function setNuOrdemApresent($nuOrdemApresent)
    {
        $this->assert('nuOrdemApresent',$nuOrdemApresent,$this);
        $this->nuOrdemApresent = $nuOrdemApresent;
        return $this;
    }

    /**
     * Get nuOrdemApresent
     *
     * @return integer
     */
    public function getNuOrdemApresent()
    {
        return $this->nuOrdemApresent;
    }

    /**
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return SicaMenu
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
     * @param Sica\Model\Entity\SicaSistema $sqSistema
     * @return Menu
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
     * Set sqMenuPai
     *
     * @param Sica\Model\Entity\Menu $sqMenuPai
     * @return Menu
     */
    public function setSqMenuPai(Menu $sqMenuPai = NULL)
    {
        $this->sqMenuPai = $sqMenuPai;
        return $this;
    }

    /**
     * Get sqMenuPai
     *
     * @return Sica\Model\Entity\Menu
     */
    public function getSqMenuPai()
    {
        return $this->sqMenuPai;
    }

}