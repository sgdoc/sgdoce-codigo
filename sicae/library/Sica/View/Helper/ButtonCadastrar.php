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
class Sica_View_Helper_ButtonCadastrar extends Core_View_Helper_Abstract
{
    /**
     * Cria um botão que ou executa uma ação JS ou envia a uma URL
     * @param string $target url para href alvo
     * @param array $resource dados para verificação no ACL
     * @param array $tagAttrs quaisquer outros atributos a serem inseridos na tag
     * @param string $label texto a ser apresentado no botão
     */
    public function buttonCadastrar($target, $resource=array(), $tagAttrs = array(), $label = NULL)
    {
        //se tiver resource verifica a permissão
        if (!$this->_checkAcl($resource)) {
            return '';
        }

        $strLabel = $label?:'Cadastrar';

        $classesDefaults = 'btn';
        if (array_key_exists('class', $tagAttrs)) {
            foreach ((array) $tagAttrs['class'] as $value) {
                $classesDefaults .= ' ' . $value;
            }
            unset($tagAttrs['class']);
        }

        $attrs = '';
        foreach ($tagAttrs as $key => $val) {
            $attrs .= $key.'="'.$val.'" ';
        }

        return "<a class=\"$classesDefaults\" href=\"$target\" $attrs>$strLabel</a>\n";
    }
}