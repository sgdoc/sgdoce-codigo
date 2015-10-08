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
 * Sica\Model\Entity\PerfilFuncionalidade
 *
 * @ORM\Table(name="perfil_funcionalidade")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\PerfilFuncionalidade")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class PerfilFuncionalidade extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqPerfilFuncionalidade
     *
     * @ORM\Column(name="sq_perfil_funcionalidade", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPerfilFuncionalidade;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * @var Sica\Model\Entity\Perfil
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_perfil", referencedColumnName="sq_perfil")
     * })
     */
    private $sqPerfil;

    /**
     * @var Sica\Model\Entity\Funcionalidade
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Funcionalidade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_funcionalidade", referencedColumnName="sq_funcionalidade")
     * })
     */
    private $sqFuncionalidade;

    /**
     * Get sqPerfilFuncionalidade
     *
     * @return integer
     */
    public function getSqPerfilFuncionalidade()
    {
        return $this->sqPerfilFuncionalidade;
    }

    /**
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return SicaPerfilFuncionalidade
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
     * @return PerfilFuncionalidade
     */
    public function setSqPerfil(Perfil $sqPerfil = NULL)
    {
        $this->sqPerfil = $sqPerfil;
        return $this;
    }

    /**
     * Get sqPerfil
     *
     * @return Sica\Model\Entity\SicaPerfil
     */
    public function getSqPerfil()
    {
        if (NULL === $this->sqPerfil) {
            $this->setSqPerfil(new Perfil());
        }

        return $this->sqPerfil;
    }

    /**
     * Set sqFuncionalidade
     *
     * @param Sica\Model\Entity\SicaFuncionalidade $sqFuncionalidade
     * @return SicaPerfilFuncionalidade
     */
    public function setSqFuncionalidade(Funcionalidade $sqFuncionalidade = NULL)
    {
        $this->sqFuncionalidade = $sqFuncionalidade;
        return $this;
    }

    /**
     * Get sqFuncionalidade
     *
     * @return Sica\Model\Entity\SicaFuncionalidade
     */
    public function getSqFuncionalidade()
    {
        if (NULL === $this->sqFuncionalidade) {
            $this->setSqFuncionalidade(new Funcionalidade());
        }

        return $this->sqFuncionalidade;
    }
}