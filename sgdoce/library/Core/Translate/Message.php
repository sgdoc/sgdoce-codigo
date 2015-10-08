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
 * @uses        Zend_Translate_Adapter
 * @package     Core
 * @subpackage  Translate
 * @name        Message
 * @category    Translate
 */
class Core_Translate_Message extends Zend_Translate_Adapter
{
    /**
     * @var array
     */
    protected $_types;

    /**
     * @var array
     */
    protected $_mapTypes = array(
        1 => 'erro',
        2 => 'alerta',
        3 => 'sucesso',
        4 => 'informação',
        5 => 'confirmação',
        6 => 'Confirmação',
        7 => 'historico'
    );

    /**
     * @param  array $options
     * @return void
     */
    public function __construct($options = array())
    {
        $options['scan'] = 'filename';
        parent::__construct($options);

        if (isset(self::$_cache)) {
            $id = 'Zend_Translate_ListaMensagensTipo_' . $this->toString();
            $temp = self::$_cache->load($id);
            if ($temp) {
                $this->_types = $temp;
            }
        }
    }

    protected function _loadTranslationData($filename, $locale, array $options = array())
    {
        $this->_data = array();

        if ('csv' !== pathinfo($filename, PATHINFO_EXTENSION)) {
            throw new UnexpectedValueException("Extensão deve ser 'csv'");
        }

        $options     = $options + $this->_options;
        $this->_file = fopen($filename, 'rb');
        if (!$this->_file) {
            require_once 'Zend/Translate/Exception.php';
            throw new Zend_Translate_Exception('Erro ao abrir arquivo \'' . $filename . '\'.');
        }

        while(($data = fgetcsv($this->_file, 0, ";", '"')) !== false) {
            if (substr($data[0], 0, 1) === '#') {
                continue;
            }

            if (!isset($data[1])) {
                throw new UnexpectedValueException("É necessário setar o mensagem para o código '$data[0]'");
            }

            if (!isset($data[2])) {
                throw new UnexpectedValueException('É necessário setar o tipo da mensagem');
            }

            if (!$type = $this->_resolveType($data[2])) {
                throw new UnexpectedValueException("Tipo setado a mensagem '$data[2]' inválido.");
            }

            $this->_data[$locale][$data[0]] = $data[1];
            $this->_types[$data[0]]         = $type;
        }

        if (count($this->_types) && isset(self::$_cache)) {
            $id = 'Zend_Translate_ListaMensagensTipo_' . $this->toString();
            self::$_cache->save($this->_types, $id);
        }

        return $this->_data;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Message';
    }

    /**
     * @param  string $code
     * @return int
     */
    public function getType($code)
    {
        $messageId = $this->getMessageId($code);

        if ($messageId) {
            $code = $messageId;
        }

        if (!isset($this->_types[$code])) {
            return null;
        }

        return $this->_types[$code];
    }

    /**
     * @param  string $code
     * @return int
     */
    protected function _resolveType($type)
    {
        //if (array_key_exists($type, $this->_mapTypes)) {
        if (array_keys($this->_mapTypes, $type)) {
            return $type;
        }

        return array_search(strtolower($type), $this->_mapTypes);
    }

    /**
     * Adicionado suporte a token
     *
     * @inheridoc
     */
    public function _($messageId, $locale = null, array $tokens = array())
    {
        return $this->translate($messageId, $locale, $tokens);
    }

    /**
     * Adicionado suporte a token
     *
     * @inheridoc
     */
    public function translate($messageId, $locale = null, array $tokens = array())
    {
        $message = parent::translate($messageId, $locale);
        if (null === $locale) {
            $locale = $this->_options['locale'];
        }

        if (!Zend_Locale::isLocale($locale, true, false)) {
            $locale = new Zend_Locale($locale);
        }

        $locale = (string) $locale;
        $exists  = count($tokens);

        if ($this->_options['log']) {
            if ($exists && !preg_match('@/%[a-zA-Z_]%/@', $message)) {
                $this->_log($message, $locale);
            }
        }

        if ($exists) {
            $tempMessage = '';

            foreach ($tokens as $token => $value) {
                if (isset($this->_translate[$locale][$value])) {
                    $value = $this->_translate[$locale][$value];
                }
                $tempMessage = str_replace("%$token%", $value, $message);
            }
                var_dump($tempMessage);die;

            $message = $tempMessage;
        }

        return $message;
    }

    public function getTypeName($code)
    {
        $messageId = $this->getMessageId($code);

        if ($messageId) {
            $code = $messageId;
        }

        if (!isset($this->_types[$code])) {
            return null;
        }

        return $this->_mapTypes[$this->_types[$code]];
    }

    public function getTypeNameTranslate($code, $locale = null)
    {
        $type = $this->getTypeName($code);

        if (null === $locale) {
            $locale = $this->getLocale();
        }

        if (isset($this->_translate[$locale][$type])) {
            $type = $this->_translate[$locale][$type];
        }

        return $type;
    }
}
