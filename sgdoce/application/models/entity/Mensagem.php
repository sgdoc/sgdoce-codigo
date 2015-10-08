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
 * Sgdoce\Model\Entity\Mensagem
 *
 * @ORM\Table(name="mensagem")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\Mensagem")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Mensagem extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqMensagem
     *
     * @ORM\Column(name="sq_mensagem", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqMensagem;

    /**
     * @var string $txMensagem
     *
     * @ORM\Column(name="tx_mensagem", type="string", length=300, nullable=false)
     */
    private $txMensagem;

    /**
     * @var boolean $stMensagemAtiva
     *
     * @ORM\Column(name="st_mensagem_ativa", type="boolean", nullable=false)
     */
    private $stMensagemAtiva;

    /**
     * @var Sgdoce\Model\Entity\Assunto
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Assunto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_assunto", referencedColumnName="sq_assunto")
     * })
     */
    private $sqAssunto;

    /**
     * @var Sgdoce\Model\Entity\TipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_documento", referencedColumnName="sq_tipo_documento")
     * })
     */
    private $sqTipoDocumento;


    /**
     * Get sqMensagem
     *
     * @return integer
     */
    public function getSqMensagem()
    {
        return $this->sqMensagem;
    }

    /**
     * Set txMensagem
     *
     * @param string $txMensagem
     * @return Mensagem
     */
    public function setTxMensagem($txMensagem)
    {
        $this->assert('txMensagem',$txMensagem,$this);
        $this->txMensagem = $txMensagem;
        return $this;
    }

    /**
     * Get txMensagem
     *
     * @return string
     */
    public function getTxMensagem()
    {
        return $this->txMensagem;
    }

    /**
     * Set stMensagemAtiva
     *
     * @param boolean $stMensagemAtiva
     * @return Mensagem
     */
    public function setStMensagemAtiva($stMensagemAtiva)
    {
        $this->stMensagemAtiva = $stMensagemAtiva;
        return $this;
    }

    /**
     * Get stMensagemAtiva
     *
     * @return boolean
     */
    public function getStMensagemAtiva()
    {
        return $this->stMensagemAtiva;
    }

    /**
     * Set sqAssunto
     *
     * @param Sgdoce\Model\Entity\Assunto $sqAssunto
     * @return Mensagem
     */
    public function setSqAssunto(\Sgdoce\Model\Entity\Assunto $sqAssunto = NULL)
    {
        $this->sqAssunto = $sqAssunto;
        return $this;
    }

    /**
     * Get sqAssunto
     *
     * @return Sgdoce\Model\Entity\Assunto
     */
    public function getSqAssunto()
    {
        return $this->sqAssunto;
    }

    /**
     * Set sqTipoDocumento
     *
     * @param Sgdoce\Model\Entity\TipoDocumento $sqTipoDocumento
     * @return Mensagem
     */
    public function setSqTipoDocumento(\Sgdoce\Model\Entity\TipoDocumento $sqTipoDocumento = NULL)
    {
        $this->sqTipoDocumento = $sqTipoDocumento;
        return $this;
    }

    /**
     * Get sqTipoDocumento
     *
     * @return Sgdoce\Model\Entity\TipoDocumento
     */
    public function getSqTipoDocumento()
    {
        return $this->sqTipoDocumento;
    }
}