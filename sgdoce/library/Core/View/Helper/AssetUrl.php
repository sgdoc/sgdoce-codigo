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
 * @subpackage View
 * @subpackage Helper
 * @name       ActionButton
 * @category   View Helper
 * @todo       review
 */
class Core_View_Helper_AssetUrl extends Zend_View_Helper_Abstract
{
    /**
     * Base URL for assets.
     *
     * @var string
     */
    protected $_assetBaseUrl;

    /**
     * Suffix for assets.
     *
     * @var string
     */
    protected $_suffix;

    protected static $_defaultSuffix;

    protected static $_addressDefault = array(
        'img' => NULL,
        'css' => NULL,
        'js'  => NULL
    );

    protected static $_mappedExtension = array(
        'png'  => 'img',
        'jpg'  => 'img',
        'gif'  => 'img',
    );

    protected static $_address = array(
        'img' => array(),
        'css' => array(),
        'js'  => array()
    );

    /**
     * Returns the URL for an asset.
     *
     * If a suffix was set, it will be appended as query parameter to the URL.
     *
     * @param  string  $file
     * @parma  boolean $omitSuffix
     * @return string
     */
    public function assetUrl($file, $omitSuffix = false, $options = array())
    {
        if (is_array($omitSuffix)) {
            $options = $omitSuffix;
            $omitSuffix = isset($options['omitSuffix'])
                        ? $options['omitSuffix']
                        : FALSE;
        }

        if (isset($options['type'])) {
            $type = $options['type'];
        } else {
            $type = $this->_resolveType($file);
        }

        $assetBaseUrl = $this->_assetBaseUrl;

        if (isset($options['address'])) {
            $assetBaseUrl = isset(static::$_address[$type][$options['address']])
                          ? static::$_address[$type][$options['address']]
                          : $options['address'];
        }

        if (NULL === $assetBaseUrl) {
            $assetBaseUrl = static::$_addressDefault[$type];
        }

        if (null === $assetBaseUrl) {
            throw new RuntimeException('No asset base URL provided');
        }

        $url = trim($assetBaseUrl, '/') . '/' . ltrim($file, '/');

        $suffix = $this->_suffix;
        if (NULL === $suffix) {
            $suffix = static::$_defaultSuffix;
        }

        if (!$omitSuffix && null !== $suffix) {
            if (strpos($url, '?') === false) {
                $url .= '?' . $suffix;
            } else {
                $url .= '&' . $suffix;
            }
        }

        return $url;
    }

    /**
     * Set the asset base URL.
     *
     * @param  string $assetBaseUrl
     * @return AssetUrl
     */
    public function setAssetBaseUrl($assetBaseUrl)
    {
        $this->_assetBaseUrl = rtrim($assetBaseUrl, '/');
        return $this;
    }

    /**
     * Set a suffix for assets.
     *
     * @param  string $suffix
     * @return AssetUrl
     */
    public function setSuffix($suffix)
    {
        $this->_suffix = urlencode($suffix);
        return $this;
    }

    public static function setDefaultSuffix($suffix)
    {
        static::$_defaultSuffix = urlencode($suffix);
    }

    public static function setDefaultAddress($type, $address)
    {
        if (!array_key_exists($type, static::$_addressDefault)) {
            throw new RuntimeException('');
        }

        static::$_addressDefault[$type] = $address;
    }

    public static function setAddress($type, $key, $address)
    {
        if (!array_key_exists($type, static::$_address)) {
            throw new RuntimeException('');
        }

        static::$_address[$type][$key] = $address;
    }

    protected function _resolveType($file)
    {
        $extension = substr($file, strrpos($file, '.') + 1);
        if (array_key_exists($extension, static::$_addressDefault)) {
            return $extension;
        }

        if (isset(static::$_mappedExtension[$extension])) {
            return static::$_mappedExtension[$extension];
        }

        return NULL;
    }

    public static function addMappedExtension($extension, $type)
    {
        if (!array_key_exists($type, static::$_addressDefault)) {
            throw new InvalidArgumentException('');
        }

        static::$_mappedExtension[$extension] = $type;
    }
}
