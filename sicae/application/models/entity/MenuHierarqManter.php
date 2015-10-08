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
 * SISICMBio
 *
 * Classe para Entity MenuHierarqManter
 *
 * @package      Model
 * @subpackage   Entity
 * @name         MenuHierarqManter
 * @version      1.0.0
 * @since        2012-08-22
 */

/**
 * Sica\Model\Entity\MenuHierarqManter
 *
 * @ORM\Table(name="vw_menu_hierarq_manter")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\MenuHierarqManter", readOnly=true)
 */
class MenuHierarqManter extends \Core_Model_Entity_Abstract
{

    /**
     * @var Sica\Model\Entity\Sistema
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Sistema", inversedBy="sqSistema")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_sistema", referencedColumnName="sq_sistema")
     * })
     */
    private $sqSistema;

    /**
     * @var integer $sqMenu
     *@ORM\Id
     * @ORM\Column(name="sq_menu", type="integer", nullable=false)
     */
    private $sqMenu;

    /**
     * @var string $noMenu
     *
     * @ORM\Column(name="sq_menu_pai", type="integer", nullable=false)
     */
    private $sqMenuPai;

    /**
     * @var string $noMenu
     *
     * @ORM\Column(name="no_menu", type="string", nullable=false)
     */
    private $noMenu;

    /**
     * @var integer $nuOrdemApresent
     *
     * @ORM\Column(name="nu_ordem_apresent", type="integer", nullable=false)
     */
    private $nuOrdemApresent;

    /**
     * @var integer $nuNivel
     *
     * @ORM\Column(name="nu_nivel", type="integer", nullable=false)
     */
    private $nuNivel;

    /**
     * @var integer $ordenacao
     *
     * @ORM\Column(name="ordenacao", type="textarray", nullable=false)
     */
    private $ordenacao;

    /**
     * @var integer $ordenacao
     *
     * @ORM\Column(name="ordenacao_sq", type="textarray", nullable=false)
     */
    private $ordenacaoSq;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * @var integer $nuQuantidadeFuncionalidade
     *
     * @ORM\Column(name="nu_quantidade_funcionalidade", type="integer")
     */
    private $nuQuantidadeFuncionalidade;

    /**
     * @var boolean $removeDaLista
     *
     * Propriedade apenas para consulta serve para dizer se remove menu corrente da lista de ordenação
     */
    private $removeDaLista;

    /**
     * @var integer $menuLista
     *
     * Propriedade apenas para consulta serve para dizer se qual menu remover da lista de ordenação
     */
    private $sqMenuLista;

    /**
     * Set sqSistema
     *
     * @param Sica\Model\Entity\SicaSistema $sqSistema
     * @return Sica\Model\Entity\MenuHierarqManter
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
     * Set sqMenu
     *
     * @return Sica\Model\Entity\MenuHierarqManter
     */
    public function setSqMenu($sqMenu)
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
        return $this->sqMenu;
    }

    /**
     * Set sqMenuPai
     *
     * @param Sica\Model\Entity\Menu $sqMenuPai
     * @return Sica\Model\Entity\MenuHierarqManter
     */
    public function setSqMenuPai($sqMenuPai = NULL)
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

    /**
     * Set noMenu
     *
     * @param string $noMenu
     * @return Sica\Model\Entity\MenuHierarqManter
     */
    public function setNoMenu($noMenu)
    {
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
     * @return Sica\Model\Entity\MenuHierarqManter
     */
    public function setNuOrdemApresent($nuOrdemApresent)
    {
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
     * Set sqFuncionalidade
     *
     * @return Sica\Model\Entity\MenuHierarqManter
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
     * Set nuNivel
     *
     * @return Sica\Model\Entity\MenuHierarqManter
     */
    public function setNuNivel($nuNivel)
    {
        $this->nuNivel = $nuNivel;
        return $this;
    }

    /**
     * Get nuNivel
     *
     * @return integer
     */
    public function getNuNivel()
    {
        return $this->nuNivel;
    }

    /**
     * Set txRota
     *
     * @return Sica\Model\Entity\MenuHierarqManter
     */
    public function setTxRota($txRota)
    {
        $this->txRota = $txRota;
        return $this;
    }

    /**
     * Get noMenu
     *
     * @return string
     */
    public function getTxRota()
    {
        return $this->txRota;
    }

    /**
     * Set ordenacao
     *
     * @return Sica\Model\Entity\MenuHierarqManter
     */
    public function setOrdenacao($ordenacao)
    {
        $this->ordenacao = $ordenacao;
        return $this;
    }

    /**
     * Get noMenu
     *
     * @return string
     */
    public function getordenacao()
    {
        return $this->ordenacao;
    }

    /**
     * Set $stRegistroAtivo
     *
     * @return Sica\Model\Entity\MenuHierarqManter
     */
    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * Get sqSistema
     *
     * @return integer
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }

    /**
     * Set nuQuantidadeFuncionalidade
     *
     * @param integer $nuQuantidadeFuncionalidade
     * @return Sica\Model\Entity\MenuHierarqManter
     */
    public function setNuQuantidadeFuncionalidade($nuQuantidadeFuncionalidade)
    {
        $this->nuQuantidadeFuncionalidade = $nuQuantidadeFuncionalidade;
        return $this;
    }

    /**
     * Get nuQuantidadeFuncionalidade
     *
     * @return integer
     */
    public function getNuQuantidadeFuncionalidade()
    {
        return $this->nuQuantidadeFuncionalidade;
    }

    /**
     * @param boolean $removeDaLista
     */
    public function setRemoveDaLista($removeDaLista)
    {
        $this->removeDaLista = $removeDaLista;
    }

    /**
     * @return boolean
     */
    public function getRemoveDaLista()
    {
        return $this->removeDaLista;
    }

    /**
     * @param int $sqMenuLista
     */
    public function setSqMenuLista($sqMenuLista)
    {
        $this->sqMenuLista = $sqMenuLista;
    }

    /**
     * @return int
     */
    public function getSqMenuLista()
    {
        return $this->sqMenuLista;
    }
}