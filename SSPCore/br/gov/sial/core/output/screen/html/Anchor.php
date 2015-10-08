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
use br\gov\sial\core\output\screen\ITextLevel,
    br\gov\sial\core\output\screen\ElementContainerAbstract;

/**
 * SIAL
 *
 * @package br.gov.sial.core.output.screen
 * @subpackage html
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Anchor extends ElementContainerAbstract implements ITextLevel
{
    /**
     * @var string
     * */
    const T_TAG = 'a';

    /**
     * @param string $text
     * @param string $href
     * @param string $target
     * */
    public function __construct ($text = NULL, $href = '#', $target = NULL)
    {
        $this->setContent((string) $text);

        if (NULL !== $href) {
            $this->attr('href', $href);
        }

        if (NULL !== $target) {
            $this->attr('target', $target);
        }
    }


    /**
     * @param string $text
     * @param string $href
     * @param string $target
     * */
    public static function factory ($text = NULL, $href = '#', $target = NULL)
    {
        return new self($text, $href, $target);
    }

    /**
     * para ser considerado link, $url precisa ser uma string inicianda por http ou https ou ftp
     *
     * @param string $url
     * @return boolean
     * */
    public function isLink ($url)
    {
        preg_match('/^((http)|(https)|(ftp)):\/\/([\- \w]+\.)+\w{2,3}(\/ [%\-\w]+(\.\w{2,})?)*(\/\w+.\w+)?\/?$/', $url, $matches, PREG_OFFSET_CAPTURE);
        return (boolean) count($matches);
    }
}