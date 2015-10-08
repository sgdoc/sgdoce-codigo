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
namespace br\gov\sial\core\mvcb\view\skeleton;
use br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb.view
 * @subpackage skeleton
 * @name Translator
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Translator extends SIALAbstract
{
    /**
     * Referência para linguagem alvo.
     *
     * @var Language
     * */
    protected $_language;

    /**
     * Construtor.
     *
     * @param Language $language
     * */
    public function __construct (Language $language)
    {
        $this->_language = $language;
    }

    /**
     * @param Element[] $content
     * @return br\gov\sial\core\mvcb\view\skeleton\Language
     * */
    public function translate (Reader $reader)
    {
        while (($element = $reader->read())) {
            $this->_language->translate($element);
        }
        return $this->_language;
    }
}