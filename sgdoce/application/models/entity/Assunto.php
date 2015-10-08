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
 * Sgdoce\Model\Entity\Assunto
 *
 * @ORM\Table(name="assunto")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\Assunto")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Assunto extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqAssunto
     *
     * @ORM\Column(name="sq_assunto", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqAssunto;

    /**
     * @var string $txAssunto
     *
     * @ORM\Column(name="tx_assunto", type="string", length=200, nullable=false)
     */
    private $txAssunto;

    /**
     * @var boolean $stHomologado
     *
     * @ORM\Column(name="st_homologado", type="boolean", nullable=false)
     */
    private $stHomologado;

    /**
     * Set sqAssunto
     *
     * @param $sqAssunto
     * @return Assunto
     */
    public function setSqAssunto($sqAssunto = NULL)
    {
        $this->sqAssunto = $sqAssunto;
        if(!$sqAssunto){
            $this->sqAssunto  = NULL;
        }
        return $this;
    }

    /**
     * Set txAssunto
     *
     * @param string $txAssunto
     * @return Assunto
     */
    public function setTxAssunto($txAssunto)
    {
        $this->assert('txAssunto', $txAssunto, $this);
        $this->txAssunto = $txAssunto;
        return $this;
    }

    /**
     * Set stHomologado
     *
     * @param boolean $stHomologado
     * @return Assunto
     */
    public function setStHomologado ($stHomologado = TRUE)
    {
        $this->assert('stHomologado', $stHomologado, $this);
        $this->stHomologado = $stHomologado;
        return $this;
    }

    /**
     * Get sqAssunto
     *
     * @return integer
     */
    public function getSqAssunto()
    {
        return $this->sqAssunto;
    }

    /**
     * Get txAssunto
     *
     * @return string
     */
    public function getTxAssunto()
    {
        return $this->txAssunto;
    }

    /**
     * Get stHomologado
     *
     * @return boolean
     */
    public function getStHomologado ()
    {
        return $this->stHomologado;
    }

}