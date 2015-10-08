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
 * Sgdoce\Model\Entity\HistoricoArtefato
 *
 * @ORM\Table(name="historico_artefato")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\HistoricoArtefato")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class HistoricoArtefato extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqHistoricoArtefato
     *
     * @ORM\Id
     * @ORM\Column(name="sq_historico_artefato", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqHistoricoArtefato;

    /**
     * @var datetime $dtOcorrencia
     *
     * @ORM\Column(name="dt_ocorrencia", type="zenddate", nullable=false)
     */
    private $dtOcorrencia;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_org", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUnidadeOrg;

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
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var Sgdoce\Model\Entity\Ocorrencia
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Ocorrencia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_ocorrencia", referencedColumnName="sq_ocorrencia")
     * })
     */
    private $sqOcorrencia;

    /**
     * @var text $txDescricaoOperacao
     *
     * @ORM\Column(name="tx_descricao_operacao", type="text", nullable=false)
     */
    private $txDescricaoOperacao;

    /**
     * Set $sqHistoricoArtefato
     *
     * @param $HistoricoArtefato $sqHistoricoArtefato
     * @return HistoricoArtefato
     */
    public function setSqHistoricoArtefato($sqHistoricoArtefato)
    {
        $this->sqHistoricoArtefato = $sqHistoricoArtefato;
        return $this;
    }

    /**
     * Get sqHistoricoArtefato
     *
     * @return integer
     */
    public function getSqHistoricoArtefato()
    {
        return $this->sqHistoricoArtefato;
    }

    /**
     * Set dtOcorrencia
     *
     * @param datetime $dtOcorrencia
     * @return HistoricoArtefato
     */
    public function setDtOcorrencia($dtOcorrencia)
    {
        $this->dtOcorrencia = $dtOcorrencia;
        return $this;
    }

    /**
     * Get dtOcorrencia
     *
     * @return datetime
     */
    public function getDtOcorrencia()
    {
        return $this->dtOcorrencia;
    }

    /**
     * Set sqUnidadeOrg
     *
     * @param Sgdoce\Model\Entity\VwUnidadeOrg $sqUnidadeOrg
     * @return HistoricoArtefato
     */
    public function setSqUnidadeOrg(VwUnidadeOrg $sqUnidadeOrg = NULL)
    {
        $this->sqUnidadeOrg = $sqUnidadeOrg;
        return $this;
    }

    /**
     * Get sqUnidadeOrg
     *
     * @return Sgdoce\Model\Entity\VwUnidadeOrg
     */
    public function getSqUnidadeOrg()
    {
        return $this->sqUnidadeOrg;
    }

    /**
     * Set sqArtefato
     *
     * @param Sgdoce\Model\Entity\Artefato $sqArtefato
     * @return HistoricoArtefato
     */
    public function setSqArtefato(Artefato $sqArtefato = NULL)
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
     * Set sqPessoa
     *
     * @param Sgdoce\Model\Entity\VwPessoa $sqPessoa
     * @return HistoricoArtefato
     */
    public function setSqPessoa(VwPessoa $sqPessoa = NULL)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return Sgdoce\Model\Entity\VwPessoa
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa;
    }

    /**
     * Set sqOcorrencia
     *
     * @param Sgdoce\Model\Entity\Ocorrencia $sqOcorrencia
     * @return HistoricoArtefato
     */
    public function setSqOcorrencia(Ocorrencia $sqOcorrencia = NULL)
    {
        $this->sqOcorrencia = $sqOcorrencia;
        return $this;
    }

    /**
     * Get sqOcorrencia
     *
     * @return Sgdoce\Model\Entity\Ocorrencia
     */
    public function getSqOcorrencia()
    {
        return $this->sqOcorrencia;
    }

    /**
     * Set txDescricaoOperacao
     *
     * @param text $txDescricaoOperacao
     * @return HistoricoArtefato
     */
    public function setTxDescricaoOperacao($txDescricaoOperacao)
    {
        $this->txDescricaoOperacao = $txDescricaoOperacao;
        return $this;
    }

    /**
     * Get txJustificativa
     *
     * @return text
     */
    public function getTxDescricaoOperacao()
    {
        return $this->txDescricaoOperacao;
    }
}
