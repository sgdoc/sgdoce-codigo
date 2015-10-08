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
 * Sgdoce\Model\Entity\AnexoComprovante
 *
 * @ORM\Table(name="anexo_comprovante")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\AnexoComprovante")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class AnexoComprovante extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqAnexoComprovante
     *
     * @ORM\Id
     * @ORM\Column(name="sq_anexo_comprovante", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqAnexoComprovante;

    /**
     * @var string $deCaminhoArquivo
     *
     * @ORM\Column(name="de_caminho_arquivo", type="string", length=100, nullable=false)
     */
    private $deCaminhoArquivo;

    /**
     * @var integer $sqEnderecoSgdoce
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\EnderecoSgdoce", mappedBy="sqEnderecoSgdoce")
     * @ORM\JoinColumn(name="sq_endereco_sgdoce", referencedColumnName="sq_endereco_sgdoce")
     */
    private $sqEnderecoSgdoce;

    /**
     * Get sqAnexoComprovanteDocumento
     *
     * @return integer
     */
    public function getSqAnexoComprovante()
    {
        return $this->sqAnexoComprovante;
    }

    /**
     * Set deCaminhoImagem
     *
     * @param string $deCaminhoImagem
     * @return AnexoComprovante
     */
    public function setDeCaminhoArquivo($deCaminhoArquivo)
    {
        $this->deCaminhoArquivo = $deCaminhoArquivo;

        return $this;
    }

    /**
     * Get deCaminhoImagem
     *
     * @return string
     */
    public function getDeCaminhoArquivo()
    {
        return $this->deCaminhoArquivo;
    }

    /**
     * Set sqEnderecoSgdoce
     *
     * @param integer $sqEnderecoSgdoce
     * @return AnexoComprovante
     */
    public function setSqEnderecoSgdoce($sqEnderecoSgdoce)
    {
        $this->sqEnderecoSgdoce = $sqEnderecoSgdoce;

        return $this;
    }

    /**
     * Get sqEnderecoSgdoce
     *
     * @return integer
     */
    public function getSqEnderecoSgdoce()
    {
        return $this->sqEnderecoSgdoce;
    }
}