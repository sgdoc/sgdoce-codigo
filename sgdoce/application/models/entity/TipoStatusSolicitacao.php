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
 * Sgdoce\Model\Entity\TipoStatusSolicitacao
 *
 * @ORM\Table(name="tipo_status_solicitacao")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\TipoStatusSolicitacao")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoStatusSolicitacao extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoStatusSolicitacao
     *
     * @ORM\Column(name="sq_tipo_status_solicitacao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoStatusSolicitacao;

    /**
     * @var string $noTipoStatusSolicitacao
     *
     * @ORM\Column(name="no_tipo_status_solicitacao", type="string", length=150, nullable=false)
     */
    private $noTipoStatusSolicitacao;


    public function getSqStatusSolicitacao ()
    {
        return $this->sqTipoStatusSolicitacao;
    }

    public function getSqTipoStatusSolicitacao ()
    {
        return $this->sqTipoStatusSolicitacao;
    }

    public function getNoTipoStatusSolicitacao ()
    {
        return $this->noTipoStatusSolicitacao;
    }

    public function setSqTipoStatusSolicitacao ($sqTipoStatusSolicitacao)
    {
        $this->sqTipoStatusSolicitacao = $sqTipoStatusSolicitacao;
        return $this;
    }

    public function setNoTipoStatusSolicitacao ($noTipoStatusSolicitacao)
    {
        $this->noTipoStatusSolicitacao = $noTipoStatusSolicitacao;
        return $this;
    }



}