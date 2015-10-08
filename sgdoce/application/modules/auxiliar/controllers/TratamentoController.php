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
 * Classe Controller TelefoneController
 *
 * @package      Principal
 * @subpackage   Controller
 * @name         TelefoneController
 * @version      1.0.0
 * @since        2012-08-21
 */
class Auxiliar_TratamentoController extends \Core_Controller_Action_CrudDto
{

    /** @var Principal\Service\Tratamento */
    protected $_service = 'Tratamento';

    /**
     * Metódo para recuperar a combo de tratamento
     */
    public function comboTratamentoAction ()
    {
        $dtoSearch = Core_Dto::factoryFromData($this->_getAllParams(), 'search');
        $result =  $this->getService('TratamentoVocativo')->getDadosTratamento($dtoSearch);
        $this->_helper->layout->disableLayout();
        $this->view->result = $result;
        return TRUE;
    }
}