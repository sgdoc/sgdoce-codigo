<?php

abstract class Core_Integration_Abstract_Soap extends SoapClient
{
    /**
     * @var string
     */
    protected $_url;


    public function __construct($options = array(
            'uri' => 'urn: xmethods-delayed-quoteds',
            'style' => SOAP_RPC,
            'use' => SOAP_ENCODED,
            'encoding' => 'UTF-8'))
    {

        $this->_init();
        if (empty($this->_url)) {
            throw new Exception('Não há url indicada para o serviço.');
        } else {
            $options['location'] = $this->_url;
        }
        parent::__construct(NULL, $options);
    }

    protected function _init()
    {
    }

    /**
     * Convert xml to array
     *
     * @param string $xml String XML
     * @return array
     */
    public static function xmlToArray($xml)
    {
        $children = array();
        $return   = FALSE;

        if (is_string($xml)) {
            $xml = preg_replace('/&(?!#?[a-z0-9]+;)/', '&amp;', $xml);
            $xml = simplexml_load_string($xml);
        }

        if ($xml instanceof SimpleXMLElement) {
            $children = $xml->children();
            $return = NULL;
        }

        $first = TRUE;

        if (!empty($children)) {
            foreach ($children as $element => $value) {
                if ($value instanceof SimpleXMLElement) {
                    $values = (array) $value->children();

                    if (count($values) > 0) {
                        if (isset($return[$element])) {
                            if ($first) {
                                $oldValue          = $return[$element];
                                $return[$element]   = array();
                                $return[$element][] = $oldValue;
                            }
                            $return[$element][] = self::xmlToArray($value);
                            $first = FALSE;
                        } else {
                            $return[$element] = self::xmlToArray($value);
                        }
                    } else {
                        if (!isset($return[$element])) {
                            $return[$element] = (string) $value;
                        } else {
                            if (!is_array($return[$element])) {
                                $return[$element] = array($return[$element], (string) $value);
                            } else {
                                $return[$element][] = (string) $value;
                            }
                        }
                    }
                }
            }
        }

        return $return;
    }
}
