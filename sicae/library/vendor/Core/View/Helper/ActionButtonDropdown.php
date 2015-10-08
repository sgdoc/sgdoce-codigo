<?php
/*
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
 * @name       ActionButtonDropdown
 * @category   View Helper
 */
class Core_View_Helper_ActionButtonDropdown extends Zend_View_Helper_Abstract
{
    /**
     * Cria um botão dropdown baseado em um array de string
     * @param array $arrItem array de string com a lista de links que ficará dentro do dropdown
     * @param string $strTitle título do botão dropdown
     * @param string $strPosition posição do dropodown. Ex.: left, right
     * @param string $strIcon nome do ícone a ser apresentado
     *
     * @return string concatenada do conteúdo final do botão
     */
    public function actionButtonDropdown(array $arrItem, $strTitle = '', $strPosition = 'left', $strIcon = 'cog')
    {
        $strRetorno = '';

        if(count($arrItem)) {
            $strRetorno .= '<div class="btn-group pull-' . $strPosition . '">
                <button class="btn btn-mini dropdown-toggle" data-toggle="dropdown" title="' . $strTitle . '"><i class="icon-' . $strIcon . '"></i></button>
                    <ul class="dropdown-menu">';

            foreach($arrItem as $key => $val) {
                $strRetorno .= '<li>' . preg_replace('/(btn\-[a-z0-9]*)|(btn)/', '', $val) . '</li>';
            }

            $strRetorno .= '</ul>
                </div>';
        }

        return $strRetorno;
    }
}