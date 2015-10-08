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
 * Classe para Entity Telefone
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Telefone
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwTelefone
 *
 * @ORM\Table(name="vw_telefone")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwTelefone")
 * @OWM\Endpoint(configKey="libcorp", repositoryClass="Sgdoce\Model\Repository\VwTelefoneWs")
 */
class VwTelefone extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTelefone
     *
     * @ORM\Column(name="sq_telefone", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTelefone;

    /**
     * @var string $nuDdd
     *
     * @ORM\Column(name="nu_ddd", type="string", length=3, nullable=false)
     */
    private $nuDdd;

    /**
     * @var string $nuTelefone
     *
     * @ORM\Column(name="nu_telefone", type="string", length=8, nullable=false)
     */
    private $nuTelefone;

    /**
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwTipoTelefone")
     * @ORM\JoinColumns({
     *     @ORM\JoinColumn(name="sq_tipo_telefone", referencedColumnName="sq_tipo_telefone")
     * })
     */
    private $sqTipoTelefone;

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
     * Get sqTelefone
     *
     * @return integer
     */
    public function getSqTelefone()
    {
        return $this->sqTelefone;
    }

    /**
     * Set nuDdd
     *
     * @param string $nuDdd
     * @return Telefone
     */
    public function setNuDdd($nuDdd)
    {
        $this->assert('nuDdd',$nuDdd,$this);
        $this->nuDdd = $nuDdd;
        return $this;
    }

    /**
     * Get nuDdd
     *
     * @return string
     */
    public function getNuDdd()
    {
        return $this->nuDdd;
    }

    /**
     * Set nuTelefone
     *
     * @param string $nuTelefone
     * @return Telefone
     */
    public function setNuTelefone($nuTelefone)
    {
        $this->assert('nuTelefone',$nuTelefone,$this);
        $this->nuTelefone = $nuTelefone;
        return $this;
    }

    /**
     * Get nuTelefone
     *
     * @return string
     */
    public function getNuTelefone()
    {
        return $this->nuTelefone;
    }

    /**
     * Set sqTipoTelefone
     *
     * @param VwTipoTelefone $sqTipoTelefone
     */
    public function setSqTipoTelefone(VwTipoTelefone $sqTipoTelefone)
    {
        $this->sqTipoTelefone = $sqTipoTelefone;

        return $this;
    }

    /**
     * Get sqTipoTelefone
     */
    public function getSqTipoTelefone()
    {
        return $this->sqTipoTelefone ? : new VwTipoTelefone();
    }

    /**
     * Set sqPessoa
     *
     * @param SCT\Model\Entity\VwPessoa $sqPessoa
     * @return Telefone
     */
    public function setSqPessoa(VwPessoa $sqPessoa = NULL)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return SCT\Model\Entity\Pessoa
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa;
    }
}