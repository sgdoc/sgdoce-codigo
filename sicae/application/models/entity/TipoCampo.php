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
 * Sica\Model\Entity\SicaTipoCampo
 *
 * @ORM\Table(name="tipo_campo")
 * @ORM\Entity
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoCampo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoCampo
     *
     * @ORM\Column(name="sq_tipo_campo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoCampo;

    /**
     * @var string $noTipoCampo
     *
     * @ORM\Column(name="no_tipo_campo", type="string", length=20, nullable=false)
     */
    private $noTipoCampo;


    /**
     * Get sqTipoCampo
     *
     * @return integer
     */
    public function getSqTipoCampo()
    {
        return $this->sqTipoCampo;
    }

    /**
     * Set noTipoCampo
     *
     * @param string $noTipoCampo
     * @return SicaTipoCampo
     */
    public function setNoTipoCampo($noTipoCampo)
    {
        $this->noTipoCampo = $noTipoCampo;
        return $this;
    }

    /**
     * Get noTipoCampo
     *
     * @return string
     */
    public function getNoTipoCampo()
    {
        return $this->noTipoCampo;
    }
}