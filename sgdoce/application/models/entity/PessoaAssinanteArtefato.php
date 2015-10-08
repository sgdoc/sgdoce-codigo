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
 * Classe para Entity PessoaAssinanteArtefato
 *
 * @package      Model
 * @subpackage     Entity
 * @name         PessoaAssinanteArtefato
 * @version     1.0.0
 * @since        2013-02-07
 */

/**
 * Sgdoce\Model\Entity\PessoaAssinanteArtefato
 *
 * @ORM\Table(name="pessoa_assinante_artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\PessoaAssinanteArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class PessoaAssinanteArtefato extends \Core_Model_Entity_Abstract
{

    /**
     * @var bigint $sqArtefato
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato", inversedBy="sqPessoaAssinanteArtefato" )
     * @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\TipoAssinante
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoAssinante")
     * @ORM\JoinColumn(name="sq_tipo_assinante", referencedColumnName="sq_tipo_assinante")
     */
    private $sqTipoAssinante;

    /**
     * @var string $noCargoAssinante
     * @ORM\Column(name="no_cargo_assinante", type="string", length=30, nullable=true)
     */
    private $noCargoAssinante;

    /**
     * @var Sgdoce\Model\Entity\PessoaUnidadeOrg
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PessoaUnidadeOrg")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="sq_pessoa_unidade_org", referencedColumnName="sq_pessoa_unidade_org")
     * })
     */
    private $sqPessoaUnidadeOrg;

    /**
     * @var Sgdoce\Model\Entity\Motivacao
     *
     * @ORM\ManyToMany(targetEntity="Sgdoce\Model\Entity\Motivacao", mappedBy="sqPessoaUnidadeOrg")
     */
    private $sqMotivacao;

    /**
     * @var datetime $dtAssinado
     *
     * @ORM\Column(name="dt_assinado", type="datetime", nullable=true)
     */
    private $dtAssinado;


    public function getSqArtefato()
    {
        return $this->sqArtefato;
    }

    public function setSqArtefato(Artefato $sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
    }

    public function getSqTipoAssinante()
    {
        return $this->sqTipoAssinante;
    }

    public function setSqTipoAssinante($sqTipoAssinante)
    {
        $this->sqTipoAssinante = $sqTipoAssinante;
    }

    public function getSqPessoaUnidadeOrg()
    {
    	return $this->sqPessoaUnidadeOrg ? $this->sqPessoaUnidadeOrg : new PessoaUnidadeOrg();
    }

    public function setSqPessoaUnidadeOrg(PessoaUnidadeOrg $sqPessoaUnidadeOrg)
    {
    	return $this->sqPessoaUnidadeOrg = $sqPessoaUnidadeOrg;
    }

    /**
     * @param mixed $noCargoAssinante
     */
    public function setNoCargoAssinante($noCargoAssinante)
    {
        $this->noCargoAssinante = $noCargoAssinante;
    }

    /**
     * @return mixed
     */
    public function getNoCargoAssinante()
    {
        return $this->noCargoAssinante;
    }

    public function getDtAssinado()
    {
        return $this->dtAssinado;
    }

    public function setDtAssinado($dtAssinatura)
    {
        $this->dtAssinado = $dtAssinatura;
    }
}