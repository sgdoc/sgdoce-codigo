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
 * Sgdoce\Model\Entity\AnexoArtefatoVinculo
 *
 * @ORM\Table(name="anexo_artefato_vinculo")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\AnexoArtefatoVinculo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class AnexoArtefatoVinculo extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqAnexoArtefatoVinculo
     *
     * @ORM\Id
     * @ORM\Column(name="sq_anexo_artefato_vinculo", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqAnexoArtefatoVinculo;

    /**
     * @var integer $sqArtefatoVinculo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\ArtefatoVinculo", inversedBy="sqAnexoArtefatoVinculo")
     * @ORM\JoinColumn(name="sq_artefato_vinculo", referencedColumnName="sq_artefato_vinculo")
     */
    private $sqArtefatoVinculo;

     /**
     * @var integer $sqTipoAnexo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoAnexo")
     * @ORM\JoinColumn(name="sq_tipo_anexo", referencedColumnName="sq_tipo_anexo")
     */
    private $sqTipoAnexo;

    /**
     * @var string $deCaminhoAnexo
     *
     * @ORM\Column(name="de_caminho_anexo", type="string", length=200, nullable=false)
     */
    private $deCaminhoAnexo;

    /**
     * @var string $txOutroTipo
     *
     * @ORM\Column(name="tx_outro_tipo", type="string", length=100, nullable=false)
     */
    private $txOutroTipo;

    /**
     * @var string $noTituloAnexo
     *
     * @ORM\Column(name="no_titulo_anexo", type="string", length=200, nullable=false)
     */
    private $noTituloAnexo;

    /**
     * Set sqAnexoArtefatoVinculo
     *
     * @param $sqAnexoArtefatoVinculo
     * @return integer
     */
    public function setSqAnexoArtefatoVinculo($sqAnexoArtefatoVinculo)
    {
    	$this->sqAnexoArtefatoVinculo = $sqAnexoArtefatoVinculo;
    	return $this;
    }

    /**
     * Get sqAnexoArtefatoVinculo
     *
     * @return integer
     */
    public function getSqAnexoArtefatoVinculo()
    {
    	return $this->sqAnexoArtefatoVinculo;
    }

    /**
     * Set sqTipoAnexo
     *
     * @param $sqTipoAnexo
     * @return integer
     */
    public function setSqTipoAnexo(TipoAnexo $sqTipoAnexo)
    {
    	$this->sqTipoAnexo = $sqTipoAnexo;
    	return $this;
    }

    /**
     * Get sqTipoAnexo
     *
     * @return integer
     */
    public function getSqTipoAnexo()
    {
    	return $this->sqTipoAnexo ? $this->sqTipoAnexo : new TipoAnexo();
    }

    /**
     * Set sqArtefatoVinculo
     *
     * @param $sqAnexoArtefato
     * @return integer
     */
    public function setSqArtefatoVinculo(ArtefatoVinculo $sqArtefatoVinculo)
    {
    	$this->sqArtefatoVinculo = $sqArtefatoVinculo;
    	return $this;
    }

    /**
     * Get sqArtefatoVinculo
     *
     * @return integer
     */
    public function getSqArtefatoVinculo()
    {
    	return $this->sqArtefatoVinculo ? $this->sqArtefatoVinculo : new ArtefatoVinculo();
    }

    /**
     * Set deCaminhoArquivo
     *
     * @param string $deCaminhoAnexo
     * @return string
     */
    public function setDeCaminhoAnexo($deCaminhoArquivo)
    {
        $this->deCaminhoAnexo = $deCaminhoArquivo;
        return $this;
    }

    /**
     * Get deCaminhoArquivo
     *
     * @return string
     */
    public function getDeCaminhoAnexo()
    {
        return $this->deCaminhoAnexo;
    }

    /**
     * Set txOutroTipo
     *
     * @param string $txOutroTipo
     * @return string
     */
    public function setTxOutroTipo($txOutroTipo)
    {
    	$this->txOutroTipo = $txOutroTipo;
    	return $this;
    }

    /**
     * Get txOutroTipo
     *
     * @return string
     */
    public function getTxOutroTipo()
    {
    	return $this->txOutroTipo;
    }

    /**
     * Set noTituloAnexo
     *
     * @param string $noTituloAnexo
     * @return string
     */
    public function setNoTituloAnexo($noTituloAnexo)
    {
    	$this->noTituloAnexo = $noTituloAnexo;
    	return $this;
    }

    /**
     * Get noTituloAnexo
     *
     * @return string
     */
    public function getNoTituloAnexo()
    {
    	return $this->noTituloAnexo;
    }
}