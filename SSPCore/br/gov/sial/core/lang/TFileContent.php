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
namespace br\gov\sial\core\lang;

/**
 * SIAL
 *
 * ValueObject responsável por manter arquivos.
 *
 * @package br.gov.sial.core
 * @subpackage lang
 * @name TFileContent
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class TFileContent extends TFile
{
    /**
     * @attr (
     *  name="content",
     *  file="content",
     *  type="blob",
     *  get="getContent",
     *  set="setContent"
     * )
     * */
    private $_content;

    /**
     * Construtor.
     *
     * @param string $content
     * */
    public function __construct ($content = NULL)
    {
        parent::__construct();
        $this->setContent($content);
    }

    /**
     * Recupera o conteúdo do arquivo.
     *
     * @return string
     * */
    public function getContent ()
    {
        return $this->_content;
    }

    /**
     * Atribui um conteúdo ao arquivo.
     *
     * @param string $content
     * @return \br\gov\sial\core\lang\TFileContent
     * */
    public function setContent ($content = NULL)
    {
        $this->_content = $content;
        return $this;
    }
}