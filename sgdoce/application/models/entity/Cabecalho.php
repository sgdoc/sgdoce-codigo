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
 * Sgdoce\Model\Entity\Cabecalho
 *
 * @ORM\Table(name="cabecalho")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\Cabecalho")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Cabecalho extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqCabecalho
     *
     * @ORM\Id
     * @ORM\Column(name="sq_cabecalho", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqCabecalho;

    /**
     * @var string $noCabecalho
     *
     * @ORM\Column(name="no_cabecalho", type="string", length=50, nullable=false)
     */
    private $noCabecalho;

    /**
     * @var string $txCabecalho
     *
     * @ORM\Column(name="tx_cabecalho", type="string", length=300, nullable=false)
     */
    private $txCabecalho;

    /**
     * @var string $deArquivoImagem
     *
     * @ORM\Column(name="de_arquivo_imagem", type="string", length=200, nullable=true)
     */
    private $deArquivoImagem;

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
     * Set sqCabecalho
     *
     * @param string $sqCabecalho
     * @return Cabecalho
     */
    public function setSqCabecalho($sqCabecalho = NULL)
    {
        $this->sqCabecalho = $sqCabecalho;
        if(!$sqCabecalho){
            $this->sqCabecalho  = NULL;
        }
        return $this;
    }

    /**
     * Get sqCabecalho
     *
     * @return integer
     */
    public function getSqCabecalho()
    {
        return $this->sqCabecalho;
    }

    /**
     * Set noCabecalho
     *
     * @param string $noCabecalho
     * @return Cabecalho
     */
    public function setNoCabecalho($noCabecalho)
    {
        $this->noCabecalho = $noCabecalho;
        return $this;
    }

    /**
     * Get noCabecalho
     *
     * @return string
     */
    public function getNoCabecalho()
    {
        return $this->noCabecalho;
    }

    /**
     * Set txCabecalho
     *
     * @param string $txCabecalho
     * @return Cabecalho
     */
    public function setTxCabecalho($txCabecalho)
    {
        $this->txCabecalho = $txCabecalho;
        return $this;
    }

    /**
     * Get txCabecalho
     *
     * @return string
     */
    public function getTxCabecalho()
    {
        return $this->txCabecalho;
    }

    /**
     * Set deArquivoImagem
     *
     * @param string $deArquivoImagem
     * @return Cabecalho
     */
    public function setDeArquivoImagem($deArquivoImagem)
    {
        $this->deArquivoImagem = $deArquivoImagem;
        return $this;
    }

    /**
     * Get deArquivoImagem
     *
     * @return string
     */
    public function getDeArquivoImagem()
    {
        return $this->deArquivoImagem;
    }

    /**
     * Set sqTipoDocumento
     *
     * @param Sgdoce\Model\Entity\TipoDocumento $sqTipoDocumento
     * @return Cabecalho
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