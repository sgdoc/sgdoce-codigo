<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sgdoce\Model\Entity\VwTipoUnidadeOrg
 *
 * @ORM\Table(name="vw_tipo_unidade_org")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwTipoUnidadeOrg", readOnly=true)
 */
class VwTipoUnidadeOrg extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoUnidadeOrg
     *
     * @ORM\Id
     * @ORM\Column(name="sq_tipo_unidade_org", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoUnidadeOrg;

    /**
     * @var string $noTipoUnidadeOrg
     *
     * @ORM\Column(name="no_tipo_unidade_org", type="string", length=100, nullable=false)
     */
    private $noTipoUnidadeOrg;

    /**
     * @var integer $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * @var integer $inEstrutura
     *
     * @ORM\Column(name="in_estrutura", type="boolean", nullable=false)
     */
    private $inEstrutura;

    /**
     * @var Sgdoce\Model\Entity\VwTipoUnidadeOrg
     *
     * @ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg", mappedBy="sqTipoUnidade")
     */
    private $sqUnidadeOrg;

    /**
     * Set sqTipoUnidadeOrg
     *
     * @param integer $sqTipoUnidadeOrg
     * @return TipoUnidadeOrg
     */
    public function setSqTipoUnidadeOrg($sqTipoUnidadeOrg)
    {
        $this->sqTipoUnidadeOrg = $sqTipoUnidadeOrg;
        return $this;
    }

    /**
     * Get sqTipoUnidadeOrg
     *
     * @return integer
     */
    public function getSqTipoUnidadeOrg()
    {
        return $this->sqTipoUnidadeOrg;
    }

    /**
     * Set noTipoUnidadeOrg
     *
     * @param string $noTipoUnidadeOrg
     * @return TipoUnidadeOrg
     */
    public function setNoTipoUnidadeOrg($noTipoUnidadeOrg)
    {
        $this->noTipoUnidadeOrg = $noTipoUnidadeOrg;
        return $this;
    }

    /**
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return TipoUnidadeOrg
     */
    public function setTipoStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * Get stRegistroAtivo
     *
     * @return boolean
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }

    /**
     * Set inEstrutura
     *
     * @param boolean $inEstrutura
     * @return TipoUnidadeOrg
     */
    public function setInEstrutura($inEstrutura)
    {
        $this->inEstrutura = $inEstrutura;
        return $this;
    }

    /**
     * Get inEstrutura
     *
     * @return boolean
     */
    public function getInEstrutura()
    {
        return $this->inEstrutura;
    }
}