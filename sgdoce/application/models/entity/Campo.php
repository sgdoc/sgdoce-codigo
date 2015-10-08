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

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sgdoce\Model\Entity\Campo
 *
 * @ORM\Table(name="campo")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\Campo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Campo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqCampo
     *
     * @ORM\Id
     * @ORM\Column(name="sq_campo", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqCampo;

    /**
     * @var string $noCampo
     *
     * @ORM\Column(name="no_campo", type="string", length=50, nullable=false)
     */
    private $noCampo;

    /**
     * @var string $noOwnerTabela
     *
     * @ORM\Column(name="no_owner_tabela", type="string", length=10, nullable=true)
     */
    private $noOwnerTabela;

    /**
     * @var string $noTabela
     *
     * @ORM\Column(name="no_tabela", type="string", length=30, nullable=true)
     */
    private $noTabela;

    /**
     * @var string $noColunaTabela
     *
     * @ORM\Column(name="no_coluna_tabela", type="string", length=30, nullable=true)
     */
    private $noColunaTabela;

    /**
     * @var Sgdoce\Model\Entity\GrupoCampo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\GrupoCampo")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_grupo_campo", referencedColumnName="sq_grupo_campo")
     * })
     */
    private $sqGrupoCampo;


    /**
     * Get sqCampo
     *
     * @return integer
     */
    public function getSqCampo()
    {
        return $this->sqCampo;
    }

    /**
     * Set noCampo
     *
     * @param string $noCampo
     * @return Campo
     */
    public function setNoCampo($noCampo)
    {
        $this->noCampo = $noCampo;
        return $this;
    }

    /**
     * Get noCampo
     *
     * @return string
     */
    public function getNoCampo()
    {
        return $this->noCampo;
    }

    /**
     * Set noOwnerTabela
     *
     * @param string $noOwnerTabela
     * @return Campo
     */
    public function setNoOwnerTabela($noOwnerTabela)
    {
        $this->noOwnerTabela = $noOwnerTabela;
        return $this;
    }

    /**
     * Get noOwnerTabela
     *
     * @return string
     */
    public function getNoOwnerTabela()
    {
        return $this->noOwnerTabela;
    }

    /**
     * Set noTabela
     *
     * @param string $noTabela
     * @return Campo
     */
    public function setNoTabela($noTabela)
    {
        $this->noTabela = $noTabela;
        return $this;
    }

    /**
     * Get noTabela
     *
     * @return string
     */
    public function getNoTabela()
    {
        return $this->noTabela;
    }

    /**
     * Set noColunaTabela
     *
     * @param string $noColunaTabela
     * @return Campo
     */
    public function setNoColunaTabela($noColunaTabela)
    {
        $this->noColunaTabela = $noColunaTabela;
        return $this;
    }

    /**
     * Get noColunaTabela
     *
     * @return string
     */
    public function getNoColunaTabela()
    {
        return $this->noColunaTabela;
    }

    /**
     * Set sqGrupoCampo
     *
     * @param Sgdoce\Model\Entity\GrupoCampo $sqGrupoCampo
     * @return Campo
     */
    public function setSqGrupoCampo(\Sgdoce\Model\Entity\GrupoCampo $sqGrupoCampo = NULL)
    {
        $this->sqGrupoCampo = $sqGrupoCampo;
        return $this;
    }

    /**
     * Get sqGrupoCampo
     *
     * @return Sgdoce\Model\Entity\GrupoCampo
     */
    public function getSqGrupoCampo()
    {
        return $this->sqGrupoCampo;
    }
}