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
 * @name         TelefoneSgdoce
 * @version     1.0.0
 * @since        2013-02-08
 */

/**
 * Sgdoce\Model\Entity\TelefoneSgdoce
 *
 * @ORM\Table(name="telefone_sgdoce")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\TelefoneSgdoce")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TelefoneSgdoce extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTelefoneSgdoce
     *
     * @ORM\Id
     * @ORM\Column(name="sq_telefone_sgdoce", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTelefoneSgdoce;

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
     * @var Sgdoce\Model\Entity\VwTipoTelefone
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwTipoTelefone")
     * @ORM\JoinColumn(name="sq_tipo_telefone", referencedColumnName="sq_tipo_telefone")
     */
    private $sqTipoTelefone;

    /**
     * @var string $nuDdd
     *
     * @ORM\Column(name="nu_ddd", type="string", nullable=true)
     */
    private $nuDdd;

    /**
     * @var string $nuTelefone
     *
     * @ORM\Column(name="nu_telefone", type="string", nullable=true)
     */
    private $nuTelefone;

    public function getSqTelefoneSgdoce() {
        return $this->sqTelefoneSgdoce;
    }

    public function setSqTelefoneSgdoce($sqTelefoneSgdoce) {
        $this->sqTelefoneSgdoce = $sqTelefoneSgdoce;
        return $this;
    }

    public function getSqPessoaSgdoce() {
        return $this->sqPessoaSgdoce ? $this->sqPessoaSgdoce : new PessoaSgdoce();
    }

    public function setSqPessoaSgdoce(PessoaSgdoce $sqPessoaSgdoce) {
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

    public function getSqTipoTelefone() {
        return $this->sqTipoTelefone ? $this->sqTipoTelefone : new VwTipoTelefone();
    }

    public function setSqTipoTelefone(VwTipoTelefone $sqTipoTelefone) {
        $this->sqTipoTelefone = $sqTipoTelefone;
        return $this;
    }

    public function getNuDdd() {
        return $this->nuDdd;
    }

    public function setNuDdd($nuDdd) {
        $this->nuDdd = $nuDdd;
        return $this;
    }

    public function getNuTelefone() {
        return $this->nuTelefone;
    }

    public function setNuTelefone($nuTelefone) {
        $this->nuTelefone = $nuTelefone;
        return $this;
    }
}