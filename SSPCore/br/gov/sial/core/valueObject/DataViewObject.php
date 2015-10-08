<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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
namespace br\gov\sial\core\valueObject;
use br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core
 * @subpackage valueObject
 * @author J. Augusto <augustowebd@gmail.com>
 * @author Sósthenes Neto <sosthenes.neto.terceirizado@icmbio.gov.br>
 * */
final class DataViewObject extends SIALAbstract
{
    /**
     * dados internos com as chaves normalizadas alá "ucfirst"
     *
     * @var mixed[]
     * */
    private $_data;

    /**
     * @deprecated
     * @param string $filename
     * @return boolean
     * */
    public static function hasCache ($filename)
    {
        return true;
    }

    /**
     * @return boolean
     * */
    public function isEmpty ()
    {
        return empty($this->_data);
    }

    /**
     * converte os dados em array. Os valores utilizados para
     * definicao da chave sera os definidos na anotacao ('name')
     *
     * @return mixed[]
     * */
    public function toArray ()
    {
        return (array) $this->_normalizeAttrName(
               (object) $this->_data
               , 'lcfirst'
        );
    }

    /**
     * converte os dados em string JSon
     *
     * @return string
     * */
    public function toJson ()
    {
        return json_encode($this->toArray());
    }

    /**
     * @return string
     * */
    public function toXml ()
    {
        $data   = $this->toArray();
        $output = NULL;

        foreach ((array) $data as $node => $value) {
            $output .= sprintf('<%1$s>%2$s</%1$s>', $node, is_array($value) ? $this->toXml($value, TRUE) : $value);
        }

        return $output;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return mixed
     * */
    public function __call ($name, $arguments)
    {
        $attr = substr($name, 3);

        if (isset($this->_data[$attr])) {
            return $this->_data[$attr];
        }

        $attr = strtolower($attr);

        foreach ($this->_data as $key => $val) {
            if(strtolower($key) == $attr) {
                return $val;
            }
        }

        return NULL;
    }

    /**
     * @param stdClass $data
     * @return DataViewObject
     * */
    public static function factory (\stdClass $data)
    {
        $self = new self;

        $self->_data = self::_normalizeAttrName(clone $data, 'ucfirst');

        return $self;
    }

    /**
     * @param stdClass $data
     * @param string $callback
     * @return stdClass
     * */
    private function _normalizeAttrName (\stdClass $data, $callback)
    {
        $nData = array();

        foreach ($data as $key => $val) {
            $nKey = implode('', array_map($callback, preg_split('/_/', $key)));
            $nData[$nKey] = $val;
        }

        return $nData;
    }
}