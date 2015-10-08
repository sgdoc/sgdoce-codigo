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

/**
 * Emprestimo
 *
 * @ORM\Table(name="emprestimo")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Emprestimo")
 */
 class Emprestimo extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqEmprestimo
     *
     * @ORM\Column(name="sq_emprestimo", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqEmprestimo;

    /**
     * @var \Sgdoce\Model\Entity\CaixaHistorico
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\CaixaHistorico")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_caixa_historico", referencedColumnName="sq_caixa_historico", nullable=false)})
     */
    private $sqCaixaHistorico;

    /**
     * @var \Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumns({@ORM\JoinColumn(name="sq_pessoa_emprestimo", referencedColumnName="sq_pessoa", nullable=false)})
     */
    private $sqPessoaEmprestimo;

    /**
     * @var string $txMotivo
     * @ORM\Column(name="tx_motivo", type="string", length=250, nullable=false)
     */
    private $txMotivo;

    /**
     * @var string $noPessoaEntregue
     * @ORM\Column(name="no_pessoa_entregue", type="string", length=250, nullable=false)
     */
    private $noPessoaEntregue;


    public function getSqEmprestimo ()
    {
        return $this->sqEmprestimo;
    }

    public function getSqCaixaHistorico ()
    {
        return $this->sqCaixaHistorico;
    }

    public function getSqPessoaEmprestimo ()
    {
        return $this->sqPessoaEmprestimo;
    }

    public function getTxMotivo ()
    {
        return $this->txMotivo;
    }

    public function getNoPessoaEntregue ()
    {
        return $this->noPessoaEntregue;
    }

    /**
     *
     * @param integer $sqEmprestimo
     * @return \Sgdoce\Model\Entity\Emprestimo
     */
    public function setSqEmprestimo ($sqEmprestimo)
    {
        $this->sqEmprestimo = $sqEmprestimo;
        return $this;
    }
    /**
     *
     * @param \Sgdoce\Model\Entity\CaixaHistorico $entityCaixaHistorico
     * @return \Sgdoce\Model\Entity\Emprestimo
     */
    public function setSqCaixaHistorico (\Sgdoce\Model\Entity\CaixaHistorico $entityCaixaHistorico)
    {
        $this->sqCaixaHistorico = $entityCaixaHistorico;
        return $this;
    }

    /**
     *
     * @param \Sgdoce\Model\Entity\VwPessoa $entityPessoa
     * @return \Sgdoce\Model\Entity\Emprestimo
     */
    public function setSqPessoaEmprestimo (\Sgdoce\Model\Entity\VwPessoa $entityPessoa)
    {
        $this->sqPessoaEmprestimo = $entityPessoa;
        return $this;
    }

    /**
     *
     * @param string $txMotivo
     * @return \Sgdoce\Model\Entity\Emprestimo
     */
    public function setTxMotivo ($txMotivo)
    {
        $this->txMotivo = $txMotivo;
        return $this;
    }

    /**
     *
     * @param string $noPessoaEntregue
     * @return \Sgdoce\Model\Entity\Emprestimo
     */
    public function setNoPessoaEntregue ($noPessoaEntregue)
    {
        $this->noPessoaEntregue = $noPessoaEntregue;
        return $this;
    }


}