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
 * Sgdoce\Model\Entity\PosicaoData
 *
 * @ORM\Table(name="posicao_data")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\PosicaoData")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class PosicaoData extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqPosicaoData
     *
     * @ORM\Column(name="sq_posicao_data", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPosicaoData;

    /**
     * @var string $noPosicaoData
     *
     * @ORM\Column(name="no_posicao_data", type="string", length=35, nullable=false)
     */
    private $noPosicaoData;

    /**
     * Get sqPosicaoData
     *
     * @return integer
     */
    public function getSqPosicaoData()
    {
        return $this->sqPosicaoData;
    }

    /**
     * Set sqPosicaoData
     *
     * @param $sqPosicaoData
     * @return PosicaoData
     */
    public function setSqPosicaoData($sqPosicaoData = NULL)
    {
        $this->sqPosicaoData = $sqPosicaoData;
        if(!$sqPosicaoData){
            $this->sqPosicaoData  = NULL;
        }
        return $this;
    }

    /**
     * Set noPosicaoData
     *
     * @param string $noPosicaoData
     * @return PosicaoData
     */
    public function setNoPosicaoData($noPosicaoData)
    {
        $this->noPosicaoData = $noPosicaoData;
        return $this;
    }

    /**
     * Get noPosicaoData
     *
     * @return string
     */
    public function getNoPosicaoData()
    {
        return $this->noPosicaoData;
    }
}