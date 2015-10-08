<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
 * da Licença Pública Geral GNU como publicada pela Fundação do Software Livre  (FSF); na versão
 * 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem
 * uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a
 * Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no endereço
 * www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre (FSF)
 * Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 * */

namespace Sica\Model\Entity;
use Doctrine\ORM\Mapping as ORM,
    Core\Model\OWM\Mapping as OWM;

/**
 * Sica\Model\Entity\TipoUnidadeOrg
 *
 * @ORM\Table (name="vw_tipo_unidade_org")
 * @ORM\Entity (repositoryClass="Sica\Model\Repository\TipoUnidadeOrg", readOnly=true)
 * @OWM\Endpoint (configKey="libcorp", repositoryClass="Sica\Model\Repository\DadoBancarioWs")
 */
class TipoUnidadeOrg extends \Core_Model_Entity_Abstract
{
    /**
     * @ORM\Column (name="sq_tipo_unidade_org", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * */
    private $sqTipoUnidadeOrg;

    /**
     * @ORM\Column (name="no_tipo_unidade_org", type="string", nullable=false)
     * */
    private $noTipoUnidadeOrg;

    /**
     * @ORM\Column (name="sg_tipo_unidade", type="string", length=80, nullable=false)
     * */
    private $sgTipoUnidade;

    /**
     * @ORM\Column (name="sq_tipo_unidade_pai", type="integer", nullable=false)
     * */
    private $sqTipoUnidadePai;

    /**
     * @ORM\Column (name="st_registro_ativo", type="boolean", nullable=false)
     * */
    private $stRegistroAtivo;

    /**
     * @ORM\Column (name="in_estrutura", type="boolean", nullable=false)
     * */
    private $inEstrutura;

    /**
     * Gets the value of sqTipoUnidadeOrg.
     *
     * @return mixed
     */
    public function getSqTipoUnidadeOrg ()
    {
        return $this->sqTipoUnidadeOrg;
    }

    /**
     * Sets the value of sqTipoUnidadeOrg.
     *
     * @param mixed $sqTipoUnidadeOrg the sq tipo unidade org
     *
     * @return self
     */
    public function setSqTipoUnidadeOrg ($sqTipoUnidadeOrg)
    {
        $this->sqTipoUnidadeOrg = $sqTipoUnidadeOrg;

        return $this;
    }

    /**
     * Gets the value of noTipoUnidadeOrg.
     *
     * @return mixed
     */
    public function getNoTipoUnidadeOrg ()
    {
        return $this->noTipoUnidadeOrg;
    }

    /**
     * Sets the value of noTipoUnidadeOrg.
     *
     * @param mixed $noTipoUnidadeOrg the no tipo unidade org
     *
     * @return self
     */
    public function setNoTipoUnidadeOrg ($noTipoUnidadeOrg)
    {
        $this->noTipoUnidadeOrg = $noTipoUnidadeOrg;

        return $this;
    }

    /**
     * Gets the value of sgTipoUnidade.
     *
     * @return mixed
     */
    public function getSgTipoUnidade ()
    {
        return $this->sgTipoUnidade;
    }

    /**
     * Sets the value of sgTipoUnidade.
     *
     * @param mixed $sgTipoUnidade the sg tipo unidade
     *
     * @return self
     */
    public function setSgTipoUnidade ($sgTipoUnidade)
    {
        $this->sgTipoUnidade = $sgTipoUnidade;

        return $this;
    }

    /**
     * Gets the value of sqTipoUnidadePai.
     *
     * @return mixed
     */
    public function getSqTipoUnidadePai ()
    {
        return $this->sqTipoUnidadePai;
    }

    /**
     * Sets the value of sqTipoUnidadePai.
     *
     * @param mixed $sqTipoUnidadePai the sq tipo unidade pai
     *
     * @return self
     */
    public function setSqTipoUnidadePai ($sqTipoUnidadePai)
    {
        $this->sqTipoUnidadePai = $sqTipoUnidadePai;

        return $this;
    }

    /**
     * Gets the value of stRegistroAtivo.
     *
     * @return mixed
     */
    public function getStRegistroAtivo ()
    {
        return $this->stRegistroAtivo;
    }

    /**
     * Sets the value of stRegistroAtivo.
     *
     * @param mixed $stRegistroAtivo the st registro ativo
     *
     * @return self
     */
    public function setStRegistroAtivo ($stRegistroAtivo)
    {
        $this->stRegistroAtivo = $stRegistroAtivo;

        return $this;
    }

    /**
     * Gets the value of inEstrutura.
     *
     * @return mixed
     */
    public function getInEstrutura ()
    {
        return $this->inEstrutura;
    }

    /**
     * Sets the value of inEstrutura.
     *
     * @param mixed $inEstrutura the in estrutura
     *
     * @return self
     */
    public function setInEstrutura ($inEstrutura)
    {
        $this->inEstrutura = $inEstrutura;

        return $this;
    }
}