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
namespace br\gov\sial\core\mvcb\view\helper;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 * Importa phtml.
 *
 * @package br.gov.sial.core.mvcb.view
 * @subpackage helper
 * @name Import
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class Import extends SIALAbstract
{
    /**
     * @var string
     * */
    const MISSING_VSCRIPT = "O vScript solicitado é inexistente ou inválido.";

    /**
     * Importa um viewScript de outro módulo.
     * <p>
     *     Se o segundo param for informado define o sistema/modulo do viewScript
     * </p>
     * <p>
     *     Se o terceiro param for inforamdo define qual tipo de de viewScript sera importado (html, json, etc...)
     * </p>
     * <p>
     *     O viewScript devera em um dos subsistemas em application
     * </p>
     * <p>
     *     O quarto param define o contexto que o vScript ira trabalhar, caso seja informado
     * </p>
     *
     * @code
     * <?php
     *     Import::import('frmCadastro', 'sica/sistema');
     *
     *     // ou
     *
     *     Import::import('frmCadastro', 'sica/sistema', 'html');
     * ?>
     * @endcode
     *
     * @param string $vScript
     * @param string $module
     * @param string $type
     * @param Object $contexto
     * @throws IllegalArgumentException
     * */
    public function import ($vScript, $module = NULL, $type = 'html', $vthis = NULL)
    {
        # basepath
        $basepath = debug_backtrace();
        preg_match('/(?<basepath>.*)\/application\/(?<module>.*)\/mvcb/', $basepath[2]['file'], $pathInfo);

        # full application path
        $basepath = $pathInfo['basepath'] . DIRECTORY_SEPARATOR . 'application';

        # module
        $module = $module ?: $pathInfo['module'];

        # mount vScript fullpath
        $vScriptPath =  $basepath . DIRECTORY_SEPARATOR
                      . $module   . DIRECTORY_SEPARATOR
                      . preg_replace('/:/', DIRECTORY_SEPARATOR, "mvcb:view:scripts:{$type}:")
                      . ucfirst($vScript)
                      . '.phtml';

        IllegalArgumentException::throwsExceptionIfParamIsNull(
            is_file($vScriptPath), self::MISSING_VSCRIPT
        );
        require_once $vScriptPath;
    }
}