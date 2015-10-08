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
 * Sgdoce\Model\Entity\ArtefatoAssinado
 *
 * @ORM\Table(name="artefato_assinado")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ArtefatoAssinado")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ArtefatoAssinado extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqArtefatoAssinatura
     *
     * @ORM\Id
     * @ORM\Column(name="sq_artefato_assinatura", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqArtefatoAssinatura;

    /**
     * @var string $deCaminhoArquivo
     *
     * @ORM\Column(name="de_caminho_arquivo", type="string", length=200, nullable=false)
     */
    private $deCaminhoArquivo;

    /**
     * @var boolean $inArquivoAssinatura
     *
     * @ORM\Column(name="in_arquivo_assinatura", type="boolean", nullable=false)
     */
    private $inArquivoAssinatura;

    /**
     * @var Sgdoce\Model\Entity\ArtefatoAssinado
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\ArtefatoAssinado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_arquivo_assinatura", referencedColumnName="sq_artefato_assinatura")
     * })
     */
    private $sqArquivoAssinatura;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefato;


    /**
     * Get sqArtefatoAssinatura
     *
     * @return integer
     */
    public function getSqArtefatoAssinatura()
    {
        return $this->sqArtefatoAssinatura;
    }

    /**
     * Set deCaminhoArquivo
     *
     * @param string $deCaminhoArquivo
     * @return ArtefatoAssinado
     */
    public function setDeCaminhoArquivo($deCaminhoArquivo)
    {
        $this->deCaminhoArquivo = $deCaminhoArquivo;
        return $this;
    }

    /**
     * Get deCaminhoArquivo
     *
     * @return string
     */
    public function getDeCaminhoArquivo()
    {
        return $this->deCaminhoArquivo;
    }

    /**
     * Set inArquivoAssinatura
     *
     * @param boolean $inArquivoAssinatura
     * @return ArtefatoAssinado
     */
    public function setInArquivoAssinatura($inArquivoAssinatura)
    {
        $this->inArquivoAssinatura = $inArquivoAssinatura;
        return $this;
    }

    /**
     * Get inArquivoAssinatura
     *
     * @return boolean
     */
    public function getInArquivoAssinatura()
    {
        return $this->inArquivoAssinatura;
    }

    /**
     * Set sqArquivoAssinatura
     *
     * @param Sgdoce\Model\Entity\ArtefatoAssinado $sqArquivoAssinatura
     * @return ArtefatoAssinado
     */
    public function setSqArquivoAssinatura(\Sgdoce\Model\Entity\ArtefatoAssinado $sqArquivoAssinatura = NULL)
    {
        $this->sqArquivoAssinatura = $sqArquivoAssinatura;
        return $this;
    }

    /**
     * Get sqArquivoAssinatura
     *
     * @return Sgdoce\Model\Entity\ArtefatoAssinado
     */
    public function getSqArquivoAssinatura()
    {
        return $this->sqArquivoAssinatura;
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return ArtefatoAssinado
     */
    public function setSqArtefato(\Sgdoce\Model\Entity\Artefato $sqArtefato = NULL)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    /**
     * Get sqArtefato
     *
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function getSqArtefato()
    {
        return $this->sqArtefato;
    }
}