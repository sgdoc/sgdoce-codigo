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
 * \Sgdoce\Model\Entity\TipoHistoricoArquivo
 *
 * @ORM\Table(name="tipo_historico_arquivo")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\TipoHistoricoArquivo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoHistoricoArquivo extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqTipoHistoricoArquivo
     *
     * @ORM\Id
     * @ORM\Column(name="sq_tipo_historico_arquivo", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoHistoricoArquivo;

    /**
     * @var zenddate $dsTipoHistoricoArquivo
     * @ORM\Column(name="ds_tipo_historico_arquivo", type="string", length=20, nullable=false)
     */
    private $dsTipoHistoricoArquivo;

    /**
     *
     * @return integer
     */
    public function getSqTipoHistoricoArquivo ()
    {
        return $this->sqTipoHistoricoArquivo;
    }


    /**
     *
     * @return string
     */
    public function getDsTipoHistoricoArquivo ()
    {
        return $this->dsTipoHistoricoArquivo;
    }

    public function setSqTipoHistoricoArquivo ($sqTipoHistoricoArquivo)
    {
        $this->sqTipoHistoricoArquivo = $sqTipoHistoricoArquivo;
        return $this;
    }

    public function setDsTipoHistoricoArquivo ($dsTipoHistoricoArquivo)
    {
        $this->dsTipoHistoricoArquivo = $dsTipoHistoricoArquivo;
        return $this;
    }





}