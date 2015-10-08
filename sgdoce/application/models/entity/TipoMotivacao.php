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
 * Sgdoce\Model\Entity\TipoMotivacao
 *
 * @ORM\Table(name="tipo_motivacao")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\TipoMotivacao")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoMotivacao extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoMotivacao
     *
     * @ORM\Column(name="sq_tipo_motivacao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoMotivacao;

    /**
     * @var string $noTipoMotivacao
     *
     * @ORM\Column(name="no_tipo_motivacao", type="string", length=30, nullable=false)
     */
    private $noTipoMotivacao;


    /**
     * Set sqTratamento
     *
     * @param integer $sqTratamento
     * @return integer
     */
    public function setSqTipoMotivacao($sqTipoMotivacao = NULL)
    {
        $this->sqTipoMotivacao = $sqTipoMotivacao;
        if(!$sqTipoMotivacao){
            $this->sqTipoMotivacao  = NULL;
        }
        return $this;
    }

    /**
     * Get sqTratamento
     *
     * @return integer
     */
    public function getSqTipoMotivacao()
    {
        return $this->sqTipoMotivacao;
    }

    /**
     * Set noTratamento
     *
     * @param string $noTratamento
     * @return Tratamento
     */
    public function setNoTipoMotivacao($noTipoMotivacao)
    {
        $this->noTipoMotivacao = $noTipoMotivacao;
        return $this;
    }

    /**
     * Get noTratamento
     *
     * @return string
     */
    public function getNoTipoMotivacao()
    {
        return $this->noTipoMotivacao;
    }
}