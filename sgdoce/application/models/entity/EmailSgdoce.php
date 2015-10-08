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
 * SISICMBio
 *
 * Classe para Entity EmailSgdoce
 *
 * @package      Model
 * @subpackage     Entity
 * @name         EmailSgdoce
 * @version     1.0.0
 * @since        2013-02-07
 */

/**
 * Sgdoce\Model\Entity\EmailSgdoce
 *
 * @ORM\Table(name="email_sgdoce")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\EmailSgdoce")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class EmailSgdoce extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqEmailSgdoce
     *
     * @ORM\Id
     * @ORM\Column(name="sq_email_sgdoce", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqEmailSgdoce;

    /**
     * @var Sgdoce\Model\Entity\PessoaSgdoce
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PessoaSgdoce", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="sq_pessoa_sgdoce", referencedColumnName="sq_pessoa_sgdoce")
     */
    private $sqPessoaSgdoce;

    /**
     * @var zenddate $dtCadastro
     * @ORM\Column(name="dt_cadastro", type="zenddate", nullable=false)
     */
    private $dtCadastro;

    /**
     * @var Sgdoce\Model\Entity\VwTipoEmail
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwTipoEmail")
     * @ORM\JoinColumn(name="sq_tipo_email", referencedColumnName="sq_tipo_email")
     */
    private $sqTipoEmail;

    /**
     * @var string $txEmail
     *
     * @ORM\Column(name="tx_email", type="string", nullable=true)
     */
    private $txEmail;

    public function getSqEmailSgdoce() {
        return $this->sqEmailSgdoce;
    }

    public function setSqEmailSgdoce($sqEmailSgdoce) {
        $this->sqEmailSgdoce = $sqEmailSgdoce;
        return $this;
    }

    public function getSqPessoaSgdoce() {
        return $this->sqPessoaSgdoce ? $this->sqPessoaSgdoce : new PessoaSgdoce();
    }

    public function setSqPessoaSgdoce($sqPessoaSgdoce) {
        $this->sqPessoaSgdoce = $sqPessoaSgdoce;
        return $this;
    }

    public function getDtCadastro() {
        return $this->dtCadastro;
    }

    public function setDtCadastro($dtCadastro) {
        $this->dtCadastro = $dtCadastro;
        return $this;
    }

    public function getSqTipoEmail() {
        return $this->sqTipoEmail;
    }

    public function setSqTipoEmail(VwTipoEmail $sqTipoEmail) {
        $this->sqTipoEmail = $sqTipoEmail;
        return $this;
    }

    public function getTxEmail() {
        return $this->txEmail;
    }

    public function setTxEmail($txEmail) {
        $this->txEmail = $txEmail;
    }
}