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
 * Sgdoce\Model\Entity\GrauAcesso
 *
 * @ORM\Table(name="grau_acesso")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\GrauAcesso")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class GrauAcesso extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqGrauAcesso
     *
     * @ORM\Id
     * @ORM\Column(name="sq_grau_acesso", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqGrauAcesso;

    /**
     * @var string $noGrauAcesso
     *
     * @ORM\Column(name="no_grau_acesso", type="string", length=20, nullable=false)
     */
    private $noGrauAcesso;

    /**
     * @ORM\Column(name="nu_nivel", type="integer", nullable=false)
     */
    private $nuNivel;

    /**
     * @var boolean $stAtivo
     *
     * @ORM\Column(name="st_ativo", type="boolean", nullable=false)
     */
    private $stAtivo;


    /**
     * Get sqGrauAcesso
     *
     * @return integer
     */
    public function getSqGrauAcesso()
    {
        return $this->sqGrauAcesso;
    }

    /**
     * Set sqGrauAcesso
     *
     * @param string $sqGrauAcesso
     * @return GrauAcesso
     */
    public function setSqGrauAcesso($sqGrauAcesso)
    {
        $this->sqGrauAcesso = $sqGrauAcesso;
        return $this;
    }

    /**
     * Set noGrauAcesso
     *
     * @param string $noGrauAcesso
     * @return GrauAcesso
     */
    public function setNoGrauAcesso($noGrauAcesso)
    {
        $this->assert('noGrauAcesso',$noGrauAcesso,$this);
        $this->noGrauAcesso = $noGrauAcesso;
        return $this;
    }

    /**
     * Get noGrauAcesso
     *
     * @return string
     * */
    public function getNoGrauAcesso()
    {
        return $this->noGrauAcesso;
    }

    function getNuNivel ()
    {
        return $this->nuNivel;
    }

    function setNuNivel ($nuNivel)
    {
        $this->nuNivel = $nuNivel;
        return $this;
    }

    /**
     * Get stAtivo
     *
     * @return boolean
     */
    public function getStAtivo()
    {
        return $this->stAtivo;
    }

    /**
     * Set stAtivo
     *
     * @param boolean $stAtivo
     * @return GrauAcesso
     */
    public function setStAtivo($stAtivo)
    {
        $this->stAtivo = $stAtivo;
        return $this;
    }
}