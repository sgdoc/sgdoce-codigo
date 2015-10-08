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
 * Sgdoce\Model\Entity\TipoVinculoArtefato
 *
 * @ORM\Table(name="tipo_vinculo_artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\TipoVinculoArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoVinculoArtefato extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoVinculoArtefato
     *
     * @ORM\Column(name="sq_tipo_vinculo_artefato", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoVinculoArtefato;

    /**
     * @var string $noTipoVinculoArtefato
     *
     * @ORM\Column(name="no_tipo_vinculo_artefato", type="string", length=50, nullable=false)
     */
    private $noTipoVinculoArtefato;

    /**
     * @var boolean $inPermiteDesvinculacao
     *
     * @ORM\Column(name="in_permite_desvinculacao", type="boolean", nullable=false)
     */
    private $inPermiteDesvinculacao;

    /**
     * Get sqTipoVinculoArtefato
     *
     * @return integer
     */
    public function setSqTipoVinculoArtefato($sqTipoVinculoArtefato)
    {
        $this->sqTipoVinculoArtefato = $sqTipoVinculoArtefato;
        return $this;
    }

    /**
     * Get sqTipoVinculoArtefato
     *
     * @return integer
     */
    public function getSqTipoVinculoArtefato()
    {
        return $this->sqTipoVinculoArtefato;
    }

    /**
     * Set noTipoVinculoArtefato
     *
     * @param string $noTipoVinculoArtefato
     * @return TipoVinculoArtefato
     */
    public function setNoTipoVinculoArtefato($noTipoVinculoArtefato)
    {
        $this->noTipoVinculoArtefato = $noTipoVinculoArtefato;
        return $this;
    }

    /**
     * Get noTipoVinculoArtefato
     *
     * @return string
     */
    public function getNoTipoVinculoArtefato()
    {
        return $this->noTipoVinculoArtefato;
    }

    /**
     * Set inPermiteDesvinculacao
     *
     * @param boolean $inPermiteDesvinculacao
     * @return TipoVinculoArtefato
     */
    public function setInPermiteDesvinculacao($inPermiteDesvinculacao)
    {
        $this->inPermiteDesvinculacao = $inPermiteDesvinculacao;
        return $this;
    }

    /**
     * Get inPermiteDesvinculacao
     *
     * @return boolean
     */
    public function getInPermiteDesvinculacao()
    {
        return $this->inPermiteDesvinculacao;
    }
}