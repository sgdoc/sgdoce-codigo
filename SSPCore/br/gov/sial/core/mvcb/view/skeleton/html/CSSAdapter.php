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
namespace br\gov\sial\core\mvcb\view\skeleton\html;
use br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * Procura por todas as propriedades que em HTML deve ser tratadas pelo uma lib javascript,
 * por questao de indepencia de lib js sera aplicado uma, ou mais, classes css e e lib js
 * em questao passa a tratar com base nesta(s) classes.
 *
 * @package br.gov.sial.core.mvcb.view.skeleton
 * @subpackage html
 * @name CSSHandlerAdapter
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class CSSAdapter extends SIALAbstract
{
    /**
     * Dicionário de propriedades que serão tratadas.
     *
     * @var string[]
     * */
    public static $_dictionary = array (
        'required'   => 'fld-required',
        'optional'   => 'fld-optional',
        'date'       => 'fld-date',
        'datetime'   => 'fld-datetime',
        'cpf'        => 'fld-cpf',
        'phone'      => 'fld-phone',
        'email'      => 'fld-email',
        'cep'        => 'fld-cep',
        'cnpj'       => 'fld-cnpj',
        'spinButton' => 'fld-spinButton'
    );

    /**
     * @param Command $command
     * @param string[] $propries
     * */
    public static function analise (Command $command, $propries)
    {
       $cssContent = NULL;
       foreach ($propries as $prop => $value) {

           if ('validate' == $prop) {
               # valor da propriedade
               $propValue =  $command->popProperty($prop);
               $validates = explode(',', $propValue);

               foreach ($validates as $validate) {
                   $cssContent .= ' ' . self::$_dictionary[trim($validate)];
               }
           }
       }

       if (NULL !== $cssContent) {
           $propries->class = trim($cssContent);
       }
    }
}