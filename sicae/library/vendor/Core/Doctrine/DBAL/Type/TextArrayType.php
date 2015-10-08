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

use Doctrine\DBAL\Types\Type,
    Doctrine\DBAL\Platforms\AbstractPlatform;

/**
 * Type that maps a database BIGINT to a PHP string.
 *
 * @since 2.0
 */
class Core_Doctrine_DBAL_Type_TextArrayType extends Type
{

     const TEXTARRAY = 'textarray'; // modify to match your type name

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getDoctrineTypeMapping('array');
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return explode(',', trim($value, '{""}'));
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        settype($value, 'array'); // can be called with a scalar or array
        $result = array();
        foreach ($value as $t) {
            if (is_array($t)) {
                $result[] = $this->convertToDatabaseValue($t);
            } else {
                $t = str_replace('"', '\\"', $t); // escape double quote
                if (! is_numeric($t)) { // quote only non-numeric values
                    $t = '"' . $t . '"';
                }
                $result[] = $t;
            }
        }
        return '{' . implode(",", $result) . '}'; // format
    }

    public function getName()
    {
        return self::TEXTARRAY; // modify to match your constant name
    }
}
