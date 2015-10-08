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
 * Sgdoce\Model\Entity\VwFeriado
 *
 * @ORM\Table(name="vw_feriado")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\VwFeriado", readOnly=true)
 */
class VwFeriado extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqFeriado
     *
     * @ORM\Id
     * @ORM\Column(name="sq_feriado", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqFeriado;

    /**
     * @var string $deFeriado
     *
     * @ORM\Column(name="de_feriado", type="string", length=200, nullable=false)
     */
    private $deFeriado;

    /**
     * @var zenddate $dtFeriado
     * @ORM\Column(name="dt_feriado", type="zenddate", nullable=false)
     */
    private $dtFeriado;

    public function getSqFeriado()
    {
        return $this->sqFeriado;
    }

    public function setSqFeriado($sqFeriado)
    {
        $this->sqFeriado = $sqFeriado;
    }

    public function getDeFeriado()
    {
        return $this->deFeriado;
    }

    public function setDeFeriado($deFeriado)
    {
        $this->deFeriado = $deFeriado;
    }

    public function getDtFeriado()
    {
        return $this->dtFeriado;
    }

    public function setDtFeriado($dtFeriado)
    {
        $this->dtFeriado = $dtFeriado;
    }
}