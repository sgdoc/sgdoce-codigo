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
 * @name Numeric
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Numeric extends Validate
{
    /**
     * padrao de validacao de numeros base 10
     *
     * @var string
     * */
    const DIGIT = '[[:digit:]]+';

    /**
     * padrao de validacao de numeros base 16
     *
     * @var string
     * */
    const HEXA = '[[:xdigit:]]+';

    /**
     * padrao de validacao de numeros base 8
     *
     * @var string
     * */
    const OCTAL = '0[0-7]+';

    /**
     * padrao de validacao de numeros base 10 longo
     *
     * @var string
     * */
    const LONG = '[0-9]+';

    /**
     * padrao de validacao de numeros fracionarios
     *
     * @var string
     * */
    const DOUBLE = '(?:[0-9]*[\.][0-9]+)|(?:[0-9]+[\.][0-9]*)';

    /**
     * padrao de validacao de numeros exponenciais
     * */
    const EXPONENTIAL = '(?:(?:[0-9]+|(?:[0-9]*[\.][0-9]+)|(?:[0-9]+[\.][0-9]*))[eE][+-]?[0-9]+)';

    /**
     * {@inheritdoc}
     * */
    public function isValid($suspicious)
    {
        $tpl = '/^([-+]?(?:%s|%s|%s|%s|%s|%s))$/';
        $pattern = sprintf($tpl, self::DIGIT, self::HEXA, self::OCTAL, self::LONG, self::DOUBLE, self::EXPONENTIAL);
        preg_match($pattern, $suspicious, $result);
        return isset($result[1]);
    }
}