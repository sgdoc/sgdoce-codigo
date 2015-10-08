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

use Doctrine\ORM\Mapping as ORM,
    Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\CadastroSemCpf
 *
 * @ORM\Table(name="vw_cadastro_sem_cpf")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\CadastroSemCpf", readOnly=true)
 * @OWM\Endpoint(configKey="libcorp" , repositoryClass="Sica\Model\Repository\CadastroSemCpfWs")
 */
class CadastroSemCpf extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqCadastroSemCpf
     *
     * @ORM\Column(name="sq_cadastro_sem_cpf", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqCadastroSemCpf;

    /**
     * @var string $dtInclusao
     *
     * @ORM\Column(name="dt_inclusao", type="zenddate", nullable=false)
     */
    private $dtInclusao;

    /**
     * @var string $dtInclusao
     *
     * @ORM\Column(name="tx_justificativa", type="string", nullable=false)
     */
    private $txJustificativa;

    /**
     * @var Sica\Model\Entity\Pessoa
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\Pessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqPessoa;

    /**
     * @var Sica\Model\Entity\Usuario
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Pessoa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_pessoa_autora", referencedColumnName="sq_pessoa")
     * })
     */
    private $sqUsuario;

    public function getSqCadastroSemCpf()
    {
        return $this->sqCadastroSemCpf;
    }

    public function setSqCadastroSemCpf($sqCadastroSemCpf)
    {
        $this->sqCadastroSemCpf = $sqCadastroSemCpf;
    }

    public function getDtInclusao()
    {
        return $this->dtInclusao;
    }

    public function setDtInclusao($dtInclusao)
    {
        $this->dtInclusao = $dtInclusao;
    }

    public function getTxJustificativa()
    {
        return $this->txJustificativa;
    }

    public function setTxJustificativa($txJustificativa)
    {
        $this->txJustificativa = $txJustificativa;
    }

    public function getSqPessoa()
    {
        return $this->sqPessoa ? $this->sqPessoa : new \Sica\Model\Entity\Pessoa();
    }

    public function setSqPessoa(\Sica\Model\Entity\Pessoa $sqPessoa)
    {
        $this->sqPessoa = $sqPessoa;
    }

    public function getSqUsuario()
    {
        return $this->sqUsuario ? $this->sqUsuario : new \Sica\Model\Entity\Usuario();
    }

    public function setSqUsuario(\Sica\Model\Entity\Usuario $sqUsuario)
    {
        $this->sqUsuario = $sqUsuario;
    }

}