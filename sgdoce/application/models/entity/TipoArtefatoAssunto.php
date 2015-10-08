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
 * Sgdoce\Model\Entity\TipoArtefatoAssunto
 *
 * @ORM\Table(name="tipo_artefato_assunto")
 * @ORM\Entity
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoArtefatoAssunto extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoArtefatoAssunto
     *
     * @ORM\Column(name="sq_tipo_artefato_assunto", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoArtefatoAssunto;

    /**
     * @var Sgdoce\Model\Entity\Assunto
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Assunto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_assunto", referencedColumnName="sq_assunto")
     * })
     */
    private $sqAssunto;

    /**
     * @var Sgdoce\Model\Entity\TipoArtefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoArtefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_artefato", referencedColumnName="sq_tipo_artefato")
     * })
     */
    private $sqTipoArtefato;


    /**
     * Set sqProjeto
     *
     * @param integer $dtProjeto
     * @return integer
     */

    public function setSqTipoArtefatoAssunto($sqTipoArtefatoAssunto  = NULL)
    {
        $this->sqTipoArtefatoAssunto = $sqTipoArtefatoAssunto;
        if(!$sqTipoArtefatoAssunto){
            $this->sqTipoArtefatoAssunto  = NULL;
        }
        return $this;
    }

    /**
     * Get sqTipoArtefatoAssunto
     *
     * @return integer
     */
    public function getSqTipoArtefatoAssunto()
    {
        return $this->sqTipoArtefatoAssunto;
    }

    /**
     * Set sqAssunto
     *
     * @param Sgdoce\Model\Entity\Assunto $sqAssunto
     * @return TipoArtefatoAssunto
     */
    public function setSqAssunto(\Sgdoce\Model\Entity\Assunto $sqAssunto = NULL)
    {
        $this->sqAssunto = $sqAssunto;
        return $this;
    }

    /**
     * Get sqAssunto
     *
     * @return Sgdoce\Model\Entity\Assunto
     */
    public function getSqAssunto()
    {
        return $this->sqAssunto;
    }

    /**
     * Set sqTipoArtefato
     *
     * @param Sgdoce\Model\Entity\TipoArtefato $sqTipoArtefato
     * @return TipoArtefatoAssunto
     */
    public function setSqTipoArtefato(\Sgdoce\Model\Entity\TipoArtefato $sqTipoArtefato = NULL)
    {
        $this->sqTipoArtefato = $sqTipoArtefato;
        return $this;
    }

    /**
     * Get sqTipoArtefato
     *
     * @return \Sgdoce\Model\Entity\TipoArtefato
     */
    public function getSqTipoArtefato()
    {
        return $this->sqTipoArtefato;
    }
}
