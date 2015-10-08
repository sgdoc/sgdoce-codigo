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
 * Sica\Model\Entity\UsuarioPerfil
 *
 * @ORM\Table(name="usuario_perfil")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\UsuarioPerfil")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class UsuarioPerfil extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqUsuarioPerfil
     *
     * @ORM\Column(name="sq_usuario_perfil", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqUsuarioPerfil;

    /**
     * @var Sica\Model\Entity\Perfil
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Perfil", inversedBy="sqPerfilFuncionalidade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_perfil", referencedColumnName="sq_perfil")
     * })
     */
    private $sqPerfil;

    /**
     * @var Sica\Model\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Usuario", inversedBy="sqUsuarioPerfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_usuario", referencedColumnName="sq_usuario")
     * })
     */
    private $sqUsuario;

    /**
     * @var Sica\Model\Entity\UnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\UnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_org_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrgPessoa;


    /**
     * Get sqUsuarioPerfil
     *
     * @return integer
     */
    public function getSqUsuarioPerfil()
    {
        return $this->sqUsuarioPerfil;
    }

    /**
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return SicaUsuarioPerfil
     */
    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * Get stRegistroAtivo
     *
     * @return string
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }

    /**
     * Set sqPerfil
     *
     * @param Sica\Model\Entity\Perfil $sqPerfil
     * @return UsuarioPerfil
     */
    public function setSqPerfil(Perfil $sqPerfil = NULL)
    {
        $this->sqPerfil = $sqPerfil;
        return $this;
    }

    /**
     * Get sqPerfil
     *
     * @return Sica\Model\Entity\Perfil
     */
    public function getSqPerfil()
    {
        if (NULL === $this->sqPerfil) {
            $this->setSqPerfil(new Perfil());
        }

        return $this->sqPerfil;
    }

    /**
     * Set sqUsuario
     *
     * @param Sica\Model\Entity\Usuario $sqUsuario
     * @return SicaUsuarioPerfil
     */
    public function setSqUsuario(Usuario $sqUsuario = NULL)
    {
        $this->sqUsuario = $sqUsuario;
        return $this;
    }

    /**
     * Get sqUsuario
     *
     * @return Sica\Model\Entity\Usuario
     */
    public function getSqUsuario()
    {
        if (NULL === $this->sqUsuario) {
            $this->setSqUsuario(new Usuario());
        }

        return $this->sqUsuario;
    }

    /**
     * Set sqUnidadeOrgPessoa
     *
     * @param Sica\Model\Entity\UnidadeOrg $sqUnidadeOrgPessoa
     * @return UsuarioPerfil
     */
    public function setSqUnidadeOrgPessoa(UnidadeOrg $sqUnidadeOrgPessoa = NULL)
    {
        $this->sqUnidadeOrgPessoa = $sqUnidadeOrgPessoa;
        return $this;
    }

    /**
     * Get sqUnidadeOrgPessoa
     *
     * @return Sica\Model\Entity\UnidadeOrg
     */
    public function getSqUnidadeOrgPessoa()
    {
        if (NULL === $this->sqUnidadeOrgPessoa) {
            $this->setSqUnidadeOrgPessoa(new UnidadeOrg());
        }

        return $this->sqUnidadeOrgPessoa;
    }
}