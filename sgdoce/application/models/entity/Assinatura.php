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
 * Sgdoce\Model\Entity\Assinatura
 *
 * @ORM\Table(name="assinatura")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Assinatura")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Assinatura extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqAssinatura
     *
     * @ORM\Id
     * @ORM\Column(name="sq_assinatura", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqAssinatura;

    /**
     * @var datetime $dtAssinatura
     *
     * @ORM\Column(name="dt_assinatura", type="zenddate", nullable=false)
     */
    private $dtAssinatura;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;
    
    /**
     * @var string $txMotivacao
     * @ORM\Column(name="tx_motivacao", type="string", length=255, nullable=true)
     */
    private $txMotivacao;

    /**
     * @var Sgdoce\Model\Entity\Certificado
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Certificado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_certificado", referencedColumnName="sq_certificado")
     * })
     */
    private $sqCertificado;

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
     * Get sqAssinatura
     *
     * @return integer
     */
    public function getSqAssinatura()
    {
        return $this->sqAssinatura;
    }

    /**
     * Set dtAssinatura
     *
     * @param datetime $dtAssinatura
     * @return Assinatura
     */
    public function setDtAssinatura($dtAssinatura)
    {
        $this->dtAssinatura = $dtAssinatura;
        return $this;
    }

    /**
     * Get dtAssinatura
     *
     * @return datetime
     */
    public function getDtAssinatura()
    {
        return $this->dtAssinatura;
    }

    /**
     * Set sqPessoa
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqPessoa
     * @return Assinatura
     */
    public function setSqPessoa(\Sgdoce\Model\Entity\VwPessoa $sqPessoa = NULL)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return Sgdoce\Model\Entity\PessoaSgdoce
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa;
    }
    
    /**
     * Set sqPessoa
     *
     */
    public function setSqTxMotivacao($txMotivacao)
    {
        $this->txMotivacao = $txMotivacao;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     */
    public function getTxMotivacao()
    {
        return $this->txMotivacao;
    }

    /**
     * Set sqCertificado
     *
     * @param Sgdoce\Model\Entity\Certificado $sqCertificado
     * @return Assinatura
     */
    public function setSqCertificado(\Sgdoce\Model\Entity\Certificado $sqCertificado = NULL)
    {
        $this->sqCertificado = $sqCertificado;
        return $this;
    }

    /**
     * Get sqCertificado
     *
     * @return Sgdoce\Model\Entity\Certificado
     */
    public function getSqCertificado()
    {
        return $this->sqCertificado;
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return Assinatura
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