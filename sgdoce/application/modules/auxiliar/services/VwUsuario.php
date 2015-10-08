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
 * Classe Service Usuario
 *
 * @package      Auxiliar
 * @subpackage   Services
 * @name         VwUsuario
 * @since        2014-10-31
 */

namespace Auxiliar\Service;

class VwUsuario extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:VwUsuario';

    /**
     *
     * @param \Core_Dto_Search $dto
     * @return boolean
     */
    public function isUserSgi(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->isUserSgi($dto);
    }

    /**
     *
     * @param \Core_Dto_Search $dto
     * @return boolean
     */
    public function isGestor(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->isGestor($dto);
    }

    /**
     * Formata dados para retornar para uma combo.
     *
     * @param \Core_Dto_Search $dto
     * @param string $key - índice.
     * @param string $value - valor.
     * @return array
     */
    public function comboPorPerfil( $dto, $key = 'sqPessoa', $value = 'noPessoa' )
    {
        $listUsuariosPorPerfil = $this->_getRepository()
                                      ->listUsuarioPorPerfil($dto);
        $out = array();
        foreach( $listUsuariosPorPerfil as $usuario ) {
            if( isset($usuario[$key])
                && isset($usuario[$value]) ) {
                $out[$usuario[$key]] = $usuario[$value];
            }
        }
        return $out;
    }
}