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

namespace br\gov\sial\core\util\document;
use br\gov\sial\core\util\document\Pageable;

/**
 * SIAL
 *
 * @package br.gov.sial.core.util
 * @subpackage document
 * @name Documentable
 * @author michael fernandes <michael.rodrigues@icmbio.gov.br>
 * @author bruno menezes <bruno.menezes@icmbio.gov.br>
 * */
interface Documentable
{

    /**
     * Retorna por meio de uma interface fluente o objeto das propriedades do documento
     */
    public function property ();

    /**
     * Retorna uma nova página de acordo com o tipo definido
     */
    public function newPage ($name);

    /**
     * Remove uma página específica do documento
     */
    public function removePage ($idx);

    /**
     *  Reomve todas as páginas
     */
    public function removePages ();

    /**
     * Salva o documento em disco no diretório especificado
     */
    public function save ($path);
}