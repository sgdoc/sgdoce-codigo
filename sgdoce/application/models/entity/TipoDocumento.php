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
 * Sgdoce\Model\Entity\TipoDocumento
 *
 * @ORM\Table(name="tipo_documento")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\TipoDocumento")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoDocumento extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoDocumento
     *
     * @ORM\Column(name="sq_tipo_documento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoDocumento;

    /**
     * @var string $noTipoDocumento
     *
     * @ORM\Column(name="no_tipo_documento", type="string", length=50, nullable=false)
     */
    private $noTipoDocumento;

    /**
     * @var boolean $inAbreProcesso
     *
     * @ORM\Column(name="in_abre_processo", type="boolean", nullable=true)
     */
    private $inAbreProcesso;

    /**
     * @var boolean $stAtivo
     *
     * @ORM\Column(name="st_ativo", type="boolean", nullable=true)
     */
    private $stAtivo;

    /**
     *
     *@ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\SequencialArtefato", mappedBy="sqTipoDocumento")
     */
    private $sqSequencialArtefato;

    /**
     * @var string $in_assinatura_digital
     * @ORM\Column(name="in_assinatura_digital", type="boolean")
     */
    private $in_assinatura_digital;

    /**
     * @var string $in_assinatura_login
     * @ORM\Column(name="in_assinatura_login", type="boolean")
     */
    private $in_assinatura_login;

    /**
     * @var string $in_controlado_sgdoc
     * @ORM\Column(name="in_controlado_sgdoc", type="boolean")
     */
    private $in_controlado_sgdoc;

    /**
     * @var string $in_multipla_assinatura
     * @ORM\Column(name="in_multipla_assinatura", type="boolean")
     */
    private $in_multipla_assinatura;

    /**
     * @param mixed $sqSequencialArtefato
     */
    public function setSqSequencialArtefato($sqSequencialArtefato)
    {
        $this->sqSequencialArtefato = $sqSequencialArtefato;
    }

    /**
     * @return mixed
     */
    public function getSqSequencialArtefato()
    {
        return $this->sqSequencialArtefato;
    }

    /**
     * Get sqTipoDocumento
     *
     * @return integer
     */
    public function getSqTipoDocumento()
    {
        return $this->sqTipoDocumento;
    }

    /**
     * Set sqTipoDocumento
     *
     * @param $sqTipoDocumento
     * @return TipoDocumento
     */
    public function setSqTipoDocumento($sqTipoDocumento)
    {
        $this->sqTipoDocumento = $sqTipoDocumento;

        return $this;
    }


    /**
     * Set noTipoDocumento
     *
     * @param string $noTipoDocumento
     * @return TipoDocumento
     */
    public function setNoTipoDocumento($noTipoDocumento)
    {
        $this->assert('noTipoDocumento',$noTipoDocumento,$this);
        $this->noTipoDocumento = $noTipoDocumento;
        return $this;
    }

    /**
     * Get noTipoDocumento
     *
     * @return string
     */
    public function getNoTipoDocumento()
    {
        return $this->noTipoDocumento;
    }

    /**
     * Set inAbreProcesso
     *
     * @param boolean $inAbreProcesso
     * @return TipoDocumento
     */
    public function setInAbreProcesso($inAbreProcesso)
    {
        $this->inAbreProcesso = $inAbreProcesso;
        return $this;
    }

    /**
     * Get inAbreProcesso
     *
     * @return boolean
     */
    public function getInAbreProcesso()
    {
        return $this->inAbreProcesso;
    }

    /**
     * Set inAssinaturaDigital
     *
     * @param boolean $inAssinaturaDigital
     * @return TipoDocumento
     */
    public function setInAssinaturaDigital($inAssinaturaDigital)
    {
        $this->inAssinaturaDigital = $inAssinaturaDigital;
        return $this;
    }

    /**
     * Get inAssinaturaDigital
     *
     * @return boolean
     */
    public function getInAssinaturaDigital()
    {
        return $this->inAssinaturaDigital;
    }

    /**
     * Set inAssinaturaLogin
     *
     * @param boolean $inAssinaturaLogin
     * @return TipoDocumento
     */
    public function setInAssinaturaLogin($inAssinaturaLogin)
    {
        $this->inAssinaturaLogin = $inAssinaturaLogin;
        return $this;
    }

    /**
     * Get inAssinaturaLogin
     *
     * @return boolean
     */
    public function getInAssinaturaLogin()
    {
        return $this->inAssinaturaLogin;
    }

    /**
     * Set inControlado
     *
     * @param boolean $inControlado
     * @return TipoDocumento
     */
    public function setInControlado($inControlado)
    {
        $this->inControlado = $inControlado;
        return $this;
    }

    /**
     * Get inControlado
     *
     * @return boolean
     */
    public function getInControlado()
    {
        return $this->inControlado;
    }

    /**
     * Set inMultiplaAssinatura
     *
     * @param boolean $inMultiplaAssinatura
     * @return TipoDocumento
     */
    public function setInMultiplaAssinatura($inMultiplaAssinatura)
    {
        $this->inMultiplaAssinatura = $inMultiplaAssinatura;
        return $this;
    }

    /**
     * Get inMultiplaAssinatura
     *
     * @return boolean
     */
    public function getInMultiplaAssinatura()
    {
        return $this->inMultiplaAssinatura;
    }

    /**
     * Set stAtivo
     *
     * @param boolean $stAtivo
     * @return TipoDocumento
     */
    public function setStAtivo($stAtivo)
    {
        $this->stAtivo = $stAtivo;
        return $this;
    }

    /**
     * Get stAtivo
     *
     * @return boolean
     */
    public function getStAtivo()
    {
        return $this->stAtivo;
    }
}