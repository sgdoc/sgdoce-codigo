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
 * Sgdoce\Model\Entity\VwTipoTelefone
 *
 * @ORM\Table(name="vw_tipo_telefone")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwTipoTelefone", readOnly=true)
 */
class VwTipoTelefone extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqTipoCampo
     *
     * @ORM\Column(name="sq_tipo_telefone", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoTelefone;

    /**
     * @var string $noTipoCampo
     *
     * @ORM\Column(name="no_tipo_telefone", type="string", length=20, nullable=false)
     */
    private $noTipoTelefone;


    /**
     * Get sqTipoCampo
     *
     * @return integer
     */
    public function getSqTipoTelefone()
    {
        return $this->sqTipoTelefone;
    }

    /**
     * Set noTipoCampo
     *
     * @param string $noTipoCampo
     * @return SicaTipoCampo
     */
    public function setNoTipoTelefone($noTipoTelefone)
    {
        $this->noTipoTelefone = $noTipoTelefone;
        return $this;
    }

    /**
     * Get noTipoCampo
     *
     * @return string
     */
    public function getNoTipoTelefone()
    {
        return $this->noTipoTelefone;
    }
}