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
 * Sgdoce\Model\Entity\Motivacao
 *
 * @ORM\Table(name="motivacao")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Motivacao")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Motivacao extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqMotivacao
     *
     * @ORM\Column(name="sq_motivacao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqMotivacao;

    /**
     * @var string $deMotivacao
     *
     * @ORM\Column(name="de_motivacao", type="string", length=30, nullable=false)
     */
    private $deMotivacao;

    /**
     * @var Sgdoce\Model\Entity\TipoMotivacao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\TipoMotivacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_tipo_motivacao", referencedColumnName="sq_tipo_motivacao")
     * })
     */
    private $sqTipoMotivacao;

    /**
     * @var Sgdoce\Model\Entity\PessoaUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\PessoaUnidadeOrg")
     * @ORM\JoinColumn(name="sq_pessoa_unidade_org", referencedColumnName="sq_pessoa_unidade_org")
     */
    private $sqPessoaUnidadeOrg;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")
     */
    private $sqArtefato;

    /**
     * Set sqMotivacao
     *
     * @param integer $sqMotivacao
     * @return integer
     */
    public function setSqMotivacao($sqMotivacao = NULL)
    {
        $this->sqMotivacao = $sqMotivacao;
        if(!$sqMotivacao){
            $this->sqMotivacao  = NULL;
        }
        return $this;
    }

    /**
     * Get sqTratamento
     *
     * @return integer
     */
    public function getSqMotivacao()
    {
        return $this->sqMotivacao;
    }

    /**
     * Set noTratamento
     *
     * @param string $noTratamento
     * @return Tratamento
     */
    public function setDeMotivacao($deMotivacao)
    {
        $this->deMotivacao = $deMotivacao;
        return $this;
    }

    /**
     * Get noTratamento
     *
     * @return string
     */
    public function getDeMotivacao()
    {
        return $this->deMotivacao;
    }

    /**
     * Set sqTipoMotivacao
     *
     * @param integer $sqTipoMotivacao
     * @return integer
     */
    public function setSqTipoMotivacao($sqTipoMotivacao = NULL)
    {
        $this->sqTipoMotivacao = $sqTipoMotivacao;
        return $this;
    }

    /**
     * Get sqTipoMotivacao
     *
     * @return integer
     */
    public function getSqTipoMotivacao()
    {
        return $this->sqTipoMotivacao ? $this->sqTipoMotivacao : new \Sgdoce\Model\Entity\TipoMotivacao();
    }

    public function setSqPessoaUnidadeOrg(PessoaUnidadeOrg $sqPessoaUnidadeOrg)
    {
        $this->sqPessoaUnidadeOrg = $sqPessoaUnidadeOrg;
        return $this;
    }

    public function getSqPessoaUnidadeOrg()
    {
        return $this->sqPessoaUnidadeOrg ? $this->sqPessoaUnidadeOrg : new \Sgdoce\Model\Entity\PessoaUnidadeOrg();
    }

    public function setSqPessoaFuncao($sqPessoaFuncao)
    {
        $this->sqPessoaFuncao = $sqPessoaFuncao;
        return $this;
    }

    public function getSqPessoaFuncao()
    {
        return $this->sqPessoaFuncao ? $this->sqPessoaFuncao : new \Sgdoce\Model\Entity\PessoaFuncao();
    }

    public function setSqArtefato($sqArtefato)
    {
        $this->sqArtefato = $sqArtefato;
        return $this;
    }

    public function getSqArtefato()
    {
        return $this->sqArtefato ? $this->sqArtefato : new \Sgdoce\Model\Entity\Artefato();
    }

}