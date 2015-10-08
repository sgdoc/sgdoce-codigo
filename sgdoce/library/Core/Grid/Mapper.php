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
 * @name        Mapper
 * @category    Mapper
 */
class Core_Grid_Mapper
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
     * transformar variaveis do post em variaveis para envio para doctrine
     * @param array $params
     * @param array $order
     */
    public function translate($params)
    {
        $order = array();

        if (isset($params['iSortingCols']) && ($params['iSortingCols'] > 0)) {
            for ($i = 0; $i < $params['iSortingCols']; $i++) {
                $key = $params['iSortCol_' . $i];

                // se não tiver alias = não ordena
                if (!isset($this->_config['columns'][$key]['alias'])) {
                    continue;
                }

                $order[] = array('sort'  => $this->_config['columns'][$key]['alias'],
                                 'order' => $params['sSortDir_' . $i]);
            }
        }

        return array('order' => $order);
    }

    /**
     * mapear as variaveis enviadas pelo post para envio para doctrine
     * @param array $params
     * @return array $params
     */
    public function mapper($params)
    {
        if ($params instanceof Core_Dto_Search){
            $params = $params->getData();
        }
        $params += $this->translate($params);

        return $params;
    }
}
