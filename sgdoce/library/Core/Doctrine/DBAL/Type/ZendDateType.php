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
    Doctrine\DBAL\Types\ConversionException,
    Doctrine\DBAL\Platforms\AbstractPlatform;
/**
 * Type that maps an SQL DATETIME/TIMESTAMP to a Zend_Date object.
 *
 * @category    DoctrineExtensions
 * @package     DoctrineExtensions\Types
 * @author      Andreas Gallien <gallien@seleos.de>
 * @license     New BSD License
 */
/**
 * Mapeia uma coluna Datetime para um objeto Zend_Date
 *
 * @package     Core
 * @subpackage  Doctrine
 * @subpackage  DBAL
 * @subpackage  Type
 * @name        ZendDateType
 * @category    Doctrine Extensions / DataType
 */
class Core_Doctrine_DBAL_Type_ZendDateType extends Type
{
    const ZENDDATE = 'zenddate';

    public function getName()
    {
        return self::ZENDDATE;
    }

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return $platform->getDateTimeTypeDeclarationSQL($fieldDeclaration);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!$value instanceof Zend_Date && null !== $value) {
            $value = new Zend_Date($value);
        }

        return ($value !== null)
            ? $value->toString(\Zend_Locale_Format::convertPhpToIsoFormat(
                  $platform->getDateTimeFormatString()
              ))
            : null;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null) {
            return null;
        }

        $dateTimeFormatString = \Zend_Locale_Format::convertPhpToIsoFormat(
            $platform->getDateTimeFormatString()
        );

        $val = new \Zend_Date($value, $dateTimeFormatString);
        if (!$val) {
            throw ConversionException::conversionFailed($value, $this->getName());
        }
        return $val;
    }
}
