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
 * Sica\Model\Repository\TipoEmail
 *
 * @ORM\Table(name="vw_tipo_email")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\TipoEmail", readOnly=true)
 */
class TipoEmail extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoEmail
     *
     * @ORM\Column(name="sq_tipo_email", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoEmail;

    /**
     * @var string $noTipoEmail
     *
     * @ORM\Column(name="no_tipo_email", type="string", length=20, nullable=false)
     */
    private $noTipoEmail;


    /**
     * Get sqTipoCampo
     *
     * @return integer
     */
    public function getSqTipoEmail()
    {
        return $this->sqTipoEmail;
    }

    /**
     * Set noTipoCampo
     *
     * @param string $noTipoCampo
     * @return SicaTipoCampo
     */
    public function setNoTipoEmail($noTipoTelefone)
    {
        $this->noTipoEmail = $noTipoTelefone;
        return $this;
    }

    /**
     * Get noTipoCampo
     *
     * @return string
     */
    public function getNoTipoEmail()
    {
        return $this->noTipoEmail;
    }
}