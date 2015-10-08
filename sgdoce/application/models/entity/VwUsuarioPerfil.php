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
 * Classe para Entity Vw Usuário Perfil
 *
 * @package      Model
 * @subpackage   Entity
 * @name         VwUsuarioPerfil
 * @version      1.0.0
 * @since        2014-12-03
 */

/**
 * Sgdoce\Model\Entity\VwUsuarioPerfil
 *
 * @ORM\Table(name="vw_usuario_perfil")
 * @ORM\Entity(repositoryClass="Sgdoce\Model\Repository\VwUsuarioPerfil", readOnly=true)
 */
class VwUsuarioPerfil extends \Core_Model_Entity_Abstract
{

    /**
     * @var integer $sqUsuarioPerfil
     *
     * @ORM\Column(name="sq_usuario_perfil", type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Id
     */
    private $sqUsuarioPerfil;
    

    /**
     * @var Sgdoce\Model\Entity\VwUsuario
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUsuario")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_usuario", referencedColumnName="sq_usuario")
     * })
     */
    private $sqUsuario;


    /**
     * @var Sgdoce\Model\Entity\VwUnidadeOrg
     *
     * @ORM\ManyToOne(targetEntity="Sgdoce\Model\Entity\VwUnidadeOrg")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sq_unidade_org_pessoa", referencedColumnName="sq_unidade_org")
     * })
     */
    private $sqUnidadeOrgPessoa;
}