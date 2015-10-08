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
 * Classe Service Função
 *
 * @package      Principal
 * @subpackage   Services
 * @name         VwFuncao
 * @version      1.0.0
 * @since        2015-08-13
 */

namespace Auxiliar\Service;

class Funcao extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:VwFuncao';
    
    /**
     * Método que retorna pesquisa do banco para preencher combo
     * @return array
     */
    public function comboFuncao()
    {
        $bFuncao = $this->_getRepository()->buscarFuncao();
        $arrComboFuncao = array();
        foreach ($bFuncao as $comboFuncao) {
            $arrComboFuncao[$comboFuncao->getSqFuncao()] = $comboFuncao->getNoFuncao();
        }
        return $arrComboFuncao;
    }

    /**
     * Retorna as funções com chave e valor recebendo o nome da função (no_funcao)
     * @return array
     */
    public function comboFuncaoCadastroDocumento()
    {
        $aux   = array();
        $combo = $this->_getRepository()->buscarFuncao();
        foreach ($combo as $comboFuncao) {
            $aux[$comboFuncao->getNoFuncao()] = $comboFuncao->getNoFuncao();
        }
        return $aux;
    }
}