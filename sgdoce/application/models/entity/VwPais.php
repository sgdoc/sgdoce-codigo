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
 * Classe para Entity País
 *
 * @package      Model
 * @subpackage   Entity
 * @name         Pais
 * @version      1.0.0
 * @since        2012-11-05
 */

/**
 * Sgdoce\Model\Entity\VwPais
 *
 * @ORM\Table(name="vw_pais")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwPais", readOnly=true)
 */
class VwPais extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqPais
     *
     * @ORM\Column(name="sq_pais", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    private $sqPais;

    /**
     * @var string $noPais
     *
     * @ORM\Column(name="no_pais", type="string", nullable=true)
     */
    private $noPais;

    /**
     * Set sqTipoPessoa
     *
     * @param integer $sqTipoPessoa
     * @return integer
     */
    public function setSqPais($sqPais = null)
    {
        $this->sqPais = $sqPais;
        if (!$sqPais) {
            $this->sqPais = null;
        }
        return $this;
    }

    public function getSqPais()
    {
        return $this->sqPais;
    }

    public function setNoPais($noPais)
    {
        $this->noPais = $noPais;

        return $this;
    }

    public function getNoPais()
    {
        return $this->noPais;
    }
}