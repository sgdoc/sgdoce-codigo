<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */


/**
 * Classe
 *
 * @name       ChangeCase
 * @category   Sgdoce
 * @package    Sgdoce_Controller
 * @subpackage Sgdoce_Controller_Action_Helper
 *
 * @author     Rafael Yoo  <rafael.yoo.terceirizado@icmbio.gov.br>
 */
class Sgdoce_Controller_Action_Helper_ChangeCase extends \Zend_Controller_Action_Helper_Abstract
{
    /**
     * @param string $string
     *
     * @return string
     */
    public function toupper( $string )
    {
        if( is_string($string) ) {
            $string = strtr($string, "ãàáâäẽèéêëĩìíîïõòóôöũùúûüç", "ÃÀÁÂÄẼÈÉÊËĨÌÍÎÏÕÒÓÔÖŨÙÚÛÜÇ");
            return strtoupper($string);
        }

        return $string;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public function tolower( $string )
    {
        if( is_string($string) ) {
            $string = strtr($string, "ÃÀÁÂÄẼÈÉÊËĨÌÍÎÏÕÒÓÔÖŨÙÚÛÜÇ", "ãàáâäẽèéêëĩìíîïõòóôöũùúûüç");
            return strtolower($string);
        }

        return $string;
    }
}
