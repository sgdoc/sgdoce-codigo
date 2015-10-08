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
 * @package    Core
 * @subpackage Util
 * @name       Locale
 * @category   Internationalization
 */
class Core_Util_Locale
{
    /**
     * @param  string|null|Zend_Locale $locale
     * @param  string $type
     * @throws InvalidArgumentException
     * @return array
     */
    public static function getMonths($locale, $type = 'wide')
    {
        if (!in_array($type, array('wide', 'abbreviated'))) {
            throw new InvalidArgumentException("Tipo inválido $type.");
        }

        $values = Zend_Locale_Data::getList($locale, 'month', array("gregorian", "format", $type));
        return $values;
    }

    /**
     * @param  string|null|Zend_Locale $locale
     * @param  string $type
     * @throws InvalidArgumentException
     * @return array
     */
    public static function getDaysWeek($locale, $type = 'wide')
    {
        if (!in_array($type, array('wide', 'abbreviated'))) {
            throw new InvalidArgumentException("Tipo inválido $type.");
        }

        $values = Zend_Locale_Data::getList($locale, 'day', array("gregorian", "format", $type));
        return $values;
    }
}
