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
 * Sgdoce\Model\Entity\Classificacao
 *
 * @ORM\Table(name="classificacao")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\Classificacao")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Classificacao extends \Core_Model_Entity_Abstract {

    /**
     * @var integer $sqClassificacao
     *
     * @ORM\Id
     * @ORM\Column(name="sq_classificacao", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqClassificacao;

    /**
     * @var string $txClassificacao
     *
     * @ORM\Column(name="tx_classificacao", type="string", length=255, nullable=false)
     */
    private $txClassificacao;

    /**
     * @var string $nuClassificacao
     *
     * @ORM\Column(name="nu_classificacao", type="string", length=15, nullable=false)
     */
    private $nuClassificacao;

    /**
     * @var Sgdoce\Model\Entity\Classificacao
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\Classificacao")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_classificacao_pai", referencedColumnName="sq_classificacao")
     * })
     */
    private $sqClassificacaoPai;


    /**
     *
     * @return integer
     */
    public function getSqClassificacao ()
    {
        return $this->sqClassificacao;
    }

    /**
     *
     * @return string
     */
    public function getTxClassificacao ()
    {
        return $this->txClassificacao;
    }

    /**
     *
     * @return string
     */
    public function getNuClassificacao ()
    {
        return $this->nuClassificacao;
    }

    /**
     *
     * @return Sgdoce\Model\Entity\Classificacao
     */
    public function getSqClassificacaoPai ()
    {
        return $this->sqClassificacaoPai;
    }

    public function setSqClassificacao ($sqClassificacao)
    {
        $this->sqClassificacao = $sqClassificacao;
        return $this;
    }

    public function setTxClassificacao ($txClassificacao)
    {
        $this->txClassificacao = $txClassificacao;
        return $this;
    }

    public function setNuClassificacao ($nuClassificacao)
    {
        $this->nuClassificacao = $nuClassificacao;
        return $this;
    }

    public function setSqClassificacaoPai (\Sgdoce\Model\Entity\Classificacao $sqClassificacaoPai)
    {
        $this->sqClassificacaoPai = $sqClassificacaoPai;
        return $this;
    }



}