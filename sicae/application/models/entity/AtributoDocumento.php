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
 * Classe para Entity Tipo AtributoDocumento
 *
 * @package      Model
 * @subpackage     Entity
 * @name         AtributoDocumento
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sica\Model\Entity\AtributoDocumento
 *
 * @ORM\Table(name="vw_atributo_documento")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\AtributoDocumento", readOnly=true)
 */
class AtributoDocumento extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqAtributoDocumento
     *
     * @ORM\Column(name="sq_atributo_documento", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqAtributoDocumento;

    /**
     * @var string $noAtributoDocumento
     *
     * @ORM\Column(name="no_atributo_documento", type="string", length=30, nullable=false)
     */
    private $noAtributoDocumento;

    /**
     * Get sqAtributoDocumento
     *
     * @return integer
     */
    public function setSqAtributoDocumento($sqAtributoDocumento)
    {
        $this->sqAtributoDocumento = $sqAtributoDocumento;
        return $this;
    }

    /**
     * Get sqAtributoDocumento
     *
     * @return integer
     */
    public function getSqAtributoDocumento()
    {
        return $this->sqAtributoDocumento;
    }

    /**
     * Set noAtributoDocumento
     *
     * @param string noAtributoDocumento
     * @return AtributoDocumento
     */
    public function setNoAtributoDocumento($noAtributoDocumento)
    {
        $this->noAtributoDocumento = $noAtributoDocumento;
        return $this;
    }

    /**
     * Get noAtributoDocumento
     *
     * @return string
     */
    public function getNoAtributoDocumento()
    {
        return $this->noAtributoDocumento;
    }

}