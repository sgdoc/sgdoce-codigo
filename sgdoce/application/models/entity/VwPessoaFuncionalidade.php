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
 * Sgdoce\Model\Entity\VwPessoaFuncionalidade
 *
 * @ORM\Table(name="vw_pessoa_funcionalidade")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwPessoaFuncionalidade", readOnly=true)
 */
class VwPessoaFuncionalidade extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqPessoa
     *
     * @ORM\Column(name="sq_pessoa", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    private $sqPessoa;

    /**
     * @var Sgdoce\Model\Entity\VwPessoa
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoa")
     * @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     */
    private $sqPessoaParaPessoa;

    /**
     * @var Sgdoce\Model\Entity\VwPessoaVinculo
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwPessoaVinculo")
     * @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa_vinculo")
     */
    private $sqPessoaParaPessoaVinculo;

     /**
     * @var integer $sqSistema
     *
     * @ORM\Column(name="sq_sistema", type="integer", nullable=true)
     */
    private $sqSistema;

      /**
     * @var integer $sqFuncionalidade
     *
     * @ORM\Column(name="sq_funcionalidade", type="integer", nullable=true)
     */
    private $sqFuncionalidade;

    /**
     * @var integer $sqUsuario
     *
     * @ORM\Column(name="sq_usuario", type="integer", nullable=true)
     */
    private $sqUsuario;

     /**
     * Set sqPessoa
     *
     * @param integer $sqPessoa
     * @return PessoaFuncionalidade
     */
    public function setSqPessoa($sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
        return $this;
    }

    /**
     * Get sqPessoa
     *
     * @return integer
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa;
    }

     /**
     * Set sqPessoaParaPessoaVinculo
     *
     * @param integer $sqPessoaParaPessoaVinculo
     * @return PessoaVinculo
     */
    public function setSqPessoaParaPessoaVinculo($sqPessoaParaPessoaVinculo)
    {
        $this->sqPessoaParaPessoaVinculo = $sqPessoaParaPessoaVinculo;
        return $this;
    }

    /**
     * Get sqPessoaParaPessoaVinculo
     *
     * @return integer
     */
    public function getSqPessoaParaPessoaVinculo()
    {
        return $this->sqPessoaParaPessoaVinculo;
    }

     /**
     * Set sqSistema
     *
     * @param integer $sqSistema
     * @return PessoaFuncionalidade
     */
    public function setSqSistema($sqSistema)
    {
        $this->sqSistema = $sqSistema;
        return $this;
    }

    /**
     * Get sqSistema
     *
     * @return integer
     */
    public function getSqSistema()
    {
        return $this->sqSistema;
    }

       /**
     * Set sqFuncionalidade
     *
     * @param integer $sqFuncionalidade
     * @return PessoaFuncionalidade
     */
    public function setSqFuncionalidade($sqFuncionalidade)
    {
        $this->sqFuncionalidade = $sqFuncionalidade;
        return $this;
    }

    /**
     * Get sqFuncionalidade
     *
     * @return integer
     */
    public function getSqFuncionalidade()
    {
        return $this->sqFuncionalidade;
    }

    /**
     * Set sqUsuario
     *
     * @param integer $sqUsuario
     * @return PessoaFuncionalidade
     */
    public function setSqUsuario($sqUsuario)
    {
        $this->sqUsuario = $sqUsuario;
        return $this;
    }

    /**
     * Get sqUsuario
     *
     * @return integer
     */
    public function getSqUsuario()
    {
        return $this->sqUsuario;
    }
}