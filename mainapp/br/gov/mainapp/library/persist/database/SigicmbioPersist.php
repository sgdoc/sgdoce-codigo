<?php
/*
 * Copyright 2013 ICMBio
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
namespace br\gov\mainapp\library\persist\database;
use br\gov\mainapp\library\persist\database\Persist as ParentPersist;
use br\gov\sial\core\valueObject\ValueObjectAbstract;

/**
 * @package br.gov.mainapp.library.persist
 * @subpackage database
 * @name SigicmbioPersist
 * */
class SigicmbioPersist extends ParentPersist
{
    /**
     * Função que monta os dados para pesquisa
     * @param ValueObjectAbstract $valueObject
     */
    protected function _preparePersist(ValueObjectAbstract $valueObject)
    {
        $arrVo = $valueObject->toArray();
        $attrs = (array) $valueObject->annotation()->load()->attrs;

        foreach ($arrVo as $key => $elmnt) {
            if (isset($attrs[$key]->database)) {
                if ('string' == $attrs[$key]->type && NULL != $elmnt) {
                    $elmnt = '%' . $elmnt . '%';
                }
                $this->_params[$key] = (object) array('type' => $attrs[$key]->type, 'value' => '' == $elmnt ? NULL : $elmnt);
            }
        }
    }
}