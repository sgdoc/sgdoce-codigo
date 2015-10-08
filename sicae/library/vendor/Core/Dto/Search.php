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
class Core_Dto_Search extends Core_Dto_Base
{
    /**
     * @var array
     */
    protected $_data = array(
        'limit' => 20,
        'page'  => 1
    );

    public function __construct(array $data)
    {
        $this->_data += $data;
    }

    public function __call($method, $args)
    {
        $prefix = substr($method, 0, 3);
        $key    = lcfirst(substr($method, 3));

        if ('get' === $prefix && array_key_exists($key, $this->_data)) {
            $value = $this->_data[$key];
            if (is_array($value)) {
                // transformação recursiva dto
                $value = new Core_Dto_SearchSub($value);
            }
            return $value;
        } else if ('has' === $prefix) {
            return array_key_exists($key, $this->_data) &&
                   !($this->_data[$key] === '' || $this->_data[$key] === NULL);
        }

        return NULL;
    }

    public function getApi()
    {
        $api = array();
        foreach ($this->_data as $key => $value) {
            $api[$key] = 'get' . ucfirst($key);
        }
        return $api;
    }
}
