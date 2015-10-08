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
 * Sgdoce\Model\Entity\AnexoSic
 *
 * @ORM\Table(name="anexo_sic")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\AnexoSic")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class AnexoSic extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqAnexoSic
     *
     * @ORM\Id
     * @ORM\Column(name="sq_anexo_sic", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqAnexoSic;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato" , inversedBy="sqArtefato")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     * })
     */
    private $sqArtefato;

    /**
     * @var string $txCaminhoArquivo
     *
     * @ORM\Column(name="tx_caminho_arquivo", type="string", length=500, nullable=false)
     */
    private $txCaminhoArquivo;

    /**
     * @var integer $noArquivoReal
     *
     * @ORM\Column(name="no_arquivo_real", type="string", length=50, nullable=false)
     */
    private $noArquivoReal;

    /**
     * @var \Zend_Date $dtCadastro
     *
     * @ORM\Column(name="dt_cadastro", type="zenddate", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var string $txExtensaoArquivo
     *
     * @ORM\Column(name="tx_extensao_arquivo", type="string", length=5, nullable=false)
     */
    private $txExtensaoArquivo;

    /**
     * Set sqAnexoSic
     *
     * @param integer $sqAnexoSic
     * @return self
     */
    public function setSqAnexoSic($sqAnexoSic = NULL)
    {
        $this->sqAnexoSic = $sqAnexoSic;
        if (!$sqAnexoSic) {
            $this->sqAnexoSic = NULL;
        }
        return $this;
    }

    /**
     * Set txCaminhoArquivo
     *
     * @param string $txCaminhoArquivo
     * @return self
     */
    public function setTxCaminhoArquivo($txCaminhoArquivo)
    {
        $this->txCaminhoArquivo = $txCaminhoArquivo;
        return $this;
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return self
     */
    public function setSqArtefato(\Sgdoce\Model\Entity\Artefato $sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    /**
     * Set noArquivoReal
     *
     * @param string $noArquivoReal
     * @return self
     */
    public function setNoArquivoReal($noArquivoReal)
    {
        $this->noArquivoReal = $noArquivoReal;
        return $this;
    }

    /**
     * Set dtCadastro
     *
     * @param $dtCadastro
     * @return self
     */
    public function setDtCadastro(\Zend_Date $dtCadastro)
    {
        $this->dtCadastro = $dtCadastro;
        return $this;
    }

    /**
     * Set txExtensaoArquivo
     *
     * @param $txExtensaoArquivo
     * @return self
     */
    public function setTxExtensaoArquivo($txExtensaoArquivo)
    {
        $this->txExtensaoArquivo = $txExtensaoArquivo;
        return $this;
    }

    /**
     * Get sqAnexoSic
     *
     * @return integer
     */
    public function getSqAnexoSic()
    {
        return $this->sqAnexoSic;
    }

    /**
     * Get txCaminhoArquivo
     *
     * @return string
     */
    public function getTxCaminhoArquivo()
    {
        return $this->txCaminhoArquivo;
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

    /**
     * Get noArquivoReal
     *
     * @return string
     */
    public function getNoArquivoReal()
    {
        return $this->noArquivoReal;
    }

    /**
     * Get dtCadastro
     *
     * @return \Zend_Date
     */
    public function getDtCadastro()
    {
        return $this->dtCadastro;
    }

    /**
     * Get txExtensaoArquivo
     *
     * @return string
     */
    public function getTxExtensaoArquivo()
    {
        return $this->txExtensaoArquivo;
    }


}