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
 * Sgdoce\Model\Entity\TipoAssuntoSolicitacao
 *
 * @ORM\Table(name="tipo_assunto_solicitacao")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\TipoAssuntoSolicitacao")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoAssuntoSolicitacao extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoAssuntoSolicitacao
     *
     * @ORM\Column(name="sq_tipo_assunto_solicitacao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoAssuntoSolicitacao;

    /**
     * @var string $noTipoAssuntoSolicitacao
     *
     * @ORM\Column(name="no_tipo_assunto_solicitacao", type="string", length=150, nullable=false)
     */
    private $noTipoAssuntoSolicitacao;

    /**
     * @var boolean $inTipoParaArtefato
     *
     * @ORM\Column(name="in_tipo_para_artefato", type="boolean", nullable=false)
     */
    private $inTipoParaArtefato;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     *
     * @return integer
     */
    public function getSqTipoAssuntoSolicitacao ()
    {
        return $this->sqTipoAssuntoSolicitacao;
    }

    /**
     *
     * @return string
     */
    public function getNoTipoAssuntoSolicitacao ()
    {
        return $this->noTipoAssuntoSolicitacao;
    }

    /**
     *
     * @return boolean
     */
    public function getInTipoParaArtefato ()
    {
        return $this->inTipoParaArtefato;
    }

    /**
     *
     * @return boolean
     */
    public function getStRegostroAtivo()
    {
        return $this->stRegistroAtivo;
    }

    public function setSqTipoAssuntoSolicitacao ($sqTipoAssuntoSolicitacao)
    {
        $this->sqTipoAssuntoSolicitacao = $sqTipoAssuntoSolicitacao;
        return $this;
    }

    public function setNoTipoAssuntoSolicitacao ($noTipoAssuntoSolicitacao)
    {
        $this->noTipoAssuntoSolicitacao = $noTipoAssuntoSolicitacao;
        return $this;
    }

    public function setInTipoParaArtefato ($inTipoParaArtefato)
    {
        $this->inTipoParaArtefato = $inTipoParaArtefato;
        return $this;
    }

    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }


}