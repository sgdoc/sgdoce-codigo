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
 * Sgdoce\Model\Entity\PosicaoTipoDocumento
 *
 * @ORM\Table(name="posicao_tipo_documento")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\PosicaoTipoDocumento")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class PosicaoTipoDocumento extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqPosicaoTipoDocumento
     *
     * @ORM\Column(name="sq_posicao_tipo_documento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPosicaoTipoDocumento;

    /**
     * @var string $noPosicaoTipoDocumento
     *
     * @ORM\Column(name="no_posicao_tipo_documento", type="string", length=20, nullable=false)
     */
    private $noPosicaoTipoDocumento;

    /**
     * Get sqPosicaoTipoDocumento
     *
     * @return integer
     */
    public function getSqPosicaoTipoDocumento()
    {
        return $this->sqPosicaoTipoDocumento;
    }

    /**
     * Set sqPosicaoTipoDocumento
     *
     * @param $sqPosicaoTipoDocumento
     * @return PosicaoTipoDocumento
     */
    public function setSqPosicaoTipoDocumento($sqPosicaoTipoDocumento)
    {
        $this->sqPosicaoTipoDocumento = $sqPosicaoTipoDocumento;
        if(!$sqPosicaoTipoDocumento){
            $this->sqPosicaoTipoDocumento  = NULL;
        }
        return $this;
    }

    /**
     * Set noPosicaoTipoDocumento
     *
     * @param string $noPosicaoTipoDocumento
     * @return PosicaoTipoDocumento
     */
    public function setNoPosicaoTipoDocumento($noPosicaoTipoDocumento)
    {
        $this->noPosicaoTipoDocumento = $noPosicaoTipoDocumento;
        return $this;
    }

    /**
     * Get noPosicaoTipoDocumento
     *
     * @return string
     */
    public function getNoPosicaoTipoDocumento()
    {
        return $this->noPosicaoTipoDocumento;
    }
}