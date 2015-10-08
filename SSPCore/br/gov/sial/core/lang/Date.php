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
namespace br\gov\sial\core\lang;
use br\gov\sial\core\lang\Enum,
    br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * Manipulador de data.
 *
 * @package br.gov.sial.core
 * @subpackage lang
 * @name Date
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Date extends SIALAbstract
{
    /**
     * Número de elementos da data.
     *
     * @var integer
     * */
    const NUMBER_ELEMENTS = 3;

    /**
     * Separador para data formato pt_br.
     *
     * @var string
     * */
    const SEPARATOR_PT_BR = '/';

    /**
     * Separador para data formato en.
     *
     * @var string
     * */
    const SEPARATOR_EN = '-';

    /**
     * Formato pt_br.
     *
     * @var string
     * */
    const FORMAT_PT_BR = 'd/m/Y';

    /**
     * Formato en.
     *
     * @var string
     * */
    const FORMAT_EN = 'Y-m-d';

    /**
     * Formatação padrão para saída.
     *
     * @var string
     * */
    private static $_defaultOutputType = 'Y-m-d';

    /**
     * Referência do objeto timestamp.
     *
     * @var integer
     * */
    private $_timestamp;

    public static function getDefaultFotmat ()
    {
        return self::$_defaultOutputType;
    }

    /**
     * define o formato de saida padrao
     *
     * @param string $format
     * */
    public static function defaultOutputFormat ($format)
    {
        self::$_defaultOutputType = $format;
    }

    /**
     * Adiciona um número de dias a data.
     *
     * @param integer
     * @return br\gov\sial\core\lang\Date
     * */
    public function addDay ($number)
    {
        $this->_timestamp += ((integer) $number * 86400);
        return $this;
    }

    /**
     * Remove o número de dias informada a data.
     *
     * @param integer
     * @return br\gov\sial\core\lang\Date
     * */
    public function subDay ($number)
    {
        $this->_timestamp -= ((integer) $number * 86400);
        return $this;
    }

    /**
     * Renderiza o objeto data.
     *
     * @return string
     * */
    public function output ()
    {
        return date(self::$_defaultOutputType, $this->_timestamp);
    }

    /**
     * Cria e retorna um objeto data.
     *
     * @param string $date
     * @param DATE_PARAM $formatIn
     * @param DATE_PARAM $formatOut
     * @return br\gov\sial\core\lang\Date
     * */
    public static function factory ($date = NULL, $formatIn = NULL, $formatOut = NULL)
    {
        $date = $date ?: date('Y-m-d H:i:s');
        $time = explode(' ', $date, 2);
        $date = current($time);
        $time = next($time);

        if (NULL == $formatIn) {
            if ('/' == $date[2]) {
                $formatIn = self::FORMAT_PT_BR;
            }

            if ('-' == $date[4]) {
                $formatIn = self::FORMAT_EN;
            }
        }

        $formatIn  = parent::toggle($formatIn, self::$_defaultOutputType);

        self::$_defaultOutputType = parent::toggle($formatOut, self::$_defaultOutputType);

        return (NULL == $date) ? self::_factoryCreateCurrentDate()
                               : self::_factoryCreateDate($date, $formatIn, $time);
    }

    /**
     * Data atual.
     *
     * @return Date
     * */
    private static function _factoryCreateCurrentDate ()
    {
        return self::_factoryCreateDate(date('Y-m-d'), '-');
    }

    /**
     * Cria objeto com a data informada.
     * O formato de entrada definirá como a string de data será tratada.
     * Se for dectectado '/' o formato assumido será pt_BR. Caso for detectado '-' o formato será 'en'.
     *
     * @param string $date
     * @param string $inputFormat
     * @param string $time
     * @return Date
     * @throws br\gov\sial\core\exception\IllegalArgumentException
     * */
    private static function _factoryCreateDate ($date, $inputFormat, $time = NULL)
    {

        $time = $time ?: '0:0:0';

        list($hour, $minute, $second) = explode(':', $time, 3);

        $result = strstr($inputFormat, self::SEPARATOR_PT_BR);

        $date = (is_string($result)) ? explode(self::SEPARATOR_PT_BR, $date) :
                                       explode(self::SEPARATOR_EN, $date);

        self::_validDateElements($date);

        if ($result) {
            list ($day, $month, $year) = $date;
        } else {
            list ($year, $month, $day) = $date;
        }

        $date = new self();

        $date->_timestamp = mktime(
            (integer) $hour,
            (integer) $minute,
            (integer) $second,
            (integer) $month,
            (integer) $day,
            (integer) $year);

        return $date;
    }

    /**
     * Verifica se os elementos do array são válidos para o objeto Date.
     *
     * @param string[]
     * @throws IllegalArgumentException
     * */
    private static function _validDateElements (array $dateElements)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull(self::NUMBER_ELEMENTS == sizeof($dateElements), "Data ou formato inválido.");
    }
}