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
 * @package    Sgdoce
 * @subpackage View
 * @subpackage Helper
 * @name       BaseUrlCDN
 * @category   View Helper
 */
class Sgdoce_View_Helper_BaseUrlCDN extends Zend_View_Helper_Abstract
{

    /**
     * Returns site's base url of CDN, or file with base url prepended
     *
     * $file is appended to the base url for simplicity
     *
     * @param  string $file
     * @return string
     */
    public function baseUrlCDN ($file = '')
    {
        $configs = \Zend_Registry::get('configs');
        if (!isset($configs['app']['layout']['cdn'])) {
            throw new \Exception("informe o app.layout.cdn nas configurações da aplicação");
        }
        $cdn = $configs['app']['layout']['cdn'];
        return "{$cdn}/{$file}";
    }
}
