<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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

require 'SpellChecker/Request.php';

class Core_SpellChecker_Request extends \SpellChecker\Request
{

    public function __construct()
    {
        if (!self::post('isPost')) {
            return;
        }

        $this->driver = self::post('driver');
        $this->action = self::post('action');
        $this->lang = self::post('lang');

        self::execute_action();
    }

    public function execute_action()
    {
        $driver = new \Core_SpellChecker_Pspell(array('lang' => $this->lang));
        $driver->{$this->action}();
    }

    public static function post($key = NULL)
    {
        $request = \Zend_Controller_Front::getInstance()->getRequest();

        if ($key == 'isPost') {
            return $request->isPost();
        }

        $data = $request->getPost();
        
        return !isset($data[$key])? : $data[$key];
    }

}

