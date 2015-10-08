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
 * Sgdoce\Model\Entity\ModeloDocumento
 *
 * @ORM\Table(name="modelo_documento")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ModeloDocumento")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ModeloDocumento extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqModeloDocumento
     *
     * @ORM\Column(name="sq_modelo_documento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqModeloDocumento;

    /**
     * @var boolean $inAtivo
     *
     * @ORM\Column(name="in_ativo", type="boolean", nullable=false)
     */
    private $inAtivo;

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
     * @var Sgdoce\Model\Entity\GrauAcesso
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\GrauAcesso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_grau_acesso", referencedColumnName="sq_grau_acesso")
     * })
     */
    private $sqGrauAcesso;

    /**
     * @var Sgdoce\Model\Entity\PosicaoData
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PosicaoData")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_posicao_data", referencedColumnName="sq_posicao_data")
     * })
     */
    private $sqPosicaoData;

    /**
     * @var Sgdoce\Model\Entity\PosicaoTipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PosicaoTipoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_posicao_tipo_documento", referencedColumnName="sq_posicao_tipo_documento")
     * })
     */
    private $sqPosicaoTipoDocumento;

    /**
     * @var Sgdoce\Model\Entity\Cabecalho
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Cabecalho")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_cabecalho", referencedColumnName="sq_cabecalho")
     * })
     */
    private $sqCabecalho;

    /**
     * @var Sgdoce\Model\Entity\SgdoceTipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_documento", referencedColumnName="sq_tipo_documento")
     * })
     */
    private $sqTipoDocumento;

    /**
     * @var Sgdoce\Model\Entity\ModeloDocumentoCampo
     *
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\ModeloDocumentoCampo", mappedBy="sqModeloDocumento")
     */
    private $sqModeloDocumentoCampo;

    /**
     *Set sqModeloDocumento
     *
     * @param $sqModeloDocumento
     * @return ModeloDocumento
     */
    public function setSqModeloDocumento($sqModeloDocumento  = NULL)
    {
        $this->sqModeloDocumento = $sqModeloDocumento;
        if (!$sqModeloDocumento) {
            $this->sqModeloDocumento = NULL;
        }
        return $this;
    }

    /**
     * Get sqModeloDocumento
     *
     * @return integer
     */
    public function getSqModeloDocumento()
    {
        return $this->sqModeloDocumento;
    }

    /**
     * Set inAtivo
     *
     * @param boolean $inAtivo
     * @return ModeloDocumento
     */
    public function setInAtivo($inAtivo)
    {
        $this->inAtivo = $inAtivo;
        return $this;
    }

    /**
     * Get inAtivo
     *
     * @return boolean
     */
    public function getInAtivo()
    {
        return $this->inAtivo;
    }

    /**
     * Set sqAssunto
     *
     * @param Sgdoce\Model\Entity\Assunto $sqAssunto
     * @return ModeloDocumento
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
        return $this->sqAssunto ? $this->sqAssunto : new \SGDOCE\Model\Entity\Assunto();
    }

    /**
     * Set sqGrauAcesso
     *
     * @param Sgdoce\Model\Entity\GrauAcesso $sqGrauAcesso
     * @return ModeloDocumento
     */
    public function setSqGrauAcesso(\Sgdoce\Model\Entity\GrauAcesso $sqGrauAcesso = NULL)
    {
        $this->sqGrauAcesso = $sqGrauAcesso;
        return $this;
    }

    /**
     * Get sqGrauAcesso
     *
     * @return Sgdoce\Model\Entity\GrauAcesso
     */
    public function getSqGrauAcesso()
    {
        return $this->sqGrauAcesso ? $this->sqGrauAcesso : new \SGDOCE\Model\Entity\GrauAcesso();
    }

    /**
     * Set sqPosicaoData
     *
     * @param Sgdoce\Model\Entity\PosicaoData $sqPosicaoData
     * @return ModeloDocumento
     */
    public function setSqPosicaoData(\Sgdoce\Model\Entity\PosicaoData $sqPosicaoData = NULL)
    {
        $this->sqPosicaoData = $sqPosicaoData;
        return $this;
    }

    /**
     * Get sqPosicaoData
     *
     * @return Sgdoce\Model\Entity\PosicaoData
     */
    public function getSqPosicaoData()
    {
        return $this->sqPosicaoData ? $this->sqPosicaoData : new \SGDOCE\Model\Entity\PosicaoData();
    }

    /**
     * Set sqPosicaoTipoDocumento
     *
     * @param Sgdoce\Model\Entity\PosicaoTipoDocumento $sqPosicaoTipoDocumento
     * @return ModeloDocumento
     */
    public function setSqPosicaoTipoDocumento(\Sgdoce\Model\Entity\PosicaoTipoDocumento $sqPosicaoTipoDocumento = NULL)
    {
        $this->sqPosicaoTipoDocumento = $sqPosicaoTipoDocumento;
        return $this;
    }

    /**
     * Get sqPosicaoTipoDocumento
     *
     * @return Sgdoce\Model\Entity\PosicaoTipoDocumento
     */
    public function getSqPosicaoTipoDocumento()
    {
        return $this->sqPosicaoTipoDocumento ?
               $this->sqPosicaoTipoDocumento : new \SGDOCE\Model\Entity\PosicaoTipoDocumento();
    }

    /**
     * Set sqCabecalho
     *
     * @param Sgdoce\Model\Entity\Cabecalho $sqCabecalho
     * @return ModeloDocumento
     */
    public function setSqCabecalho(\Sgdoce\Model\Entity\Cabecalho $sqCabecalho = NULL)
    {
        $this->sqCabecalho = $sqCabecalho;
        return $this;
    }

    /**
     * Get sqCabecalho
     *
     * @return Sgdoce\Model\Entity\Cabecalho
     */
    public function getSqCabecalho()
    {
        return $this->sqCabecalho ?
               $this->sqCabecalho : new \SGDOCE\Model\Entity\Cabecalho();
    }

    /**
     * Set sqTipoDocumento
     *
     * @param Sgdoce\Model\Entity\TipoDocumento $sqTipoDocumento
     * @return ModeloDocumento
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
        return $this->sqTipoDocumento ? $this->sqTipoDocumento : new \SGDOCE\Model\Entity\TipoDocumento();
    }

    /**
     * Set sqModeloDocumentoCampo
     *
     * @param Sgdoce\Model\Entity\ModeloDocumentoCampo $sqModeloDocumentoCampo
     * @return ModeloDocumentoCampo
     */
    public function setSqModeloDocumentoCampo(\Sgdoce\Model\Entity\ModeloDocumentoCampo $sqModeloDocumentoCampo = NULL)
    {
        $this->sqModeloDocumentoCampo = $sqModeloDocumentoCampo;
        return $this;
    }

    /**
     * Get sqModeloDocumentoCampo
     *
     * @return Sgdoce\Model\Entity\ModeloDocumentoCampo
     */
    public function getSqModeloDocumentoCampo()
    {
        return $this->sqModeloDocumentoCampo ?
               $this->sqModeloDocumentoCampo : new \SGDOCE\Model\Entity\ModeloDocumentoCampo();
    }

}