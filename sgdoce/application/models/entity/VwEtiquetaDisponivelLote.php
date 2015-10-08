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
 */

namespace Sgdoce\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Atribuicao
 *
 * @ORM\Table(name="vw_etiqueta_disponivel_lote")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwEtiquetaDisponivelLote")
 */
 class VwEtiquetaDisponivelLote extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $sqLoteEtiqueta
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\LoteEtiqueta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_lote_etiqueta", referencedColumnName="sq_lote_etiqueta")
     * })
     * @ORM\Id 
     */
    private $sqLoteEtiqueta;

    /**
     * @var string $nuQuantidade
     *
     * @ORM\Column(name="nu_quantidade", type="integer", nullable=false)
     */
    private $nuQuantidade;

    /**
     * @var string $nuQuantidadeDisponivel
     *
     * @ORM\Column(name="nu_quantidade_disponivel", type="integer", nullable=false)
     */
    private $nuQuantidadeDisponivel;

    /**
     * Get sqLoteEtiqueta
     *
     * @return integer
     */
    public function getSqLoteEtiqueta()
    {
        return $this->sqLoteEtiqueta;
    }

    /**
     * Get nuQuantidade
     *
     * @return integer
     */
    public function getNuQuantidade()
    {
        return $this->nuQuantidade;
    }

    /**
     * Get nuQuantidadeDisponivel
     *
     * @return integer
     */
    public function getNuQuantidadeDisponivel()
    {
        return $this->nuQuantidadeDisponivel;
    }

    /**
     * Set sqLoteEtiqueta
     *
     * @param integer $sqLoteEtiqueta
     * @return self
     */
    public function setSqLoteEtiqueta($sqLoteEtiqueta)
    {
        $this->sqLoteEtiqueta = $sqLoteEtiqueta;
        return $this;
    }

    /**
     * Set nuQuantidade
     *
     * @param integer $nuQuantidade
     * @return self
     */
    public function setNuQuantidade($nuQuantidade)
    {
        $this->nuQuantidade = $nuQuantidade;
        return $this;
    }

    /**
     * Set nuQuantidadeDisponivel
     *
     * @param integer $nuQuantidadeDisponivel
     * @return self
     */
    public function setNuQuantidadeDisponivel($nuQuantidadeDisponivel)
    {
        $this->nuQuantidadeDisponivel = $nuQuantidadeDisponivel;
        return $this;
    }
}