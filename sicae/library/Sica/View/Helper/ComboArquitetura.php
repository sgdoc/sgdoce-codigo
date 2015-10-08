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
/**
 * @package    Sica
 * @subpackage View
 * @subpackage Helper
 * @name       Sistemas
 * @category   View Helper
 */
class Sica_View_Helper_ComboArquitetura extends Core_View_Helper_HtmlForm_ComboCustom
{
    public function comboArquitetura($name, $value = NULL, array $attribs = array(), array $options)
    {
        $arquiteturas = array();

        foreach ($options as $key => $arquitetura){
            $arquiteturas[$arquitetura['sqArquitetura']] = $this->_getLabel($arquitetura);
        }

        return $this->comboCustom($name, $value, $attribs, $arquiteturas);
    }

    protected function _getLabel($value)
    {
        return $value['noArquitetura'];
    }
}
