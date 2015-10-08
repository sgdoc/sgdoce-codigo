<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo Ã© parte do programa SISICMBio
 * O SISICMBio Ã© um software livre; vocÃª pode redistribuÃ­-lo e/ou modificÃ¡-lo dentro
 * dos termos da LicenÃ§a PÃºblica Geral GNU como publicada pela FundaÃ§Ã£o do Software Livre
 * (FSF); na versÃ£o 2 da LicenÃ§a.
 * Este programa Ã© distribuÃ­do na esperanÃ§a que possa ser  Ãºtil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implÃ­cita de ADEQUAÃ‡ÃƒO a qualquer  MERCADO ou APLICAÃ‡ÃƒO EM PARTICULAR.
 * Veja a LicenÃ§a PÃºblica Geral GNU/GPL em portuguÃªs para maiores detalhes.
 * VocÃª deve ter recebido uma cÃ³pia da LicenÃ§a PÃºblica Geral GNU, sob o tÃ­tulo "LICENCA.txt",
 * junto com este programa, se nÃ£o, acesse o Portal do Software PÃºblico Brasileiro no
 * endereÃ§o www.softwarepublico.gov.br ou escreva para a FundaÃ§Ã£o do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
namespace Sgdoce\Model\Entity;
use Doctrine\DBAL\Types\BooleanType;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sgdoce\Model\Entity\VwVinculoFuncional
 *
 * @ORM\Table(name="vw_vinculo_funcional")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwVinculoFuncional", readOnly=true)
 */
class VwVinculoFuncional extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqVinculoFuncional
     *
     * @ORM\Id
     * @ORM\Column(name="sq_vinculo_funcional", type="integer", nullable=false)
     */
    private $sqVinculoFuncional;

    /**
     * @var Sgdoce\Model\Entity\Corporativo\Pessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     */
    private $sqPessoa;

    /**
     * @var string $nuMatricula
     * @ORM\Column(name="nu_matricula", type="string", length=3, nullable=false)
     */
    private $nuMatricula;

    /**
     * @var Sgdoce\Model\Entity\Cargo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwCargo")
     * @ORM\JoinColumn(name="sq_cargo", referencedColumnName="sq_cargo")
     */
    private $sqCargo;

    /**
     * @var Sgdoce\Model\Entity\UnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumn(name="sq_unidade_exercicio", referencedColumnName="sq_pessoa")
     */
    private $sqUnidadeExercicio;

    /**
     * @var Sgdoce\Model\Entity\UnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumn(name="sq_unidade_lotacao", referencedColumnName="sq_pessoa")
     */
    private $sqUnidadeLotacao;

    /**
     * @var Sgdoce\Model\Entity\SituacaoFuncional
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwSituacaoFuncional")
     * @ORM\JoinColumn(name="sq_situacao_funcional", referencedColumnName="sq_situacao_funcional")
     */
    private $sqSituacaoFuncional;

    /**
     * @var Sgdoce\Model\Entity\VwFuncao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwFuncao")
     * @ORM\JoinColumn(name="sq_funcao", referencedColumnName="sq_funcao")
     */
    private $sqFuncao;

    public function setSqPessoa(VwPessoa $sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    public function getSqPessoa()
    {
        return $this->sqPessoa ? $this->sqPessoa : new VwPessoa();
    }

    /**
     * @return the $sqVinculoFuncional
     */
    public function getSqVinculoFuncional()
    {
        return $this->sqVinculoFuncional;
    }

    /**
     * @param integer $sqVinculoFuncional
     */
    public function setSqVinculoFuncional($sqVinculoFuncional)
    {
        $this->sqVinculoFuncional = $sqVinculoFuncional;
    }

    /**
     * @return the $nuMatricula
     */
    public function getNuMatricula()
    {
        return $this->nuMatricula;
    }

    /**
     * @param string $nuMatricula
     */
    public function setNuMatricula($nuMatricula)
    {
        $this->nuMatricula = $nuMatricula;
    }

    /**
     * @return the $sqUnidadeExercicio
     */
    public function getSqUnidadeExercicio()
    {
        return $this->sqUnidadeExercicio;
    }

    /**
     * @param string $sqUnidadeExercicio
     */
    public function setSqUnidadeExercicio($sqUnidadeExercicio)
    {
        $this->sqUnidadeExercicio = $sqUnidadeExercicio;
    }

    /**
     * @return the $sqUnidadeLotacao
     */
    public function getSqUnidadeLotacao()
    {
        return $this->sqUnidadeLotacao;
    }

    /**
     * @param string $sqUnidadeLotacao
     */
    public function setSqUnidadeLotacao($sqUnidadeLotacao)
    {
        $this->sqUnidadeLotacao = $sqUnidadeLotacao;
    }

    /**
     * @return the $sqCargo
     */
    public function getSqCargo()
    {
        return $this->sqCargo;
    }

    /**
     * @param string $sqCargo
     */
    public function setSqCargo($sqCargo)
    {
        $this->sqCargo = $sqCargo;
    }


    /**
     * @return the $sqSituacaoFuncional
     */
    public function getSqSituacaoFuncional()
    {
        return $this->sqSituacaoFuncional;
    }

    /**
     * @param string $sqSituacaoFuncional
     */
    public function setSqSituacaoFuncional($sqSituacaoFuncional)
    {
        $this->sqSituacaoFuncional = $sqSituacaoFuncional;
    }

    /**
     * @return the $sqFuncao
     */
    public function getSqFuncao()
    {
        return $this->sqFuncao;
    }

    /**
     * @param string $sqFuncao
     */
    public function setSqFuncao($sqFuncao)
    {
        $this->sqFuncao = $sqFuncao;
    }


}