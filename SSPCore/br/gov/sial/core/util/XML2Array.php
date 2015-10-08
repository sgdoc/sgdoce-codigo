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
namespace br\gov\sial\core\util;
use br\gov\sial\core\exception\SIALException;

/**
 * SIAL
 *
 * Config
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @author J. Augusto <augustowebd@gmail.com>
 * @author http://php.net/manual/en/function.xml-parse.php
 * */
class XML2Array
{
    var $arrOutput = array();
    var $resParser;
    var $strXmlData;

    /**
     * converte o XML informado em um array
     *
     * @param string $strXML
     * @return array
     * @throws Exception
     * */
    public function parse ($strXML)
    {
        $this->resParser = xml_parser_create ();

        xml_set_object($this->resParser, $this);

        xml_set_element_handler($this->resParser, "tagOpen", "tagClosed");

        xml_set_character_data_handler($this->resParser, "tagData");

        $this->strXmlData = xml_parse($this->resParser, $strXML);

        if (!$this->strXmlData) {

           $message = sprintf("XML error: %s at line %d"
                , xml_error_string(xml_get_error_code($this->resParser))
                , xml_get_current_line_number($this->resParser)
            );

           throw new SIALException($message);
        }

        xml_parser_free($this->resParser);

        return $this->arrOutput;
    }

    public function tagOpen ($parser, $name, $attrs)
    {
       $tag = array(
            "name"  => $name,
            "attrs" => $attrs
        );

       array_push($this->arrOutput, $tag);
    }

    public function tagData ($parser, $tagData)
    {
       if (trim($tagData)) {

            if (isset($this->arrOutput[count($this->arrOutput)-1]['tagData'])) {
                $this->arrOutput[count($this->arrOutput)-1]['tagData'] .= $tagData;
            } else {
                $this->arrOutput[count($this->arrOutput)-1]['tagData'] = $tagData;
            }
       }
    }

    public function tagClosed ($parser, $name)
    {
       $this->arrOutput[count($this->arrOutput)-2]['children'][] = $this->arrOutput[count($this->arrOutput)-1];
       array_pop($this->arrOutput);
    }

    /**
     * @return XML2Array
     * */
    public static function factory ()
    {
        return new self;
    }
}