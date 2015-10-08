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
 * Sica\Model\Entity\MontarMenu
 *
 * @ORM\Table(name="vw_montar_menu")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\MontarMenu", readOnly=true)
 */
class MontarMenu extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqMenuPai
     *@ORM\Id
     * @ORM\Column(name="sqmenupai", type="integer", nullable=false)
     */
    private $sqMenuPai;

    /**
     * @var string $noMenu
     *
     * @ORM\Column(name="nomenupai", type="string", nullable=false)
     */
    private $noMenuPai;

    /**
     * @var boolean $ativoPai
     *
     * @ORM\Column(name="ativopai", type="boolean", nullable=false)
     */
    private $ativoPai;

    /**
     * @var integer $ordemPai
     *
     * @ORM\Column(name="ordempai", type="integer", nullable=false)
     */
    private $ordemPai;

    /**
     * @var string $funcionalidadePai
     *
     * @ORM\Column(name="funcionalidadepai", type="string", nullable=false)
     */
    private $funcionalidadePai;

    /**
     * @var string $txRotaPai
     *
     * @ORM\Column(name="txrotapai", type="string", nullable=false)
     */
    private $txRotaPai;

    /**
     * @var integer $sqMenuNivel2
     *
     * @ORM\Column(name="sqmenunivel2", type="integer", nullable=false)
     */
    private $sqMenuNivel2;

    /**
     * @var string $menuNivel2
     *
     * @ORM\Column(name="menunivel2", type="string", nullable=false)
     */
    private $menuNivel2;

    /**
     * @var boolean $ativoNivel2
     *
     * @ORM\Column(name="ativonivel2", type="boolean", nullable=false)
     */
    private $ativoNivel2;

    /**
     * @var integer $ordemNivel2
     *
     * @ORM\Column(name="ordemnivel2", type="integer", nullable=false)
     */
    private $ordemNivel2;

    /**
     * @var integer $funcionalidadeNivel2
     *
     * @ORM\Column(name="funcionalidadenivel2", type="integer", nullable=false)
     */
    private $funcionalidadeNivel2;

    /**
     * @var string $txRotaNivel2
     *
     * @ORM\Column(name="txrotanivel2", type="string", nullable=false)
     */
    private $txRotaNivel2;

    /**
     * @var integer $sqMenuNivel3
     *
     * @ORM\Column(name="sqmenunivel3", type="integer", nullable=false)
     */
    private $sqMenuNivel3;

    /**
     * @var string $menuNivel3
     *
     * @ORM\Column(name="menunivel3", type="string", nullable=false)
     */
    private $menuNivel3;

    /**
     * @var boolean $ativoNivel3
     *
     * @ORM\Column(name="ativonivel3", type="boolean", nullable=false)
     */
    private $ativoNivel3;

    /**
     * @var integer $ordemNivel3
     *
     * @ORM\Column(name="ordemnivel3", type="integer", nullable=false)
     */
    private $ordemNivel3;

    /**
     * @var integer $funcionalidadeNivel3
     *
     * @ORM\Column(name="funcionalidadenivel3", type="integer", nullable=false)
     */
    private $funcionalidadeNivel3;

    /**
     * @var string $txRotaNivel3
     *
     * @ORM\Column(name="txrotanivel3", type="string", nullable=false)
     */
    private $txRotaNivel3;
    /**
     * @var integer $sqMenuNivel4
     *
     * @ORM\Column(name="sqmenunivel4", type="integer", nullable=false)
     */
    private $sqMenuNivel4;

    /**
     * @var string $menuNivel4
     *
     * @ORM\Column(name="menunivel4", type="string", nullable=false)
     */
    private $menuNivel4;

    /**
     * @var boolean $ativoNivel4
     *
     * @ORM\Column(name="ativonivel4", type="boolean", nullable=false)
     */
    private $ativoNivel4;

    /**
     * @var integer $ordemNivel4
     *
     * @ORM\Column(name="ordemnivel4", type="integer", nullable=false)
     */
    private $ordemNivel4;

    /**
     * @var integer $funcionalidadeNivel4
     *
     * @ORM\Column(name="funcionalidadenivel4", type="integer", nullable=false)
     */
    private $funcionalidadeNivel4;

    /**
     * @var string $txRotaNivel4
     *
     * @ORM\Column(name="txrotanivel4", type="string", nullable=false)
     */
    private $txRotaNivel4;

    /**
     * @var string $sqPerfil1
     *
     * @ORM\Column(name="sqperfil1", type="integer", nullable=false)
     */
    private $sqPerfil1;

    /**
     * @var string $sqPerfil2
     *
     * @ORM\Column(name="sqperfil2", type="integer", nullable=false)
     */
    private $sqPerfil2;

    /**
     * @var string $sqPerfil3
     *
     * @ORM\Column(name="sqperfil3", type="integer", nullable=false)
     */
    private $sqPerfil3;

    /**
     * @var string $sqPerfil4
     *
     * @ORM\Column(name="sqperfil4", type="integer", nullable=false)
     */
    private $sqPerfil4;

    /**
     * Set sqSistema
     *
     * @param $sqMenuPai
     * @return Menu
     */
    public function setSqMenuPai($sqMenuPai)
    {
        $this->sqMenuPai = $sqMenuPai;
        return $this;
    }

    /**
     * Get sqMenuPai
     *
     * @return
     */
    public function getSqMenuPai()
    {
        return $this->sqMenuPai;
    }

    /**
     * Set $noMenuPai
     *
     * @param $noMenuPai
     * @return Menu
     */
    public function setNoMenuPai($noMenuPai)
    {
        $this->noMenuPai = $noMenuPai;
        return $this;
    }

    /**
     * Get sqMenuPai
     *
     * @return
     */
    public function getNoMenuPai()
    {
        return $this->noMenuPai;
    }

    /**
     * Set $ativoPai
     *
     * @param $ativoPai
     * @return Menu
     */
    public function setAtivoPai($ativoPai)
    {
        $this->ativoPai = $ativoPai;
        return $this;
    }

    /**
     * Get $ativoPai
     *
     * @return
     */
    public function getAtivoPai()
    {
        return $this->ativoPai;
    }

    /**
     * Set $ordemPai
     *
     * @param $ordemPai
     * @return Menu
     */
    public function setOrdemPai($ordemPai)
    {
        $this->ordemPai = $ordemPai;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getOrdemPai()
    {
        return $this->ordemPai;
    }

    /**
     * Set $funcionalidadePai
     *
     * @param $funcionalidadePai
     * @return Menu
     */
    public function setFuncionalidadePai($funcionalidadePai)
    {
        $this->funcionalidadePai = $funcionalidadePai;
        return $this;
    }

    /**
     * Get $funcionalidadePai
     *
     * @return Menu
     */
    public function getFuncionalidadePai()
    {
        return $this->funcionalidadePai;
    }

    /**
     * Set $txRotaPai
     *
     * @param $txRotaPai
     * @return Menu
     */
    public function setTxRotaPai($txRotaPai)
    {
        $this->txRotaPai = $txRotaPai;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getTxRotaPai()
    {
        return $this->txRotaPai;
    }

    /**
     * Set $sqMenuNivel2
     *
     * @param $sqMenuNivel2
     * @return Menu
     */
    public function setSqMenuNivel2($sqMenuNivel2)
    {
        $this->sqMenuNivel2 = $sqMenuNivel2;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getSqMenuNivel2()
    {
        return $this->sqMenuNivel2;
    }

    /**
     * Set $menunivel2
     *
     * @param $menunivel2
     * @return Menu
     */
    public function setMenuNivel2($menuNivel2)
    {
        $this->menuNivel2 = $menuNivel2;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getMenuNivel2()
    {
        return $this->menuNivel2;
    }

    /**
     * Set $ativoNivel2
     *
     * @param $ativoNivel2
     * @return Menu
     */
    public function setAtivoNivel2($ativoNivel2)
    {
        $this->ativoNivel2 = $ativoNivel2;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getAtivoNivel2()
    {
        return $this->ativoNivel2;
    }

    /**
     * Set $ordemNivel2
     *
     * @param $ordemNivel2
     * @return Menu
     */
    public function setOrdemNivel2($ordemNivel2)
    {
        $this->ordemNivel2 = $ordemNivel2;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getOrdemNivel2()
    {
        return $this->ordemNivel2;
    }

    /**
     * Set $funcionalidadeNivel2
     *
     * @param $funcionalidadeNivel2
     * @return Menu
     */
    public function setFuncionalidadeNivel2($funcionalidadeNivel2)
    {
        $this->funcionalidadeNivel2 = $funcionalidadeNivel2;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getFuncionalidadeNivel2()
    {
        return $this->funcionalidadeNivel2;
    }

    /**
     * Set $txRotaNivel2
     *
     * @param $txRotaNivel2
     * @return Menu
     */
    public function setTxRotaNivel2($txRotaNivel2)
    {
        $this->txRotaNivel2 = $txRotaNivel2;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getTxRotaNivel2()
    {
        return $this->txRotaNivel2;
    }


    /**
     * Set $sqMenuNivel3
     *
     * @param $sqMenuNivel3
     * @return Menu
     */
    public function setSqMenuNivel3($sqMenuNivel3)
    {
        $this->sqMenuNivel3 = $sqMenuNivel3;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getSqMenuNivel3()
    {
        return $this->sqMenuNivel3;
    }

    /**
     * Set $menunivel3
     *
     * @param $menunivel3
     * @return Menu
     */
    public function setMenuNivel3($menuNivel3)
    {
        $this->menuNivel3 = $menuNivel3;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getMenuNivel3()
    {
        return $this->menuNivel3;
    }

    /**
     * Set $ativoNivel3
     *
     * @param $ativoNivel3
     * @return Menu
     */
    public function setAtivoNivel3($ativoNivel3)
    {
        $this->ativoNivel3 = $ativoNivel3;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getAtivoNivel3()
    {
        return $this->ativoNivel3;
    }

    /**
     * Set $ordemNivel3
     *
     * @param $ordemNivel3
     * @return Menu
     */
    public function setOrdemNivel3($ordemNivel3)
    {
        $this->ordemNivel3 = $ordemNivel3;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getOrdemNivel3()
    {
        return $this->ordemNivel3;
    }

    /**
     * Set $funcionalidadeNivel3
     *
     * @param $funcionalidadeNivel3
     * @return Menu
     */
    public function setFuncionalidadeNivel3($funcionalidadeNivel3)
    {
        $this->funcionalidadeNivel3 = $funcionalidadeNivel3;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getFuncionalidadeNivel3()
    {
        return $this->funcionalidadeNivel3;
    }

    /**
     * Set $txRotaNivel3
     *
     * @param $txRotaNivel3
     * @return Menu
     */
    public function setTxRotaNivel3($txRotaNivel3)
    {
        $this->txRotaNivel3 = $txRotaNivel3;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getTxRotaNivel3()
    {
        return $this->txRotaNivel3;
    }

    /**
     * Set $sqPerfil1
     *
     * @param $sqPerfil1
     * @return Menu
     */
    public function setSqPerfil1($sqPerfil1)
    {
        $this->sqPerfil1 = $sqPerfil1;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getSqPerfil1()
    {
        return $this->sqPerfil1;
    }

    /**
     * Set $sqPerfil2
     *
     * @param $sqPerfil2
     * @return Menu
     */
    public function setSqPerfil2($sqPerfil2)
    {
        $this->sqPerfil2 = $sqPerfil2;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getSqPerfil2()
    {
        return $this->sqPerfil2;
    }

    /**
     * Set $sqPerfil3
     *
     * @param $sqPerfil3
     * @return Menu
     */
    public function setSqPerfil3($sqPerfil3)
    {
        $this->sqPerfil3 = $sqPerfil3;
        return $this;
    }

    /**
     * Get
     *
     * @return
     */
    public function getSqPerfil3()
    {
        return $this->sqPerfil3;
    }

    /**
     * @param boolean $ativoNivel4
     */
    public function setAtivoNivel4($ativoNivel4)
    {
        $this->ativoNivel4 = $ativoNivel4;
    }

    /**
     * @return boolean
     */
    public function getAtivoNivel4()
    {
        return $this->ativoNivel4;
    }
}