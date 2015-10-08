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
 * Sgdoce\Model\Entity\PessoaFuncao
 *
 * @ORM\Table(name="pessoa_funcao")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\PessoaFuncao")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class PessoaFuncao extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqPessoaFuncao
     *
     * @ORM\Column(name="sq_pessoa_funcao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqPessoaFuncao;

    /**
     * @var string $noPessoaFuncao
     *
     * @ORM\Column(name="no_pessoa_funcao", type="string", length=20, nullable=false)
     */
    private $noPessoaFuncao;

    /**
     * Set sqPessoaFuncao
     *
     * @param integer $sqPessoaFuncao
     * @return integer
     */
    public function setSqPessoaFuncao($sqPessoaFuncao = NULL)
    {
        $this->sqPessoaFuncao = $sqPessoaFuncao;
        if(!$sqPessoaFuncao){
            $this->sqPessoaFuncao  = NULL;
        }
        return $this;
    }

    /**
     * Get sqPessoaFuncao
     *
     * @return integer
     */
    public function getSqPessoaFuncao()
    {
        return $this->sqPessoaFuncao;
    }

    /**
     * Set noPessoaFuncao
     *
     * @param string $noPessoaFuncao
     * @return PessoaFuncao
     */
    public function setNoPessoaFuncao($noPessoaFuncao)
    {
        $this->noPessoaFuncao = $noPessoaFuncao;
        return $this;
    }

    /**
     * Get noPessoaFuncao
     *
     * @return string
     */
    public function getNoPessoaFuncao()
    {
        return $this->noPessoaFuncao;
    }
}