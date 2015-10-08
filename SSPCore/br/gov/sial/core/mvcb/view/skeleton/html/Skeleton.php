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
use br\gov\sial\core\mvcb\view\skeleton\Reader,
    br\gov\sial\core\mvcb\view\skeleton\Language,
    br\gov\sial\core\mvcb\view\skeleton\Translator,
    br\gov\sial\core\mvcb\view\skeleton\Skeleton as SSkeleton;

/**
 * SIAL
 *
 * @package br.gov.sial.core.mvcb.view.skeleton
 * @subpackage html
 * @name Skeleton
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Skeleton extends SSkeleton
{
    /**
     * @param string $filename
     * */
    public function __construct ($filename)
    {
        parent::__construct(
            new Reader($filename),
            new Writer(),
            new Translator(new Language('Html'))
        );
    }
}