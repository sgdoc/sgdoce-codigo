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
 * @subpackage Filter
 * @name       MaskNumber
 * @category   Filter
 */
class Core_Filter_MaskNumber implements Zend_Filter_Interface
{
    /**
     * @var string
     */
    protected $_mask;

    /**
     * @var array
     */
    protected static $_masksDefault = array(
        'digital'  => '9999999',
        'cep'  => '99999-999',
        'cpf'  => '999.999.999-99',
        'cnpj' => '99.999.999/9999-99',
        'nup'  => '9999999.99999999/9999-99',
    );

    /**
     * @param  string|array $options
     * @return void
     */
    public function __construct($options = null)
    {
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (is_array($options) && array_key_exists('mask', $options)) {
            $options = $options['mask'];
        }

        if (null !== $options) {
            $this->setMask($options);
        }
    }

    /**
     * @param  string $mask
     * @return Core_Filter_MaskNumber
     */
    public function setMask($mask)
    {
        $this->_mask = (string) $mask;
        return $this;
    }

    /**
     * @return string
     */
    public function getMask()
    {
        return $this->_mask;
    }

    /**
     * @param string $alias
     * @param string $mask
     */
    public static function addMaskDefault($alias, $mask)
    {
        self::$_masksDefault[strtolower($alias)] = (string) $mask;
    }

    /**
     * @param  null|string       $alias
     * @return null|array|string
     */
    public static function getMasksDefault($alias = null)
    {
        $alias = strtolower($alias);

        if (null === $alias) {
            return self::$_masksDefault;
        }

        if (!isset(self::$_masksDefault[$alias])) {
            return null;
        }

        return self::$_masksDefault[$alias];
    }

    /**
     * Format values.
     *
     * value for apply format ex: 12345678900
     * format output ex: 999.999.999-99
     * return 123.456.789-00
     *
     * @param  string $number
     * @return string
     */
    public function filter($number)
    {
        $number = (string) $number;
        $mask   = self::getMasksDefault($this->getMask());

        if (null === $mask) {
            $mask = $this->getMask();
        }

        if (null === $mask) {
            return $number;
        }

        if (!$number) {
            return null;
        }
                
        if( $mask == self::$_masksDefault['digital'] ){            
            if( strlen($number) < strlen($mask) ){
                return str_pad($number, strlen($mask), '0', STR_PAD_LEFT);
            }
            return $number;
        }
        
        for ($i = 0, $length = strlen($mask); $i < $length; $i++) {
            if ($mask[$i] != '9') {
                $number = substr($number, 0, $i) . $mask[$i] . substr($number, $i);
            }
        }

        return substr($number, 0, strlen($mask));
    }
}
