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
/**
 * SISICMBio
 *
 * Classe para Entity Tipo Endereco
 *
 * @package      Model
 * @subpackage     Entity
 * @name         TipoEndereco
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sica\Model\Entity\TipoEndereco
 *
 * @ORM\Table(name="vw_tipo_endereco")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\TipoEndereco", readOnly=true)
 */
class TipoEndereco extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoEndereco
     *
     * @ORM\Column(name="sq_tipo_endereco", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoEndereco;

    /**
     * @var text $noTipoEndereco
     *
     * @ORM\Column(name="no_tipo_endereco", type="text", nullable=false)
     */
    private $noTipoEndereco;


    /**
     * Get sqTipoEndereco
     *
     * @return integer
     */
    public function getSqTipoEndereco()
    {
        return $this->sqTipoEndereco;
    }

    /**
     * Set noTipoEndereco
     *
     * @param text $noTipoEndereco
     * @return TipoEndereco
     */
    public function setNoTipoEndereco($noTipoEndereco)
    {
        $this->noTipoEndereco = $noTipoEndereco;
        return $this;
    }

    /**
     * Get noTipoEndereco
     *
     * @return text
     */
    public function getNoTipoEndereco()
    {
        return $this->noTipoEndereco;
    }
}