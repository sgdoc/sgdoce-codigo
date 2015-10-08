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
 * SISICMBio
 *
 * Classe para Entity Cep
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Cep
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sica\Model\Entity\Leiaute
 *
 * @ORM\Table(name="leiaute")
 * @ORM\Entity(repositoryClass="\Sica\Model\Repository\Arquitetura")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Leiaute extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqArquitetura
     *
     * @ORM\Column(name="sq_leiaute", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $sqLeiaute;

    /**
     * @var integer $noArquitetura
     *
     * @ORM\Column(name="no_leiaute", type="string", nullable=false)
     *
     */
    private $noLeiaute;

    /**
     * @var integer $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     *
     */
    private $stRegistroAtivo;

    public function setSqLeiaute($sqLeiaute)
    {
        $this->sqLeiaute = $sqLeiaute;
        return $this;
    }

    public function getSqLeiaute()
    {
        return $this->sqLeiaute;
    }

    public function setNoLeiaute($noLeiaute)
    {
        $this->noLeiaute = $noLeiaute;
        return $this;
    }

    public function getNoLeiaute()
    {
        return $this->noLeiaute;
    }

    public function setRegistroAtivo($registroAtivo)
    {
        $this->stRegistroAtivo = $registroAtivo;
        return $this;
    }

    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }
}