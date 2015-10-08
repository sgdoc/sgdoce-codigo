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
 * @name       Notification
 * @category   View Helper
 */
class Sgdoce_View_Helper_Notification extends Zend_View_Helper_Abstract
{
    /**
     *
     * @var array
     */
    private $_decode = array(
        1 => array(
            'url' => '/artefato/demanda-informacao',
            'name' => 'Demanda de Informação',
            'types' => array(
                1 => 'Você possui %1$d prazo%2$s expirado%2$s.',
                2 => 'Você possui %1$d prazo%2$s vencendo em menos de 48 horas.',
                3 => 'Você possui %1$d prazo%2$s vencendo entre 2 a 5 dias.',
                4 => 'Você possui %1$d prazo%2$s vencendo em mais de 5 dias.',
                5 => 'Sua Unidade possui %1$d prazo%2$s aguardando resposta.',
            ),
        ),
        2 => array(//demandas de suporte
            'url' => '/artefato/solicitacao',
            'name' => 'Demanda de Suporte',
            'types' => array(
                1 => 'Você possui %1$d demanda%2$s de suporte à responder',
                2 => 'O SGI possui %1$d demanda%2$s de suporte à triar',
            ),
        ),
    );

    /**
     * Gera html com a estrutura para apresentar notificações
     *
     * @param array $notifications
     * @return string xhtml
     */
    public function notification (array $notifications = array())
    {
        if (!$notifications) {
            return '';
        }

        $notificationCount = count($notifications);

        $html = '<ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle pull-right notification" aria-expanded="true">
                            <i class="icon-bell"></i>
                            <span class="label lotifications-count">' . $notificationCount . '</span>
                        </a>
                        <ul class="dropdown-menu pull-right notification-list">';

        $data = $this->_normalizeNotification($notifications);

        $i = 0;
        foreach ($data as $type => $intervalos) {
            if ($i != 0) { //divisor
                $html .= '<li class="divider"></li>';
            }
            $notificationTypeLabel = $this->_decode[$type]['name'];

            $html .= "<li>";
            $html .= "  <h6>{$notificationTypeLabel}</h6>";

            foreach ($intervalos as $intervalo => $qtde) {
                $plural = ($qtde === 1) ? '' : 's';
                $label = sprintf($this->_decode[$type]['types'][$intervalo], $qtde, $plural);
                $html .= '<div class="notification-item"><a href="' . $this->_decode[$type]['url'] . '">&raquo; ' . $label . '</a></div>';
            }
            $html .= "</li>";
            $i++;
        }

        $html .= '</ul></li></ul>';
        return $html;
    }

    private function _normalizeNotification ($arr)
    {
        $aux = array();
        foreach ($arr as $value) {
            $aux[$value['tipo']][$value['intervalo']] = $value['qtde'];
        }
        return $aux;
    }

}
