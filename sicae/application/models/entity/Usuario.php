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

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\Usuario
 *
 * @ORM\Table(name="usuario")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Usuario")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Usuario extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqUsuario
     *
     * @ORM\Column(name="sq_usuario", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqUsuario;

    /**
     * @var string $txSenha
     *
     * @ORM\Column(name="tx_senha", type="string", length=128, nullable=true)
     */
    private $txSenha;

    /**
     * @var boolean $stAtivo
     *
     * @ORM\Column(name="st_ativo", type="boolean", nullable=false)
     */
    private $stAtivo;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\Pessoa", inversedBy="sqUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     *
     * @var Sica\Model\Entity\UsuarioPerfil
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\UsuarioPerfil", mappedBy="sqUsuario")
     */
    private $sqUsuarioPerfil;

    /**
     * Set sqUsuario
     *
     * @return integer
     */
    public function setSqUsuario($sqUsuario)
    {
        $this->sqUsuario = $sqUsuario;
        return $this;
    }

    /**
     * Get sqUsuario
     *
     * @return integer
     */
    public function getSqUsuario()
    {
        return $this->sqUsuario;
    }

    /**
     * Set txSenha
     *
     * @param string $txSenha
     * @return Usuario
     */
    public function setTxSenha($txSenha)
    {
        $this->txSenha = $txSenha;
        return $this;
    }

    /**
     * Get txSenha
     *
     * @return string
     */
    public function getTxSenha()
    {
        return $this->txSenha;
    }

    /**
     * Set stAtivo
     *
     * @param boolean $stAtivo
     * @return Usuario
     */
    public function setStAtivo($stAtivo)
    {
        $this->stAtivo = $stAtivo;
        return $this;
    }

    /**
     * Get stAtivo
     *
     * @return string
     */
    public function getStAtivo()
    {
        return $this->stAtivo;
    }

    /**
     * Set inLdap
     *
     * @param boolean $inLdap
     * @return Usuario
     */
    public function setInLdap($inLdap)
    {
        $this->inLdap = $inLdap;
        return $this;
    }

    /**
     * Get inLdap
     *
     * @return string
     */
    public function getInLdap()
    {
        return $this->inLdap;
    }

    /**
     * Set sqPessoa
     *
     * @param Sica\Model\Entity\Pessoa $sqPessoa
     * @return Pessoa
     */
    public function setSqPessoa(Pessoa $sqPessoa = NULL)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return Sica\Model\Entity\Pessoa
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa ? $this->sqPessoa : new \Sica\Model\Entity\Pessoa();
    }

    /**
     * Set sqUsuarioPerfil
     *
     * @param Sica\Model\Entity\UsuarioPerfil $sqUsuarioPerfil
     * @return Sica\Model\Entity\UsuarioPerfil
     */
    public function setSqUsuarioPerfil(\Sica\Model\Entity\UsuarioPerfil $sqUsuarioPerfil = NULL)
    {
        $this->sqPessoa = $sqUsuarioPerfil;
        return $this;
    }

    /**
     * Get sqUsuarioPerfil
     *
     * @return Sica\Model\Entity\UsuarioPerfil
     */
    public function getSqUsuarioPerfil()
    {
        return $this->sqUsuarioPerfil ? $this->sqUsuarioPerfil : new \Doctrine\Common\Collections\ArrayCollection();
    }

}
