<?php
/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
/**
 * @package     Core
 * @subpackage  Grid
 * @name        Parse
 * @category    Parser
 */
class Core_Grid_Parse
{

    public function __construct($config = array())
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }

        if (!is_array($config)) {
            throw new exception('must be in an array or a Zend_Config object');
        }

        $this->_config = $config;
    }

    /**
     *
     * pega os dados enviados e processa para view
     * @param array $data
     * @return array $result
     */
    public function getData($data)
    {
        ksort($this->_config['columns']);
        $columns = $this->_config['columns'];
        $result  = array();

        foreach($data as $key => $row) {
            $resultRow = array();
            for ($order = 0; $order < count($columns); $order++) {
                $column = $columns[$order]['column'];

                $value = is_callable($column)
                       ? call_user_func($column, $row, $key)
                       : $row[$column];

                $resultRow[] = $value;
            }

            $result[] = array_values($resultRow);
        }

        return $result;
    }

    /**
     *
     * formata todos os parametros necessários para retorno json dos dados
     * @param array $data
     * @return array $response
     */
    public function parse($result)
    {
        $response = array();

        $response['aaData'] = $this->getData($result['data']);
        $response['sEcho']  = isset($result['sEcho'])
                            ? $result['sEcho']
                            : '';
        $response['iTotalDisplayRecords'] = count($response['aaData']);
        $response['iTotalRecords']        = $result['total'];

        return $response;
    }

}
