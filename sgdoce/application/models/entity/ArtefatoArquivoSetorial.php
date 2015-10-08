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
 * Sgdoce\Model\Entity\ArtefatoArquivoSetorial
 *
 * @ORM\Table(name="artefato_arquivo_setorial")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\ArtefatoArquivoSetorial")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class ArtefatoArquivoSetorial extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqArtefatoArquivoSetorial
     *
     * @ORM\Id
     * @ORM\Column(name="sq_artefato_arquivo_setorial", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqArtefatoArquivoSetorial;

    /**
     * @var Sgdoce\Model\Entity\Artefato
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Artefato")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_artefato", referencedColumnName="sq_artefato")})
     */
    private $sqArtefato;

    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_unidade_arquivamento", referencedColumnName="sq_pessoa")})
     */
    private $sqUnidadeArquivamento;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_arquivamento", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoaArquivamento;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_desarquivamento", referencedColumnName="sq_pessoa")})
     */
    private $sqPessoaDesarquivamento;

    /**
     * @var \Zend_Date $dtArquivamento
     *
     * @ORM\Column(name="dt_arquivamento", type="zenddate", nullable=false)
     */
    private $dtArquivamento;

    /**
     * @var \Zend_Date $dtDesarquivamento
     *
     * @ORM\Column(name="dt_desarquivamento", type="zenddate", nullable=true)
     */
    private $dtDesarquivamento;

    /**
     *
     * @return integer
     */
    public function getSqArtefatoArquivoSetorial ()
    {
        return $this->sqArtefatoArquivoSetorial;
    }

    /**
     *
     * @return Sgdoce\Model\Entity\Artefato
     */
    public function getSqArtefato ()
    {
        return $this->sqArtefato;
    }

    /**
     *
     * @return VwUnidadeOrg
     */
    public function getSqUnidadeArquivamento ()
    {
        return $this->sqUnidadeArquivamento;
    }

    /**
     *
     * @return VwPessoa
     */
    public function getSqPessoaArquivamento ()
    {
        return $this->sqPessoaArquivamento;
    }

    /**
     *
     * @return VwPessoa
     */
    public function getSqPessoaDesarquivamento ()
    {
        return $this->sqPessoaDesarquivamento;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getDtArquivamento ()
    {
        return $this->dtArquivamento;
    }

    /**
     *
     * @return \Zend_Date
     */
    public function getDtDesarquivamento ()
    {
        return $this->dtDesarquivamento;
    }

    public function setSqArtefatoArquivoSetorial ($sqArtefatoArquivoSetorial)
    {
        $this->sqArtefatoArquivoSetorial = $sqArtefatoArquivoSetorial;
        return $this;
    }

    public function setSqArtefato (\Sgdoce\Model\Entity\Artefato $entityArtefato)
    {
        $this->sqArtefato = $entityArtefato;
        return $this;
    }

    public function setSqUnidadeArquivamento (\Sgdoce\Model\Entity\VwUnidadeOrg $entityUnidadeOrg)
    {
        $this->sqUnidadeArquivamento = $entityUnidadeOrg;
        return $this;
    }

    public function setSqPessoaArquivamento (\Sgdoce\Model\Entity\VwPessoa $entityPessoa)
    {
        $this->sqPessoaArquivamento = $entityPessoa;
        return $this;
    }

    public function setSqPessoaDesarquivamento (\Sgdoce\Model\Entity\VwPessoa $entityPessoa)
    {
        $this->sqPessoaDesarquivamento = $entityPessoa;
        return $this;
    }

    public function setDtArquivamento (\Zend_Date $dtArquivamento)
    {
        $this->dtArquivamento = $dtArquivamento;
        return $this;
    }

    public function setDtDesarquivamento (\Zend_Date $dtDesarquivamento)
    {
        $this->dtDesarquivamento = $dtDesarquivamento;
        return $this;
    }

}
