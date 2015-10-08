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
 *
 *
 * @package      Model
 * @subpackage   Entity
 * @name         VwIntegracaoTaxon
 * @version      1.0.0
 * @since        2012-11-05
 */

/**
 * Sgdoce\Model\Entity\VwIntegracaoTaxon
 *
 * @ORM\Table(name="vw_integracao_sgdoce_especies")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwIntegracaoSistema", readOnly=true)
 */
class VwIntegracaoTaxon extends \Core_Model_Entity_Abstract
{
    /**
     * @var integer $codigo
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    private $codigo;

    /**
     * @var string $nome
     *
     * @ORM\Column(name="nome", type="string", nullable=true)
     */
    private $nome;

    /**
     * Set codigo
     *
     * @param integer $codigo
     * @return integer
     */
    public function setCodigo($codigo = null)
    {
        $this->codigo = $codigo;
        if (!$codigo) {
            $this->codigo = null;
        }
        return $this;
    }

    public function getCodigo()
    {
        return $this->codigo;
    }

    public function setNome($nome)
    {
        $this->nome = $nome;

        return $this;
    }

    public function getNome()
    {
        return $this->nome;
    }
    }