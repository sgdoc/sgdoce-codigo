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
namespace br\gov\sial\core\util;
use br\gov\sial\core\SIALAbstract;

/**
 * SIAL
 *
 * super classe de abstracao de protecao contra dados malicioso
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @name ProtectionAbstract
 * @author J. Augusto <augustowebd@gmail.com>
 * */
abstract class ProtectionAbstract extends SIALAbstract
{
    /**
     * analisa e codifica o conteudo informado aplicando-lhe um conjunto de regras que a posterior podera ser
     * deconfificada estado original a fim de proteger um deteminado dispositivo* de possivel fragmentos
     * maliciosos inseridos no conteudo manipulado
     *
     * @param string $content
     * @return string
     * @codeCoverageIgnoreStart
     * */
    public abstract function encode ($content);
    // @codeCoverageIgnoreEnd

    /**
     * desfaz as regras aplicadas pelo metodo encode a fim de obter o conteudo original antes das aplicacao das regras
     *
     * @param string $content
     * @return string
     * @codeCoverageIgnoreStart
     * */
    public abstract function decode ($content);
    // @codeCoverageIgnoreEnd
}