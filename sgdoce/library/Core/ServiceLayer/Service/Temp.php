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
 * Concrete Service
 *
 * Camada de serviço para manipular dados temporários
 * @deprecated
 * @category   Service Layer
 * @package    Core
 * @subpackage ServiceLayer
 * @subpackage Service
 * @name       Temp
 */
class Core_ServiceLayer_Service_Temp extends Core_ServiceLayer_Service_Crud
{
    const CONTAINER_TEMP = 'temps';

    const INDEX_ADD_TRASH = 'add';
    const INDEX_DEL_TRASH = 'del';

    /**
     * @param array       $data
     * @param null|string $alias
     */
    public function addTemps(array $data, $alias = NULL, $notValidate = FALSE)
    {
        try {
            $sessionTemp = $this->_getContainerTemp();
            $alias       = $this->_getIdTemp($alias);

            if (FALSE === $notValidate) {
                foreach ($data as $index => $value) {
                    $this->_validateDataTemp($this->getTemp(NULL, $alias), $value, $index, $alias);
                }
            }

            $sessionTemp->$alias = $data;
        } catch (\Core_Exception_ServiceLayer $exc) {
            throw new \Core_Exception_ServiceLayer($exc, $exc->getCode(), $exc);
        }

        return $this;
    }

    /**
     * @param array       $data
     * @param null|string $alias
     * @param string      $index
     */
    public function addTemp($data, $alias = NULL, $index = NULL, $notValidate = FALSE)
    {
        try {
            $sessionTemp = $this->_getContainerTemp();
            $alias       = $this->_getIdTemp($alias);
            $temp        = $this->getTemp(NULL, $alias);

            if (FALSE === $notValidate) {
                $dataValidate = $data;
                if (static::INDEX_ADD_TRASH === $index) {
                    $dataValidate = end($dataValidate);
                }
                $this->_validateDataTemp($temp, $dataValidate, $index, $alias);
            }

            if (NULL === $index) {
                $temp[] = $data;
            } else {
                $temp[$index] = $data;
            }

            $sessionTemp->{$alias} = $temp;
        } catch (\Core_Exception_ServiceLayer $exc) {
            throw new \Core_Exception_ServiceLayer($exc, $exc->getCode(), $exc);
        }
        return $this;
    }

    public function clearTemp($alias = NULL)
    {
        $container = $this->_getContainerTemp();
        $alias     = $this->_getIdTemp($alias);
        $container->$alias = array();
        return TRUE;
    }

    public function deleteTemp($index, $alias = NULL , $reset = FALSE)
    {
        $dataTemp = $this->getTemp(NULL, $alias);

        if (!isset($dataTemp[$index])) {
            return FALSE;
        }

        unset($dataTemp[$index]);
        $dataTemp = (array) $dataTemp;
        if (TRUE === $reset) {
            $dataTemp = array_values($dataTemp);
        }
        $this->addTemps($dataTemp, $alias, TRUE);
        return TRUE;
    }

    /**
     * @param array $temp
     * @param mixed $data
     */
    protected function _validateDataTemp($temp, $data, $index, $alias)
    {}

    /**
     * @param  null|string $alias
     * @return array
     */
    public function getTemp($index = NULL, $alias = NULL)
    {
        $container = $this->_getContainerTemp();
        $alias     = $this->_getIdTemp($alias);
        $temp      = array();

        $tempArray = $container->$alias;

        if (isset($container->$alias)) {
            $temp = $tempArray;
            if (isset($tempArray[$index])) {
                $temp = $tempArray[$index];
            }
        }

        return $temp;
    }

    public function hasTempIndex($index, $alias = NULL)
    {
        $container = $this->_getContainerTemp();
        $alias     = $this->_getIdTemp($alias);
        $tempArray = (array) $container->$alias;

        return array_key_exists($index, $tempArray);
    }

    /**
     * @return object
     */
    protected function _getContainerTemp()
    {
        return new Zend_Session_Namespace(static::CONTAINER_TEMP);
    }

    protected function _getIdTemp($alias = NULL)
    {
        if (NULL === $alias) {
            $alias = get_class($this);
        }

        return (string) $alias;
    }

}
