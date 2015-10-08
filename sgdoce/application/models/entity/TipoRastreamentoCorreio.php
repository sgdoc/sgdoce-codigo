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
 * Sgdoce\Model\Entity\StatusArtefato
 *
 * @ORM\Table(name="tipo_rastreamento_correio")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\TipoRastreamentoCorreio")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoRastreamentoCorreio extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoRastreamentoCorreio
     *
     * @ORM\Column(name="sq_tipo_rastreamento_correio", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoRastreamentoCorreio;

    /**
     * @var string $noTipoRastreamentoCorreio
     *
     * @ORM\Column(name="no_tipo_rastreamento_correio", type="string", length=20, nullable=false)
     */
    private $noTipoRastreamentoCorreio;

    /**
     * Set sqStatusArtefato
     *
     * @param integer $sqTipoRastreamentoCorreio
     * @return self
     */
    public function setSqTipoRastreamentoCorreio($sqTipoRastreamentoCorreio)
    {
        $this->sqTipoRastreamentoCorreio = $sqTipoRastreamentoCorreio;
        return $this;
    }

    /**
     * Set sqStatusArtefato
     *
     * @param integer $sqTipoRastreamentoCorreio
     * @return self
     */
    public function setSqTipoRastreamento($sqTipoRastreamentoCorreio)
    {
        $this->setSqTipoRastreamento($sqTipoRastreamentoCorreio);
        return $this;
    }

    /**
     * Get sqTipoRastreamentoCorreio
     *
     * @return integer
     */
    public function getSqTipoRastreamentoCorreio()
    {
        return $this->sqTipoRastreamentoCorreio;
    }

    /**
     * Set noTipoRastreamentoCorreio
     *
     * @param string $noTipoRastreamentoCorreio
     * @return self
     */
    public function setNTipoRastreamentoCorreio($noTipoRastreamentoCorreio)
    {
        $this->noTipoRastreamentoCorreio = $noTipoRastreamentoCorreio;
        return $this;
    }

    /**
     * Get noTipoRastreamentoCorreio
     *
     * @return string
     */
    public function getNoTipoRastreamentoCorreio()
    {
        return $this->noTipoRastreamentoCorreio;
    }
}