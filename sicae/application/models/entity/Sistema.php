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

namespace Sica\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\Sistema
 *
 * @ORM\Table(name="sistema")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\Sistema")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Sistema extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqSistema
     *
     * @ORM\Column(name="sq_sistema", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqSistema;

    /**
     * @var string $noSistema
     *
     * @ORM\Column(name="no_sistema", type="string", length=80, nullable=false)
     */
    private $noSistema;

    /**
     * @var string $sgSistema
     *
     * @ORM\Column(name="sg_sistema", type="string", length=12, nullable=false)
     */
    private $sgSistema;

    /**
     * @var text $txDescricao
     *
     * @ORM\Column(name="tx_descricao", type="text", nullable=true)
     */
    private $txDescricao;

    /**
     * @var bigint $sqArquitetura
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Arquitetura", inversedBy="arquiteturas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_arquitetura", referencedColumnName="sq_arquitetura")
     * })
     */
    private $sqArquitetura;

    /**
     * @var bigint $sqArquitetura
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Leiaute")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_leiaute", referencedColumnName="sq_leiaute")
     * })
     */
    private $sqLeiaute;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Pessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_responsavel", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoaResponsavel;

    /**
     * @var string $txEnderecoImagem
     *
     * @ORM\Column(name="tx_endereco_imagem", type="string", length=200, nullable=true)
     */
    private $txEnderecoImagem;

    /**
     * @var string $txUrl
     *
     * @ORM\Column(name="tx_url", type="string", length=80, nullable=false)
     */
    private $txUrl;

    /**
     * @var string $txUrl
     *
     * @ORM\Column(name="tx_url_help", type="string", length=80, nullable=false)
     */
    private $txUrlHelp;

    /**
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Menu", mappedBy="sqSistema")
     */
    private $sistema;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * @var Sica\Model\Entity\Perfil
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Perfil")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_perfil_externo_padrao", referencedColumnName="sq_perfil")
     * })
     */
    private $sqPerfilExternoPadrao;

    /**
     * Set sqSistema
     *
     * @return integer
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
     * Set noSistema
     *
     * @param string $noSistema
     * @return SicaSistema
     */
    public function setNoSistema($noSistema)
    {
        $this->noSistema = $noSistema;
        return $this;
    }

    /**
     * Get noSistema
     *
     * @return string
     */
    public function getNoSistema()
    {
        return $this->noSistema;
    }

    /**
     * Set sgSistema
     *
     * @param string $sgSistema
     * @return SicaSistema
     */
    public function setSgSistema($sgSistema)
    {
        $this->sgSistema = $sgSistema;
        return $this;
    }

    /**
     * Get sgSistema
     *
     * @return string
     */
    public function getSgSistema()
    {
        return $this->sgSistema;
    }

    /**
     * Set txUrl
     *
     * @param string $txUrl
     * @return SicaSistema
     */
    public function setTxUrl($txUrl)
    {
        $this->txUrl = $txUrl;
        return $this;
    }

    /**
     * Get txUrl
     *
     * @return string
     */
    public function getTxUrl()
    {
        return $this->txUrl;
    }

    /**
     * Set txUrl
     *
     * @param string $txUrl
     * @return SicaSistema
     */
    public function setTxUrlHelp($txUrl)
    {
        $this->txUrlHelp = $txUrl;
        return $this;
    }

    /**
     * Get txUrl
     *
     * @return string
     */
    public function getTxUrlHelp()
    {
        return $this->txUrlHelp;
    }

    /**
     * Set txEnderecoImagem
     *
     * @param string $txEnderecoImagem
     * @return SicaSistema
     */
    public function setTxEnderecoImagem($txEnderecoImagem)
    {
        $this->txEnderecoImagem = $txEnderecoImagem;
        return $this;
    }

    /**
     * Get txEnderecoImagem
     *
     * @return string
     */
    public function getTxEnderecoImagem()
    {
        return $this->txEnderecoImagem;
    }

    /**
     * Set stRegistroAtivo
     *
     * @param boolean $stRegistroAtivo
     * @return SicaSistema
     */
    public function setStRegistroAtivo($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;
        return $this;
    }

    /**
     * Get stRegistroAtivo
     *
     * @return string
     */
    public function getStRegistroAtivo()
    {
        return $this->stRegistroAtivo;
    }

    /**
     * Set txDescricao
     *
     * @param text $txDescricao
     * @return SicaSistema
     */
    public function setTxDescricao($txDescricao)
    {
        $this->txDescricao = $txDescricao;
        return $this;
    }

    /**
     * Get txDescricao
     *
     * @return text
     */
    public function getTxDescricao()
    {
        return $this->txDescricao;
    }

    /**
     * Set sqPessoaResponsavel
     *
     * @param Sica\Model\Entity\Pessoa $sqPessoaResponsavel
     * @return SicaSistema
     */
    public function setSqPessoaResponsavel(Pessoa $sqPessoaResponsavel = NULL)
    {
        $this->sqPessoaResponsavel = $sqPessoaResponsavel;
        return $this;
    }

    /**
     * Get sqPessoaResponsavel
     *
     * @return Sica\Model\Entity\Pessoa
     */
    public function getSqPessoaResponsavel()
    {
        if (NULL === $this->sqPessoaResponsavel) {
            $this->setSqPessoaResponsavel(new Pessoa());
        }
        return $this->sqPessoaResponsavel;
    }

    public function setSqArquitetura(Arquitetura $sqArquitetura)
    {
        $this->sqArquitetura = $sqArquitetura;
        return $this;
    }

    public function getSqArquitetura()
    {
        if (NULL === $this->sqArquitetura) {
            $this->setSqArquitetura(new Arquitetura());
        }
        return $this->sqArquitetura;
    }

    public function setSqLeiaute(Leiaute $sqLeiaute)
    {
        $this->sqLeiaute = $sqLeiaute;
        return $this;
    }

    public function getSqLeiaute()
    {
        if (NULL === $this->sqLeiaute) {
            $this->setSqLeiaute(new Leiaute());
        }
        return $this->sqLeiaute;
    }

    public function setSqPerfilExternoPadrao(Perfil $perfil = NULL)
    {
        $this->sqPerfilExternoPadrao = $perfil;
        return $this;
    }

    public function getSqPerfilExternoPadrao()
    {
        if (NULL === $this->sqPerfilExternoPadrao) {
            $this->setSqPerfilExternoPadrao(new Perfil());
        }

        return $this->sqPerfilExternoPadrao;
    }
}
