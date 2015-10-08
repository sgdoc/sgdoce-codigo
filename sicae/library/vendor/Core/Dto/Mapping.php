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
class Core_Dto_Mapping extends Core_Dto_Abstract
{
    protected $_map = array();
    protected $_input = array();

    public function __construct(array $input, array $map = array())
    {
        $this->setMap($this->_map + $map);
        $this->setInput($input);
    }

    public function setMap(array $map)
    {
        $this->_map = $map;
        return $this;
    }

    public function getMap()
    {
        return $this->_map;
    }

    public function setInput(array $input)
    {
        $data = array();

        foreach ($input as $attr => $value) {
            $this->_setInput($attr, $value);
            $data[$attr] = $value;
        }

        if (count($data)) {
            $this->_input = $data;
        }

        return $this;
    }

    protected function _setInput($attr, &$value)
    {
        if (!in_array($attr, $this->_map)) {
            return NULL;
        }
    }

    public function getInput()
    {
        return $this->_input;
    }

    public function __call($method, $args = NULL)
    {
        $command = substr($method, 0, 3);

        if ('get' !== $command) {
            throw new BadMethodCallException('Metodo inexistente.');
        }

        $attr = lcfirst(substr($method, 3));

        if (!array_key_exists($attr, $this->_input)) {
            return NULL;
        }

        return $this->_input[$attr];
    }

    public function toArray()
    {
        $map = $this->getMap();
        $mapping = array();
        foreach($map as $key => $value) {
            $mapping[$value] = $this->__call('get'.$value);
        }

        return $mapping;
    }
}
