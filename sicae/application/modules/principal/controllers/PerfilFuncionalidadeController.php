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
 * Classe Controller PerfilFuncionalidade
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         PerfilFuncionalidade
 * @version      1.0.0
 * @since        2012-09-10
 */
class Principal_PerfilFuncionalidadeController extends \Core_Controller_Action_CrudDto
{

    /** @var Principal\Service\PerfilFuncionalidade */
    protected $_service = 'PerfilFuncionalidade';

    /**
     * @var array
     */
    protected $_optionsDtoEntity = array(
                    'entity'  => 'Sica\Model\Entity\PerfilFuncionalidade',
                    'mapping' => array(
                        'sqPerfil'  => array('sqPerfil' => 'Sica\Model\Entity\Perfil'),
                    )
    );

    protected function _factoryParamsExtrasSave($data)
    {
        if (array_key_exists('sqFuncionalidade', $data)) {
            foreach ($data['sqFuncionalidade'] as $value) {
                $arrayDto[] = Core_Dto::factoryFromData(
                    array('sqFuncionalidade' => $value),
                    'entity', array('entity' => 'Sica\Model\Entity\Funcionalidade')
                );
            }
        } else {
                $arrayDto = NULL;
        }

        return array($arrayDto);
    }
}