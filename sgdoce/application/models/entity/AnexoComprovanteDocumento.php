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
 * Sgdoce\Model\Entity\AnexoComprovanteDocumento
 *
 * @ORM\Table(name="anexo_comprovante_documento")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\AnexoComprovanteDocumento")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class AnexoComprovanteDocumento extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqAnexoArtefato
     *
     * @ORM\Id
     * @ORM\Column(name="sq_anexo_comprovante_documento", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqAnexoComprovanteDocumento;

    /**
     * @var string $deCaminhoImagem
     *
     * @ORM\Column(name="de_caminho_imagem", type="string", length=100, nullable=false)
     */
    private $deCaminhoImagem;

    /**
     * @var integer $sqPessoaSgdoce
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PessoaSgdoce", inversedBy="sqPessoaSgdoce", cascade={"persist"})
     * @ORM\JoinColumn(name="sq_pessoa_sgdoce", referencedColumnName="sq_pessoa_sgdoce")
     */
    private $sqPessoaSgdoce;

    /**
     * @var string $sqTipoDocumento
     *
     * @ORM\Column(name="sq_tipo_documento", type="integer", nullable=false)
     */
    private $sqTipoDocumento;

    /**
     * Get sqAnexoComprovanteDocumento
     *
     * @return integer
     */
    public function getSqAnexoComprovanteDocumento()
    {
        return $this->sqAnexoComprovanteDocumento;
    }

    /**
     * Set deCaminhoImagem
     *
     * @param string $deCaminhoImagem
     * @return AnexoComprovanteDocumento
     */
    public function setDeCaminhoImagem($deCaminhoImagem)
    {
        $this->deCaminhoImagem = $deCaminhoImagem;

        return $this;
    }

    /**
     * Get deCaminhoImagem
     *
     * @return string
     */
    public function getDeCaminhoImagem()
    {
        return $this->deCaminhoImagem;
    }

    /**
     * Set sqPessoaSgdoce
     *
     * @param integer $sqPessoaSgdoce
     * @return AnexoComprovanteDocumento
     */
    public function setSqPessoaSgdoce($sqPessoaSgdoce)
    {
        $this->sqPessoaSgdoce = $sqPessoaSgdoce;

        return $this;
    }

    /**
     * Get sqPessoaSgdoce
     *
     * @return integer
     */
    public function getSqPessoaSgdoce()
    {
        return $this->sqPessoaSgdoce;
    }

    public function setSqTipoDocumento($sqTipoDocumento)
    {
        $this->sqTipoDocumento = $sqTipoDocumento;

        return false;
    }

    public function getSqTipoDocumento()
    {
        return $this->sqTipoDocumento;
    }
}