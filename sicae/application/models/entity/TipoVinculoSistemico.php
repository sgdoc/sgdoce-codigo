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
 * TipoVinculoSistemico
 *
 * @ORM\Table(name="vw_tipo_vinculo_sistemico")
 * @ORM\Entity(repositoryClass="Sica\Model\Repository\TipoVinculoSistemico")
 */
class TipoVinculoSistemico extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqTipoVinculoSistemico
     *
     * @ORM\Column(name="sq_tipo_vinculo_sistemico", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqTipoVinculoSistemico;

    /**
     * @var string $noTipoVinculoSistemico
     *
     * @ORM\Column(name="no_tipo_vinculo_sistemico", type="string", length=50, nullable=false)
     */
    private $noTipoVinculoSistemico;

    /**
     * @var $sqSistema
     *
     * @ORM\OneToOne(targetEntity="Sica\Model\Entity\Sistema")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_sistema", referencedColumnName="sq_sistema")
     * })
     */
    private $sqSistema;

    /**
     * Get sqTipoVinculoSistemico
     *
     * @return integer
     *
     */
    public function getSqTipoVinculoSistemico()
    {
        return $this->sqTipoVinculoSistemico;
    }

    /**
     * Set noTipoVinculoSistemico
     *
     * @param string $noTipoVinculoSistemico
     * @return TipoVinculoSistemico
     */
    public function setNoTipoVinculoSistemico($noTipoVinculoSistemico)
    {
        $this->noTipoVinculoSistemico = $noTipoVinculoSistemico;
        return $this;
    }

    /**
     * Get noTipoVinculoSistemico
     *
     * @return string
     */
    public function getNoTipoVinculoSistemico()
    {
        return $this->noTipoVinculoSistemico;
    }

    /**
     * Set sqSistema
     *
     * @param integer $sqSistema
     * @return TipoVinculoSistemico
     */
    public function setSqSistema(\Sica\Model\Entity\Sistema $sqSistema = NULL)
    {
        $this->sqSistema = $sqSistema;
        return $this;
    }

    /**
     * Get sqSistema
     *
     * @return integer
     */
    public function getSqSistema()
    {
        return $this->sqSistema ? $this->sqSistema : new \Sica\Model\Entity\Sistema();
    }

}