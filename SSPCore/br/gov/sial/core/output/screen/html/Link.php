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
namespace br\gov\sial\core\output\screen\html;
use br\gov\sial\core\output\screen\LinkAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage html
 * @name Link
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Link extends LinkAbstract
{
    /**
     * @var string
     * */
    const T_TAG = 'link';

    /**
     * @param string $href
     * @param string $rel
     * */
    public function __construct ($href, $rel = NULL)
    {
        $this->attr('href', $href);

        if (NULL !== $rel) {
              $this->attr('rel', $rel);
        }
    }
    /**
     * @override
     * @return string
     * */
    public function render ()
    {
        return sprintf('<%1$s%2$s>', $this::T_TAG, $this->renderProperty());
    }
}