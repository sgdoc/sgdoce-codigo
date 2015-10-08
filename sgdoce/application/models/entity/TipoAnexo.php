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
 * Sgdoce\Model\Entity\TipoAnexo
 *
 * @ORM\Table(name="tipo_anexo")
 * @ORM\Entity(repositoryClass="\Sgdoce\Model\Repository\TipoAnexo")
 * @OWM\Logger(eventLog="insert::update::delete")
 */
class TipoAnexo extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqTipoAnexo
     *
     * @ORM\Id
     * @ORM\Column(name="sq_tipo_anexo", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoAnexo;

    /**
     * @var string $noTipoAnexo
     *
     * @ORM\Column(name="no_tipo_anexo", type="string", length=20, nullable=false)
     */
    private $noTipoAnexo;

    /**
     * @var boolean $stRegistroAtivo
     *
     * @ORM\Column(name="st_registro_ativo", type="boolean", nullable=false)
     */
    private $stRegistroAtivo;

    /**
     * Set sqTipoArtefato
     *
     * @param $sqTipoAnexo
     * @return integer
     */
    public function setSqTipoAnexo($sqTipoAnexo)
    {
        $this->sqTipoAnexo = $sqTipoAnexo;
        return $this;
    }

    /**
     * Get sqTipoAnexo
     *
     * @return integer
     */
    public function getSqTipoAnexo()
    {
        return $this->sqTipoAnexo;
    }

    /**
     * Set noTipoAnexo
     *
     * @param $noTipoAnexo
     * @return string
     */
    public function setNoTipoAnexo($noTipoAnexo)
    {
        $this->noTipoArtefato = $noTipoAnexo;
        return $this;
    }

    /**
     * Get noTipoAnexo
     *
     * @return string
     */
    public function getNoTipoAnexo()
    {
        return $this->noTipoAnexo;
    }

    /**
     * Set noTipoAnexo
     *
     * @param $stRegistroAtivo
     * @return boolean
     */
    public function setStRegistroAtivo($stRegistroAtivo)
    {
    	$this->stRegistroAtivo = $stRegistroAtivo;
    	return $this;
    }

    /**
     * Get noTipoAnexo
     *
     * @return boolean
     */
    public function getStRegostroAtivo()
    {
    	return $this->stRegistroAtivo;
    }

}