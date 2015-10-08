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
 * @name Cnpj
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Cnpj extends Digits
{
    /**
     * {@inheritdoc}
     * @param string
     * @return boolean
     * */
    public function isValid ($suspicious)
    {
        # certifica-se do param ser composto apenas de digitos
        if (FALSE == parent::isValid($suspicious)) {
            return FALSE;
        }

        # certifica-se de que param nao seja formado apenas de numero repetidos
        if ($suspicious == str_repeat($suspicious[0], 14)) {
            return FALSE;
        }

        # a primeira chamada ao checkDigit verifica o primeiro digito verificador
        # que se por sua vez for avaliado como TRUE invocará novamente o mesmo metodo
        # para verificar o segundo digito
        return self::_checkDigit($suspicious, 12, 5) ?
               self::_checkDigit($suspicious, 13, 6) ? TRUE : FALSE : FALSE;
    }

    /**
     * Verifica os digitos do cnpj
     * 
     * @param string $cnpjNumber
     * @param integer $limitPos
     * @param integer $factor
     * @return boolean
     * */
    private static function _checkDigit ($cnpjNumber, $limitPos, $factor)
    {
        $digit    = 0;
        for ($idx = 0; $idx < $limitPos; $idx++) {
            $digit += $cnpjNumber[$idx] * $factor;
            $factor = ($factor == 2) ? 9 : $factor - 1;
        }

        $digit = $digit % 11;
        $digit = $digit < 2 ? 0 : 11 - $digit;
        return $digit == $cnpjNumber[$limitPos];
    }
}