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
 * SISICMBio
 *
 * Classe para Entity Pessoa
 *
 * @package      Model
 * @subpackage     Entity
 * @name         Pessoa
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwPessoa
 *
 * @ORM\Table(name="vw_pessoa")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwPessoa", readOnly=true)
 */
class VwPessoa extends \Core_Model_Entity_Abstract
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
    * @var Sgdoce\Model\Entity\VwUnidadeOrg
    *
    * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
    * @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
    */
    private $sqPessoaParaUnidadeOrg;

    /**
     * @var string $noPessoa
     *
     * @ORM\Column(name="no_pessoa", type="string", nullable=true)
     */
    private $noPessoa;

    /**
     * @var integer $sqTipoPessoa
     *
     * @ORM\Column(name="sq_tipo_pessoa", type="integer", nullable=true)
     */
    private $sqTipoPessoa;
    /**
     * @var integer $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="integer", nullable=true)
     */
    private $stRegistroAtivo;

    /**
     * @var Sgdoce\Model\Entity\VwPessoaFisica
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoaFisica", mappedBy="sqPessoaFisica")
     */
    private $sqPessoaFisica;

    /**
     * @var Sgdoce\Model\Entity\Pessoa
     *
     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\PessoaSgdoce", mappedBy="sqPessoaCorporativo")
     */
    private $sqPessoaCorporativo;

    /**
     * @var Sgdoce\Model\Entity\VwPessoaCorporativo
     *
     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\VwProfissional", mappedBy="sqProfissional")
     */
    private $sqProfissional;

    /**
     * @var Sgdoce\Model\Entity\VwPessoaFuncionalidade
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoaFuncionalidade")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaParaPessoaFuncionalidade;

    /**
     * @var Sgdoce\Model\Entity\VwDocumento
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwDocumento", mappedBy="sqPessoa")
     */
    private $sqPessoaDocumento;

    /**
     * @var Sgdoce\Model\Entity\VwPessoaJuridica
     *
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwPessoaJuridica", mappedBy="sqPessoa")
     */
    private $sqPessoaJuridica;

    /**
     * @var Sgdoce\Model\Entity\VwVinculoFuncional
     *
     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\VwVinculoFuncional", mappedBy="sqPessoa")
     */
    private $sqVinculoFuncional;

    /**
     * @var Sgdoce\Model\Entity\VwPessoaVinculo
     *
     * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\VwPessoaVinculo", mappedBy="sqPessoa")
     */
    private $sqPessoaVinculo;

   /**
    * @var Sgdoce\Model\Entity\VwEndereco
    *
    * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\VwEndereco", mappedBy="sqPessoa")
    */
   private $sqEndereco;

   /**
    * @var Sgdoce\Model\Entity\VwEmail
    *
    * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\VwEmail", mappedBy="sqPessoa")
    */
   private $sqEmail;

   /**
    * @var Sgdoce\Model\Entity\VwTelefone
    *
    * @ORM\OneToMany(targetEntity="\Sgdoce\Model\Entity\VwTelefone", mappedBy="sqPessoa")
    */
   private $sqTelefone;

   /**
    * @var Sgdoce\Model\Entity\VwUnidadeOrgExterna
    *
    * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrgExterna")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
    * })
    */
   private $sqUnidadeOrgExterna;

   /**
    * @var Sgdoce\Model\Entity\VwUnidadeOrg
    *
    * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
    * })
    */
   private $sqUnidadeOrgInterna;

   /**
    * @var Sgdoce\Model\Entity\VwRppn
    *
    * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwRppn")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
    * })
    */
   private $sqRppn;

   /**
    * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwNaturezaJuridica")
    * @ORM\JoinColumn(name="sq_natureza_juridica", referencedColumnName="sq_natureza_juridica")
    */
   private $sqNaturezaJuridica;

   //comentado pois esse relacionamento ger loop

   /**
    * @ ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\PessoaSgdoce", mappedBy="sqPessoaCorporativo")
    */
//   private $sqPessoaSgdoce;

     /**
     * @ORM\OneToOne(targetEntity="Sgdoce\Model\Entity\VwCadastroSemCpf", mappedBy="sqPessoa")
     */
    private $sqCadastroSemCpf;

    /**
     * Set sqPessoa
     *
     * @param integer $sqPessoa
     * @return integer
     */
    public function setSqPessoa($sqPessoa = NULL)
    {
        $this->sqPessoa = $sqPessoa;
        if (!$sqPessoa) {
            $this->sqPessoa = NULL;
        }
        return $this;
    }

    /**
     * @return number
     */
    public function getSqPessoa()
    {
        return $this->sqPessoa;
    }


    /**
     * @return integer
     */
    public function getSqPessoaAssinatura()
    {
    	return $this->sqPessoa;
    }


    public function getSqPessoaOperacao()
    {
        return $this->getSqPessoa();
    }

    public function getSqPessoaRecebimento()
    {
        return $this->getSqPessoa();
    }

    /**
     * @return string
     */
    public function getNoPessoa()
    {
        return $this->noPessoa;
    }

    /**
     * @param unknown $noPessoa
     * @return \Sgdoce\Model\Entity\VwPessoa
     */
    public function setNoPessoa($noPessoa)
    {
        $this->noPessoa = $noPessoa;
        return $this;
    }

    /**
     * Get sqPessoaFisica
     *
     * @return Sgdoce\Model\Entity\VwPessoaFisica
     */
    public function getSqPessoaFisica()
    {
        return $this->sqPessoaFisica ? $this->sqPessoaFisica : new VwPessoaFisica();
    }

    public function setSqPessoaFisica(VwPessoaFisica $sqPessoaFisica)
    {
        $this->sqPessoaFisica = $sqPessoaFisica;


        return $this;
    }

    public function getSqPessoaJuridica()
    {
        return $this->sqPessoaJuridica ? $this->sqPessoaJuridica : new VwPessoaJuridica();
    }

    public function setSqPessoaJuridica(VwPessoaJuridica $sqPessoaJuridica)
    {
        $this->sqPessoaJuridica = $sqPessoaJuridica;

        return $this;
    }

    public function getSqPessoaDocumento()
    {
        return $this->sqPessoaDocumento;
    }

    public function setSqPessoaDocumento(VwDocumento $sqPessoaDocumento)
    {
        $this->sqPessoaDocumento = $sqPessoaDocumento;
    }

    public function getSqTipoPessoa()
    {
        return $this->sqTipoPessoa;
    }

    public function setSqTipoPessoa($sqTipoPessoa)
    {
        $this->sqTipoPessoa = $sqTipoPessoa;
    }

    public function getSqVinculoFuncional()
    {
        return $this->sqVinculoFuncional ? $this->sqVinculoFuncional : NULL;
    }

    public function setSqVinculoFuncional(VwVinculoFuncional $sqVinculoFuncional)
    {
        $this->sqVinculoFuncional = $sqVinculoFuncional;
    }

    public function getSqPessoaVinculo()
    {
        return $this->sqPessoaVinculo ? $this->sqPessoaVinculo : NULL;
    }

    public function setSqPessoaVinculo(VwPessoaVinculo $sqPessoaVinculo)
    {
        $this->sqPessoaVinculo = $sqPessoaVinculo;
    }

    public function getSqEndereco()
    {
        return $this->sqEndereco ? $this->sqEndereco : new VwEndereco();
    }

    public function setSqEndereco(VwEndereco $sqEndereco)
    {
        $this->sqEndereco = $sqEndereco;
    }

    public function getSqEmail()
    {
        return $this->sqEmail;
    }

    public function setSqEmail(VwEmail $sqEmail)
    {
        $this->sqEmail = $sqEmail;
    }

    public function getSqTelefone()
    {
        return $this->sqTelefone;
    }

    public function setSqTelefone(VwTelefone $sqTelefone)
    {
        $this->sqTelefone = $sqTelefone;
    }

    public function getSqProfissional()
    {
        return $this->sqProfissional ? $this->sqProfissional : new VwProfissional();
    }

    public function setSqProfissional(VwProfissional $sqProfissional)
    {
        $this->sqProfissional = $sqProfissional;
    }

    /**
     * @return Sgdoce\Model\Entity\VwNaturezaJuridica
     */
    public function setSqNaturezaJuridica($sqNaturezaJuridica = null)
    {
        $this->sqNaturezaJuridica = $sqNaturezaJuridica;

        return $this;
    }

    /**
     * @return Sgdoce\Model\Entity\VwPessoa $sqNaturezaJuridica
     */
    public function getSqNaturezaJuridica()
    {
        return $this->sqNaturezaJuridica
            ? $this->sqNaturezaJuridica
            : new VwNaturezaJuridica();
    }

    public function setSqCadastroSemCpf($sqCadastroSemCpf = null)
    {
        $this->sqCadastroSemCpf = $sqCadastroSemCpf;

        return $this;
    }

    public function getSqCadastroSemCpf()
    {
        return $this->sqCadastroSemCpf ? : new VwCadastroSemCpf();
    }

    public function getSqPessoaParaUnidadeOrg()
    {
        return $this->sqPessoaParaUnidadeOrg;
    }

    public function setSqPessoaAssinatura($sqPessoa)
    {
        $this->setSqPessoa($sqPessoa);
        return $this;
    }

    public function setSqPessoaOperacao($sqPessoa)
    {
        $this->setSqPessoa($sqPessoa);
        return $this;
    }

    public function setSqPessoaRecebimento($sqPessoa)
    {
        $this->setSqPessoa($sqPessoa);
        return $this;
    }

    public function getSqUnidadeOrgExterna ()
    {
        return $this->sqUnidadeOrgExterna;
    }

    public function setSqUnidadeOrgExterna (VwUnidadeOrgExterna $sqUnidadeOrgExterna)
    {
        $this->sqUnidadeOrgExterna = $sqUnidadeOrgExterna;
        return $this;
    }


}