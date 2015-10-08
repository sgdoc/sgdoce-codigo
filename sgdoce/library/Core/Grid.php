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
class Core_Grid
{
    /**
     * configuration
     *
     * @var array
     */
    protected $_config = array();
    
    /**
     * search params
     *
     * @var array
     */
    protected $_params = array();

    public function __construct($config = array(), $params = array())
    {
        $this->setConfig($config);
        $this->setParams($params);
    }
    
    public function setConfig($config)
    {
        if ($config instanceof Zend_Config) {
            $config = $config->toArray();
        }
        
        if (!is_array($config)) {
            throw new exception('must be in an array or a Zend_Config object');
        }
        
        $this->_config = $config;
    }
    
    public function setParams($params)
    {
        if (!is_array($params)) {
            throw new exception('must be in an array');
        }
        
        $this->_params = $params;
    }
    
    public function getParams()
    {
        return $this->_params;
    }

    public function mapper($params)
    {
        $this->setParams($params);
        $mapper = new Core_Grid_Mapper($this->_config);
        return $mapper->mapper($this->_params);
    }

    public function parse($params, $data)
    {
        $this->setParams($params);
        return $this->parseData($data);
    }

    public function parseData($data)
    {
        if (!count($this->_params)) {
            throw new InvalidArgumentException('No params set!');
        }
        $data['sEcho'] = $this->_params['sEcho'];
        $parse = new Core_Grid_Parse($this->_config);
        return $parse->parse($data);
    }

}
