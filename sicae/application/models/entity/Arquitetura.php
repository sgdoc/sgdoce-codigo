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
 * Sica\Model\Entity\Cep
 *
 * @ORM\Table(name="arquitetura")
 * @ORM\Entity(repositoryClass="\Sica\Model\Repository\Arquitetura")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Arquitetura extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqArquitetura
     *
     * @ORM\Column(name="sq_arquitetura", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $sqArquitetura;

    /**
     * @var integer $noArquitetura
     *
     * @ORM\Column(name="no_arquitetura", type="string", nullable=false)
     *
     */
    private $noArquitetura;

    /**
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Sistema", mappedBy="sqArquitetura")
     */
    private $arquiteturas;

    /**
     * @var integer $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     *
     */
    private $stRegistroAtivo;

    public function setSqArquitetura($sqArquitetura)
    {
        $this->sqArquitetura = $sqArquitetura;
        return $this;
    }

    public function getSqArquitetura()
    {
        return $this->sqArquitetura;
    }

    public function setNoArquitetura($noArquitetura)
    {
        $this->noArquitetura = $noArquitetura;
        return $this;
    }

    public function getNoArquitetura()
    {
        return $this->noArquitetura;
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