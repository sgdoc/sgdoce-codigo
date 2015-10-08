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
 * Classe para Serviço VwCargo
 *
 * @package      Auxiliar
 * @subpackage   Servico
 * @name         VwCargo
 * @version      1.0.0
 * @since        2014-12-03
 */

namespace Auxiliar\Service;

class VwCargo extends \Core_ServiceLayer_Service_CrudDto
{
	/**
     * @var string
     */
    protected $_entityName = 'app:VwCargo';

    /**
     * Retorna os cargos
     * @param boolean $withSelect
     * @return array
     */
    public function comboCargo($withSelect=true)
    {
        return $this->_getRepository()->comboCargo($withSelect);
    }

    /**
     * Retorna os cargos
     * @return array
     */
    public function comboCargoCadastroDocumento()
    {
        $aux = array();
        $combo = $this->comboCargo(false);
        foreach ($combo as $value) {
            $aux[$value] = $value;
        }
        return $aux;
    }



}
