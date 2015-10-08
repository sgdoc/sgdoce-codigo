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
 * Sica\Model\Entity\Funcionalidade
 *
 * @ORM\Table(name="funcionalidade")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Funcionalidade")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Funcionalidade extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqFuncionalidade
     *
     * @ORM\Column(name="sq_funcionalidade", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqFuncionalidade;

    /**
     * @var string $noFuncionalidade
     *
     * @ORM\Column(name="no_funcionalidade", type="string", length=80, nullable=false)
     */
    private $noFuncionalidade;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=true)
     */
    private $stRegistroAtivo;

    /**
     * @var boolean $inFuncionalidadePrincipal
     *
     * @ORM\Column(name="in_funcionalidade_principal", type="boolean", nullable=false)
     */
    private $inFuncionalidadePrincipal = FALSE;

    /**
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Rota", mappedBy="sqFuncionalidade")
     */
    private $rotas;

    /**
     * @var Sica\Model\Entity\MenuHierarqManter
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\MenuHierarqManter", mappedBy="sqMenu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_menu", referencedColumnName="sq_menu")
     * })
     */
    private $sqMenuHierarquico;

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
     * @var Sica\Model\Entity\Menu
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Menu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_menu", referencedColumnName="sq_menu")
     * })
     */
    private $sqMenu;

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
     * @return integer
     */
    public function getSqFuncionalidade()
    {
        return $this->sqFuncionalidade;
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
     *
     * @return string
     */
    public function getInFuncionalidadePrincipal()
    {
        return $this->inFuncionalidadePrincipal;
    }

    public function addRota(Rota $rota)
    {
        $rotas = $this->getRotas();
        $rotas->add($rota);

        return $this;
    }

    public function getRotas()
    {
        if (NULL === $this->rotas) {
            $this->rotas =  new \Doctrine\Common\Collections\ArrayCollection();
        }
        return $this->rotas;
    }

    public function setSqRotaPrincipal(Rota $rota = null)
    {
        $this->sqRotaPrincipal = $rota;
        return $this;
    }

    public function getSqRotaPrincipal()
    {
        if (NULL === $this->sqRotaPrincipal) {
            $this->setSqRotaPrincipal(new Rota());
        }

        return $this->sqRotaPrincipal;
    }
}
