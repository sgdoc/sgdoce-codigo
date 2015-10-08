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

class Core_ServiceLayer_Service_Container_Temp extends Core_ServiceLayer_Service_Abstract
{
    /**
     *Nome do container de sessão
     * @var string
     */
    protected $_container;

    /**
     *
     * @var int
     */
    protected $_id;

    /**
     * Adiciona ao Container atual os dados enviados
     * @param array $data
     * @throws \Core_Exception_ServiceLayer
     * @return Core_Controller_Temp
     */
    public function add(Core_Dto_Abstract $data, $index = NULL, $id = NULL)
    {
        if (NULL === $id) {
            $id = $this->getId();
        }

        try {
            $temp   = $this->get();

            if ($index !== NULL) {
                $temp[$index] = $data;
            } else {
                $temp[] = $data;
            }

            $this->getContainer()->{$id} = $temp;
        } catch (\Core_Exception_ServiceLayer $exc) {
            throw new \Core_Exception_ServiceLayer($exc, $exc->getCode(), $exc);
        }
        return $this;
    }

    /**
     *
     * @return boolean
     */
    public function delete($index = NULL, $id = NULL)
    {
        if (NULL === $id) {
            $id = $this->getId();
        }

        if ($index !== NULL) {
            $temp   = $this->get();
            unset($temp[$index]);
            $this->getContainer()->{$id} = $temp;
            return TRUE;
        }

        // throw exception
    }

    /**
     *
     * @return boolean
     */
    public function clear($id = NULL)
    {
        if (NULL === $id) {
            $id = $this->getId();
        }

        $this->getContainer()->{$id} = array();
        return TRUE;
    }

    public function getAll()
    {
        // @todo
    }

    public function clearAll()
    {
        // @todo
    }

    /**
     *
     */
    public function get($index = NULL, $id = NULL)
    {
        if (NULL === $id) {
            $id = $this->getId();
        }

        if ($index === NULL) {
            return $this->getContainer()->{$id};
        } else {
            return $this->getContainer()->{$id}[$index];
        }
    }

    /**
     * Retorna o container da sessão
     * @throws UnexpectedValueException
     * @return string
     */
    public function getContainer()
    {
        if (NULL === $this->_container) {
            throw new UnexpectedValueException('Container não pode ser nulo');
        }
        return $this->_container;
    }

    /**
     * Inicializa o container
     * @param  string $container
     * @return Core_Controller_Temp
     */
    public function setContainer($container)
    {
        $this->_container = $container;
        return $this;
    }

    /**
     * Retorna o identificador do Container atual
     * @param string $alias
     * @return string alias
     */
    public function getId()
    {
        if (NULL === $this->_id) {
            $this->setId(get_class($this));
        }
        return $this->_id;
    }

    /**
     *
     * @param string $id
     * @return Core_Controller_Temp
     */
    public function setId($id)
    {
        $this->_id = (string) $id;
        return $this;
    }
}