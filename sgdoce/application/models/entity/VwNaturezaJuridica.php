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
 * Classe para Entity Natureza Jurídica
 *
 * @package      Model
 * @subpackage   Entity
 * @name         NaturezaJuridica
 * @version      1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwNaturezaJuridica
 *
 * @ORM\Table(name="vw_natureza_juridica")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwNaturezaJuridica", readOnly=true)
 */
class VwNaturezaJuridica extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqNaturezaJuridica
     *
     * @ORM\Id
     * @ORM\Column(name="sq_natureza_juridica", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $sqNaturezaJuridica;

    /**
     * @var string $noNaturezaJuridica
     *
     * @ORM\Column(name="no_natureza_juridica", type="string", length=80, nullable=false)
     */
    private $noNaturezaJuridica;

    /**
     * @var string $sqNaturezaJuridicaPai
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwNaturezaJuridica")
     * @ORM\JoinColumn(name="sq_natureza_juridica_pai", referencedColumnName="sq_natureza_juridica")
     */
    private $sqNaturezaJuridicaPai;

    /**
     * Set $sqNaturezaJuridica
     *
     * @param  $sqNaturezaJuridica
     * @return VwNaturezaJuridica
     */
    public function setSqNaturezaJuridica($sqNaturezaJuridica)
    {
        $this->sqNaturezaJuridica = $sqNaturezaJuridica;

        return $this;
    }

    /**
     * Get $sqNaturezaJuridica
     *
     * @return $sqNaturezaJuridica
     */
    public function getSqNaturezaJuridica()
    {
        return $this->sqNaturezaJuridica;
    }

    /**
     * Set $noNaturezaJuridica
     *
     * @param string $noNaturezaJuridica
     * @return VwNaturezaJuridica
     */
    public function setNoNaturezaJuridica($noNaturezaJuridica)
    {
        $this->noNaturezaJuridica = $noNaturezaJuridica;

        return $this;
    }

    /**
     * Get $noNaturezaJuridica
     *
     * @return string
     */
    public function getNoNaturezaJuridica()
    {
        return $this->noNaturezaJuridica;
    }

    /**
     * Set $sqNaturezaJuridicaPai
     *
     * @param  $sqNaturezaJuridicaPai
     * @return VwNaturezaJuridica
     */
    public function setSqNaturezaJuridicaPai(VwNaturezaJuridica $sqNaturezaJuridicaPai)
    {
        $this->sqNaturezaJuridicaPai = $sqNaturezaJuridicaPai;

        return $this;
    }

    /**
     * Get $sqNaturezaJuridicaPai
     *
     * @return $sqNaturezaJuridicaPai
     */
    public function getSqNaturezaJuridicaPai()
    {
        return $this->sqNaturezaJuridicaPai ? : new VwNaturezaJuridica();
    }

}