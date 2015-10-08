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

use Doctrine\ORM\Mapping as ORM,
    Core\Model\OWM\Mapping as OWM;

/**
 * SISICMBio
 *
 * Classe para Entity VwEmail
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Email
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwEmail
 *
 * @ORM\Table(name="vw_email")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwEmail", readOnly=true)
 * @OWM\Endpoint(configKey="libcorp", repositoryClass="Sgdoce\Model\Repository\VwEmailWs")
 */
class VwEmail extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqEmail
     *
     * @ORM\Column(name="sq_email", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqEmail;

    /**
     * @var string $txEmail
     *
     * @ORM\Column(name="tx_email", type="string", length=200, nullable=false)
     */
    private $txEmail;

    /**
     * @var Sgdoce\Model\Entity\TipoEmail
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwTipoEmail")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_email", referencedColumnName="sq_tipo_email")
     * })
     */
    private $sqTipoEmail;

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
     * Get sqEmail
     *
     * @return integer
     */
    public function getSqEmail()
    {
        return $this->sqEmail;
    }

    /**
     * Set txEmail
     *
     * @param string $txEmail
     * @return Email
     */
    public function setTxEmail($txEmail)
    {
        $this->assert('txEmail',$txEmail,$this);
        $this->txEmail = $txEmail;
        return $this;
    }

    /**
     * Get txEmail
     *
     * @return string
     */
    public function getTxEmail()
    {
        return $this->txEmail;
    }

    /**
     * Set sqTipoEmail
     *
     * @param \Sgdoce\Model\Entity\VwTipoEmail $sqTipoEmail
     * @return Email
     */
    public function setSqTipoEmail(VwTipoEmail $sqTipoEmail = NULL)
    {
        $this->sqTipoEmail = $sqTipoEmail;
        return $this;
    }

    /**
     * Get sqTipoEmail
     *
     * @return \Sgdoce\Model\Entity\VwTipoEmail
     */
    public function getSqTipoEmail()
    {
        return $this->sqTipoEmail ? $this->sqTipoEmail : new VwTipoEmail();
    }

    /**
     * Set sqPessoa
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqPessoa
     * @return Email
     */
    public function setSqPessoa(VwPessoa $sqPessoa = NULL)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return SCT\Model\Entity\VwPessoa
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa;
    }

}