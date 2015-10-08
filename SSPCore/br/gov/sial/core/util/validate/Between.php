<?php
/*
 * Copyright 2011 ICMBio
 * Este arquivo é parte do programa SIAL
 * O SIAL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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
namespace br\gov\sial\core\util\validate;

/**
 * SIAL
 *
 * @package br.gov.sial.core.util
 * @subpackage validate
 * @name Between
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Between extends Validate
{
   /**
     * {@inheritdoc}
     * */
    public function isValid($suspicious)
    {
        $tmpResult = FALSE;
        list($element, $params) = $suspicious;

        # valida a existencia dos parametros
        self::_initParam($params);

        if (TRUE == $params['inclusive']) {
            $tmpResult = $element >= $params['min'] &&
                         $element <= $params['max'];
        } else {
            $tmpResult = $element > $params['min'] &&
                         $element < $params['max'];
        }

        return $tmpResult;
    }


    /**
     * inicializa os param obrigatorios
     *
     * @param mixed[]
     * */
    private static function _initParam (&$params)
    {
        $paramList = array('min' => 0, 'max' => 0, 'inclusive' => FALSE);
        foreach ($paramList as $key => $val) {
            if (!isset($params[$key])) {
                $params[$key] = $val;
            }
        }
    }
}