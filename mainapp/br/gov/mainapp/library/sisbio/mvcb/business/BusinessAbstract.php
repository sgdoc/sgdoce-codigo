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
namespace br\gov\mainapp\library\sibio\mvcb\business;

/**
 * SIAL
 *
 * @package com\appdemo\library\mvcb
 * @subpackage business
 * @name BusinessAbstract
 * @author SIAL Generator
 * */
class BusinessAbstract extends \br\gov\sial\core\mvcb\business\BusinessAbstract
{
    /**
     * converte um array de value objects em um array de c
     * @param string $attrKey
     * @param string $attrValue
     * @param ArrayObject $arrValueObject
     * @param boolean $hasFirstOption
     * @param string $keyNameForValue
     * @param string $keyNameForKey
     * @return string[]
     */
    public function arrayObjectToCombo (
        $attrValue, 
        $attrKey, 
        $arrValueObject, 
        $hasFirstOption = TRUE, 
        $keyNameForValue = 'text',
        $keyNameForKey = 'value'
    ) {
        $arr = array();
        
        if ($hasFirstOption) {
            $arr[] = array($keyNameForValue => 'Selecione uma opção', $keyNameForKey => '');
        }
        
        foreach ($arrValueObject as $vo) {
            $getterKey   = 'get' . ucfirst($attrKey);
            $getterValue = 'get' . ucfirst($attrValue);
            $arr[] = array($keyNameForValue => $vo->$getterValue(), $keyNameForKey => $vo->$getterKey());
        }
        return $arr;
    }
}
