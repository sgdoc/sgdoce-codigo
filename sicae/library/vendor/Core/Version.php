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
 * @category    Version
 * @package     Core
 * @name        Version
 */
class Core_Version
{
    /**
     * @var string
     */
    const VERSION = '2.0.0rc2';

     /**
     * @var array
     */
    private static $_reverseMap = array(
        'major'  => 1,
        'minor'  => 2,
        'mini'   => 3,
        'suffix' => 4
    );

    /**
     * @link   http://site.svn.dasprids.de/trunk/application/library/App/Version.php
     * @param  string  $part
     * @param  boolean $fromBeginning
     * @return string
     */
    final public static function getVersion($part = null, $fromBeginning = false)
    {
        if ($part === null) {
            return static::VERSION;
        } elseif (!preg_match('(^(?P<major>\d+)\.(?P<minor>\d+)\.(?P<mini>\d+)(?P<suffix>.*)?$)', static::VERSION, $matches)) {
            throw new RuntimeException('Incapaz de analisar a versão da library');
        } elseif (!isset($matches[$part])) {
            throw new InvalidArgumentException('Nome da parte "' . $part . '" não existe uma versão!');
        } elseif ($fromBeginning === false) {
            return $matches[$part];
        } else {
            $version = '';

            for ($i = 1; $i <= self::$_reverseMap[$part]; $i++) {
                $version .= (($i > 1 && $i < 4) ? '.' : '') . $matches[$i];
            }

            return $version;
        }
    }
}