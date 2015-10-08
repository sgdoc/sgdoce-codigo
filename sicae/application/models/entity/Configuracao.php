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
 * Sica\Model\Entity\Configuracao
 *
 * @ORM\Table(name="configuracao")
 * @ORM\Entity(repositoryClass="Doctrine\ORM\EntityRepository")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Configuracao extends \Core_Model_Entity_Abstract implements \Core_Model_Entity_Configuracao_Interface
{
    /**
     * @var integer $sqConfiguracao
     *
     * @ORM\Column(name="sq_configuracao", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqConfiguracao;

    /**
     * @var string $noConfiguracao
     *
     * @ORM\Column(name="no_configuracao", type="string", length=200, nullable=false)
     */
    private $noConfiguracao;

    /**
     * @var string $noConstante
     *
     * @ORM\Column(name="no_constante", type="string", length=50, nullable=true)
     */
    private $noConstante;

    /**
     * @var string $noOwner
     *
     * @ORM\Column(name="no_owner", type="string", length=50, nullable=false)
     */
    private $noOwner;

    /**
     * @var string $noTabela
     *
     * @ORM\Column(name="no_tabela", type="string", length=50, nullable=false)
     */
    private $noTabela;

    /**
     * @var string $noColuna
     *
     * @ORM\Column(name="no_coluna", type="string", length=50, nullable=false)
     */
    private $noColuna;

    /**
     * @var integer $sqValor
     *
     * @ORM\Column(name="sq_valor", type="integer", nullable=false)
     */
    private $sqValor;


    /**
     * Get sqConfiguracao
     *
     * @return integer
     */
    public function getSqConfiguracao()
    {
        return $this->sqConfiguracao;
    }

    /**
     * Set noConfiguracao
     *
     * @param string $noConfiguracao
     * @return SicaConfiguracao
     */
    public function setNoConfiguracao($noConfiguracao)
    {
        $this->noConfiguracao = $noConfiguracao;
        return $this;
    }

    /**
     * Get noConfiguracao
     *
     * @return string
     */
    public function getNoConfiguracao()
    {
        return $this->noConfiguracao;
    }

    /**
     * Set noConstante
     *
     * @param string $noConstante
     * @return SicaConfiguracao
     */
    public function setNoConstante($noConstante)
    {
        $this->noConstante = $noConstante;
        return $this;
    }

    /**
     * Get noConstante
     *
     * @return string
     */
    public function getNoConstante()
    {
        return $this->noConstante;
    }

    /**
     * Set noOwner
     *
     * @param string $noOwner
     * @return SicaConfiguracao
     */
    public function setNoOwner($noOwner)
    {
        $this->noOwner = $noOwner;
        return $this;
    }

    /**
     * Get noOwner
     *
     * @return string
     */
    public function getNoOwner()
    {
        return $this->noOwner;
    }

    /**
     * Set noTabela
     *
     * @param string $noTabela
     * @return SicaConfiguracao
     */
    public function setNoTabela($noTabela)
    {
        $this->noTabela = $noTabela;
        return $this;
    }

    /**
     * Get noTabela
     *
     * @return string
     */
    public function getNoTabela()
    {
        return $this->noTabela;
    }

    /**
     * Set noColuna
     *
     * @param string $noColuna
     * @return SicaConfiguracao
     */
    public function setNoColuna($noColuna)
    {
        $this->noColuna = $noColuna;
        return $this;
    }

    /**
     * Get noColuna
     *
     * @return string
     */
    public function getNoColuna()
    {
        return $this->noColuna;
    }

    /**
     * Set sqValor
     *
     * @param integer $sqValor
     * @return SicaConfiguracao
     */
    public function setSqValor($sqValor)
    {
        $this->sqValor = $sqValor;
        return $this;
    }

    /**
     * Get sqValor
     *
     * @return integer
     */
    public function getSqValor()
    {
        return $this->sqValor;
    }
}