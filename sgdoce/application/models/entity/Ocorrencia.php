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
 * Sgdoce\Model\Entity\Ocorrencia
 *
 * @ORM\Table(name="ocorrencia")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Ocorrencia")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Ocorrencia extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqOcorrencia
     *
     * @ORM\Column(name="sq_ocorrencia", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqOcorrencia;

    /**
     * @var string $noOcorrencia
     *
     * @ORM\Column(name="no_ocorrencia", type="string", length=50, nullable=false)
     */
    private $noOcorrencia;

    /**
     * @var Sgdoce\Model\Entity\SgdoceOcorrencia
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Ocorrencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_ocorrencia_canceladora", referencedColumnName="sq_ocorrencia")
     * })
     */
    private $sqOcorrenciaCanceladora;

    /**
     * Set sqOcorrencia
     *
     * @param string $sqOcorrencia
     * @return Ocorrencia
     */
    public function setSqOcorrencia($sqOcorrencia)
    {
        $this->sqOcorrencia = $sqOcorrencia;
        return $this;
    }

    /**
     * Get sqOcorrencia
     *
     * @return integer
     */
    public function getSqOcorrencia()
    {
        return $this->sqOcorrencia;
    }

    /**
     * Set noOcorrencia
     *
     * @param string $noOcorrencia
     * @return Ocorrencia
     */
    public function setNoOcorrencia($noOcorrencia)
    {
        $this->noOcorrencia = $noOcorrencia;
        return $this;
    }

    /**
     * Get noOcorrencia
     *
     * @return string
     */
    public function getNoOcorrencia()
    {
        return $this->noOcorrencia;
    }

    /**
     * Set sqOcorrenciaCanceladora
     *
     * @param Sgdoce\Model\Entity\SgdoceOcorrencia $sqOcorrenciaCanceladora
     * @return Ocorrencia
     */
    public function setSqOcorrenciaCanceladora(\Sgdoce\Model\Entity\Ocorrencia $sqOcorrenciaCanceladora = NULL)
    {
        $this->sqOcorrenciaCanceladora = $sqOcorrenciaCanceladora;
        return $this;
    }

    /**
     * Get sqOcorrenciaCanceladora
     *
     * @return Sgdoce\Model\Entity\Ocorrencia
     */
    public function getSqOcorrenciaCanceladora()
    {
        return $this->sqOcorrenciaCanceladora;
    }
}