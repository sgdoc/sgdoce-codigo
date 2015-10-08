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
 * Sgdoce\Model\Entity\GrauAcessoArtefato
 *
 * @ORM\Table(name="grau_acesso_artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\GrauAcessoArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class GrauAcessoArtefato extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqGrauAcessoArtefato
     *
     * @ORM\Id
     * @ORM\Column(name="sq_grau_acesso_artefato", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqGrauAcessoArtefato;

    /**
     * @var zenddate $dtAtribuicao
     *
     * @ORM\Column(name="dt_atribuicao", type="zenddate", nullable=false)
     */
    private $dtAtribuicao;

    /**
     * @var boolean $stAtivo
     *
     * @ORM\Column(name="st_ativo", type="boolean", nullable=false)
     */
    private $stAtivo;

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
     * @var Sgdoce\Model\Entity\GrauAcesso
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\GrauAcesso")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_grau_acesso", referencedColumnName="sq_grau_acesso")
     * })
     */
    private $sqGrauAcesso;


    /**
     * Get sqGrauAcessoArtefato
     *
     * @return integer
     */
    public function getSqGrauAcessoArtefato()
    {
        return $this->sqGrauAcessoArtefato;
    }

    /**
     * Set sqGrauAcessoArtefato
     *
     * @param Sgdoce\Model\Entity\GrauAcessoArtefato $sqGrauAcessoArtefato
     * @return GrauAcessoArtefato
     */
    public function setSqGrauAcessoArtefato(\Sgdoce\Model\Entity\GrauAcessoArtefato $sqGrauAcessoArtefato = NULL)
    {
        $this->sqGrauAcessoArtefato = $sqGrauAcessoArtefato;
        return $this;
    }

    /**
     * Set dtAtribuicao
     *
     * @param datetime $dtAtribuicao
     * @return GrauAcessoArtefato
     */
    public function setDtAtribuicao($dtAtribuicao)
    {
        $this->dtAtribuicao = $dtAtribuicao;
        return $this;
    }

    /**
     * Get dtAtribuicao
     *
     * @return datetime
     */
    public function getDtAtribuicao()
    {
        return $this->dtAtribuicao;
    }

    /**
     * Set stAtivo
     *
     * @param boolean $stAtivo
     * @return GrauAcessoArtefato
     */
    public function setStAtivo($stAtivo)
    {
        $this->stAtivo = $stAtivo;
        return $this;
    }

    /**
     * Get stAtivo
     *
     * @return boolean
     */
    public function getStAtivo()
    {
        return $this->stAtivo;
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return GrauAcessoArtefato
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

    /**
     * Set sqGrauAcesso
     *
     * @param Sgdoce\Model\Entity\SgdoceGrauAcesso $sqGrauAcesso
     * @return GrauAcessoArtefato
     */
    public function setSqGrauAcesso($sqGrauAcesso = NULL)
    {
        $this->sqGrauAcesso = $sqGrauAcesso;
        return $this;
    }

    /**
     * Get sqGrauAcesso
     *
     * @return Sgdoce\Model\Entity\SgdoceGrauAcesso
     */
    public function getSqGrauAcesso()
    {
        return $this->sqGrauAcesso;
    }
}