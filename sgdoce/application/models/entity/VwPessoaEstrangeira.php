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
 * Classe para Entity VwPessoaEstrangeira
 *
 * @package      Model
 * @subpackage  Entity
 * @name         Pessoa
 * @version     1.0.0
 * @since        2012-06-26
 */

/**
 * Sgdoce\Model\Entity\VwPessoaEstrangeira
 *
 * @ORM\Table(name="vw_pessoa_estrangeira")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwPessoaEstrangeira", readOnly=true)
 */
class VwPessoaEstrangeira extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqPessoaEstrangeira
     *
     * @ORM\Column(name="sq_pessoa", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    private $sqPessoaEstrangeira;

    /**
     * @var integer $nuPassaporte
     *
     * @ORM\Column(name="nu_passaporte", type="integer", nullable=true)
     */
    private $nuPassaporte;

    public function getSqPessoaEstrangeira()
    {
        return $this->sqPessoaEstrangeira;
    }

    public function getNuPassaporte()
    {
        return $this->nuPassaporte;
    }

    public function setNuPassaportef($nuPassaporte)
    {
        $this->nuPassaporte = $nuPassaporte;
        return $this;
    }
}