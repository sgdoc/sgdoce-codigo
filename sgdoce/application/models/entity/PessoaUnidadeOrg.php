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
 * Classe para Entity PessoaUnidadeOrg
 *
 * @package      Model
 * @subpackage     Entity
 * @name         PessoaUnidadeOrg
 * @version     1.0.0
 * @since        2013-02-07
 */

/**
 * Sgdoce\Model\Entity\PessoaUnidadeOrg
 *
 * @ORM\Table(name="pessoa_unidade_org")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\PessoaUnidadeOrg")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class PessoaUnidadeOrg extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqPessoaUnidadeOrg
     *
     * @ORM\Column(name="sq_pessoa_unidade_org", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPessoaUnidadeOrg;

    /**
     * @var Sgdoce\Model\Entity\PessoaSgdoce
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PessoaSgdoce")
     * @ORM\JoinColumn(name="sq_pessoa_sgdoce", referencedColumnName="sq_pessoa_sgdoce")
     */
    private $sqPessoaSgdoce;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumn(name="sq_pessoa_unidade_org_corp", referencedColumnName="sq_pessoa",nullable=true)
     */
    private $sqPessoaUnidadeOrgCorp;

    /**
     * @var string $noUnidadeOrg
     * @ORM\Column(name="no_unidade_org", type="string", length=255, nullable=true)
     */
    private $noUnidadeOrg;

    /**
     * @var string $noCargo
     * @ORM\Column(name="no_cargo", type="string", length=120, nullable=true)
     */
    private $noCargo;

    /**
     * @ var Sgdoce\Model\Entity\PessoaAssinanteArtefato
     *
     * @ ORM\OneToMany(targetEntity="Sgdoce\Model\Entity\PessoaAssinanteArtefato", mappedBy="sqPessoaSgdoce")
    */
//    private $sqPessoaArtefato;

    /**
     * Get sqArtefato
     *
     * @return Artefato
     */
    public function getSqPessoaUnidadeOrg()
    {
        return $this->sqPessoaUnidadeOrg;
    }

    /**
     * Set sqArtefato
     *
     * @param object $sqArtefato
     * @return PessoaInteressadaArtefato
     */
    public function setSqPessoaUnidadeOrg($sqPessoaUnidadeOrg = NULL)
    {
        $this->sqPessoaUnidadeOrg = $sqPessoaUnidadeOrg;
        if (!$sqPessoaUnidadeOrg) {
            $this->sqPessoaUnidadeOrg = NULL;
        }
        return $this;
    }

    /**
     * Set sqPessoaSgdoce
     *
     * @param object $sqPessoaSgdoce
     * @return PessoaInteressadaArtefato
     */
    public function setSqPessoaSgdoce(PessoaSgdoce $sqPessoaSgdoce)
    {
        $this->sqPessoaSgdoce = $sqPessoaSgdoce;
        return $this;
    }

    /**
     * Get sqPessoaSgdoce
     *
     * @return PessoaSgdoce
     */
    public function getSqPessoaSgdoce()
    {
        return $this->sqPessoaSgdoce;
    }

    /**
     * Set sqPessoaUnidadeOrgCorp
     *
     * @param integer $sqPessoaUnidadeOrgCorp
     * @return PessoaUnidadeOrgCorp
     */
    public function setSqPessoaUnidadeOrgCorp($sqPessoaUnidadeOrgCorp)
    {
        $this->sqPessoaUnidadeOrgCorp = $sqPessoaUnidadeOrgCorp;
        return $this;
    }

    /**
     * Get sqPessoaUnidadeOrgCorp
     *
     * @return integer
     */
    public function getSqPessoaUnidadeOrgCorp()
    {
        return $this->sqPessoaUnidadeOrgCorp ? $this->sqPessoaUnidadeOrgCorp : new VwUnidadeOrg();
    }

    /**
     * Set noUnidadeOrg
     *
     * @param string $noUnidadeOrg
     * @return noUnidadeOrg
     */
    public function setNoUnidadeOrg($noUnidadeOrg)
    {
        $this->noUnidadeOrg = $noUnidadeOrg;
        return $this;
    }

    /**
     * Get noUnidadeOrg
     *
     * @return string
     */
    public function getNoUnidadeOrg()
    {
        return $this->noUnidadeOrg;
    }

    /**
     * Set noCargo
     *
     * @param string $noCargo
     * @return noCargo
     */
    public function setNoCargo($noCargo)
    {
        $this->noCargo = $noCargo;
        return $this;
    }

    /**
     * Get noCargo
     *
     * @return string
     */
    public function getNoCargo()
    {
        return $this->noCargo;
    }
}