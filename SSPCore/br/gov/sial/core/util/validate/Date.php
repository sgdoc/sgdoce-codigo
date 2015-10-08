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
namespace br\gov\sial\core\util\validate;

/**
 * SIAL
 *
 * @package br.gov.sial.core.util
 * @subpackage validate
 * @name Date
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Date extends Validate
{
    const DATE_FORMAT_YYYY_MM_DD = 'YYYY-MM-DD';
    const DATE_FORMAT_YYYY_DD_MM = 'YYYY-DD-MM';
    const DATE_FORMAT_DD_MM_YYYY = 'DD-MM-YYYY';
    const DATE_FORMAT_MM_DD_YYYY = 'MM-DD-YYYY';
    const DATE_FORMAT_YYYYMMDD   = 'YYYYMMDD';
    const DATE_FORMAT_YYYYDDMM   = 'YYYYDDMM';

    /**
     * {@inheritdoc} -  Este metodo recebe um array de argumento(date, format)
     *
     * @param string[] $suspicious)
     * */
    public function isValid($suspicious)
    {
        list($date, $format) = $suspicious;
        list($month, $day, $year) = self::_parseDate($date, $format);
        return checkdate($month, $day, $year);
    }

    /**
     * Separa a data informada
     * 
     * @param string $date
     * @param string $format
     * @return int[]
     * */
    private static function _parseDate ($date, $format)
    {
        $day   =
        $year  =
        $month = 0;

        switch (strtoupper($format)) {
            case 'YYYY-MM-DD':
                list($year, $month, $day) = preg_split('/[-\.\/ ]/', $date);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'YYYY-DD-MM':
                list($year, $day, $month) = preg_split('/[-\.\/ ]/', $date);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'DD-MM-YYYY':
                list($day, $month, $year) = preg_split('/[-\.\/ ]/', $date);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'MM-DD-YYYY':
                list($month, $day, $year) = preg_split('/[-\.\/ ]/', $date);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'YYYYMMDD':
                $year  = substr($date, 0, 4);
                $day   = substr($date, 6, 2);
                $month = substr($date, 4, 2);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 'YYYYDDMM':
                $year  = substr($date, 0, 4);
                $day   = substr($date, 4, 2);
                $month = substr($date, 6, 2);
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd
        }
        return array($month, $day, $year);
    }
}