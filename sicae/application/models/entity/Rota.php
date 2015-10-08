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
 * SISICMBio
 *
 * Classe para Entity Cep
 *
 * @package      Model
 * @subpackage   Entity
 * @name         Rota
 * @version      1.0.0
 * @since        2012-08-07
 */

/**
 * Sica\Model\Entity\Rota
 *
 * @ORM\Table(name="rota")
 * @ORM\Entity(repositoryClass="\Sica\Model\Repository\Rota")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class Rota extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqRota
     *
     * @ORM\Column(name="sq_rota", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    private $sqRota;

    /**
     * @var integer $txRota
     *
     * @ORM\Column(name="tx_rota", type="string", nullable=false)
     *
     */
    private $txRota;

    /**
     * @ORM\OneToMany(targetEntity="Sica\Model\Entity\Sistema", mappedBy="sqArquitetura")
     */
    private $arquiteturas;

    /**
     * @var Sica\Model\Entity\Funcionalidade
     *
     * @ORM\ManyToOne(targetEntity="Sica\Model\Entity\Funcionalidade", inversedBy="rotas")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_funcionalidade", referencedColumnName="sq_funcionalidade")
     * })
     */
    private $sqFuncionalidade;

    /**
     * @var integer $inRotaPrincipal
     */
    private $inRotaPrincipal = FALSE;

    /**
     *
     */
    public function setSqRota($sqRota)
    {
        $this->sqRota = $sqRota;
        return $this;
    }

    /**
     *
     */
    public function getSqRota()
    {
        return $this->sqRota;
    }

    public function setTxRota($txRota)
    {
        $this->txRota = $txRota;
        return $this;
    }

    /**
     *
     */
    public function getTxRota()
    {
        return $this->txRota;
    }

    /**
     *
     * @param boolean $inRotaPrincipal
     * @return \Sica\Model\Entity\Rota
     */
    public function setInRotaPrincipal($inRotaPrincipal)
    {
        $this->inRotaPrincipal = $inRotaPrincipal;
        return $this;
    }

    /**
     *
     * @return number
     */
    public function getInRotaPrincipal()
    {
        return $this->inRotaPrincipal;
    }

    /**
     * Set sqFuncionalidade
     *
     * @param Sica\Model\Entity\SicaFuncionalidade $sqFuncionalidade
     * @return SicaPerfilFuncionalidade
     */
    public function setSqFuncionalidade(Funcionalidade $sqFuncionalidade = NULL)
    {
        $this->sqFuncionalidade = $sqFuncionalidade;
        return $this;
    }

    /**
     * Get sqFuncionalidade
     *
     * @return Sica\Model\Entity\SicaFuncionalidade
     */
    public function getSqFuncionalidade()
    {
        if (NULL === $this->sqFuncionalidade) {
            $this->setSqFuncionalidade(new Funcionalidade());
        }

        return $this->sqFuncionalidade;
    }
}
