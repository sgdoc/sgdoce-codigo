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
/**
 * SISICMBio
 *
 * Classe Service Usuario Perfil
 *
 * @package      Principal
 * @subpackage   Services
 * @name         UsuarioPerfil
 * @version      1.0.0
 * @since        2012-07-24
 */
namespace Principal\Service;

class UsuarioPerfil extends \Sica_Service_Crud
{
    /**
     * Nome da Entidade
     * @var string
     */
    protected $_entityName = 'app:UsuarioPerfil';

    /**
     *
     * @param int $sqUsuario
     */
    public function findSystensByUser($sqUsuario)
    {
        $userSystems = $this->_getRepository()->findSystensByUser($sqUsuario);
        $systems = array();
        foreach($userSystems as $system) {
            $systems[$system['sqSistema']] = $system;
        }

        return $systems;

    }

    /**
     *
     * @param \Core_Dto_Entity $dto
     * @return array
     */
    public function userUnit(\Core_Dto_Mapping $dto)
    {
        return $this->_getRepository()->userUnit($dto);
    }

    /**
     *
     * @param \Core_Dto_Entity $dto
     * @return array
     */
    public function unitProfile(\Core_Dto_Mapping $dto)
    {
        return $this->_getRepository()->unitProfile($dto);
    }

    public function findProfileByUnit(\Core_Dto_Mapping $dto)
    {
        return $this->_getRepository()->findProfileByUnit($dto);
    }

    public function getProfilesByUnit(\Core_Dto_Mapping $dto)
    {
        return $this->_getRepository()->getProfilesByUnit($dto);
    }

    public function countPerfil(\Core_Dto_Mapping $mapping)
    {
        return $this->_getRepository()->countPerfil($mapping);
    }

    public function countUsuarioByPerfil($perfil)
    {
        return count($this->_getRepository()->findBySqPerfil($perfil));
    }
}
