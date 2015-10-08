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
namespace br\gov\sial\core\mvcb\controller;
use br\gov\sial\core\Version;

/**
 * SIAL
 *
 * Este controle é de uso interno do SIAL, usado quando alguma de suas configurações não é execu-
 * tada conforme planejado. A saída de error será em HTML, caso seja necessário mudar o tipo de
 * saída deve ser registrado o novo tipo pelo método <b>ErrorController</b>::<i>outputType</i>
 *
 * @package br.gov.sial.core.mvcb
 * @subpackage controller
 * @name ErrorController
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class ErrorController extends ControllerAbstract
{
    /**
     * @var \br\gov\sial\core\exception\SIALException
     * */
    private $_exception;

    /**
     * Construtor.
     *
     * @param \br\gov\sial\core\exception\SIALException $siale
     * */
    public function __construct (\br\gov\sial\core\exception\SIALException $siale)
    {
        parent::__construct();
        $this->_exception = $siale;
    }

    /**
     * Quando ocorre algum erro no framework este método é automaticamete executado.
     *
     * @todo (trocar) note que o tipo da saida ja esta sendo definida estaticamente (html)
     * esta sainda deve ser considerada com base no tipo de aplicacao configuracao
     * no arquivo de config.ini
     *
     * @return string
     * */
    public function errorAction ()
    {
        $sep = current(explode(self::NAMESPACE_SEPARATOR, self::getNamespace($this)));
        $sialHome = (string) current(preg_split("/\/{$sep}\//", __DIR__)) . DIRECTORY_SEPARATOR
                                                                          . 'br/gov/sial/core/mvcb';

        $view = $this->getView();
        $viewScriptError = $sialHome
                         . DIRECTORY_SEPARATOR . 'view'
                         . DIRECTORY_SEPARATOR . 'scripts'
                         . DIRECTORY_SEPARATOR . 'html'
                         ;

        $params = (object) array_map('strtolower', $this->request()->getParams());

        if (('sial' == $params->m) && ('version' == $params->f) && ('showaction' == $params->a)) {

            return $view->set('version',Version::get())
                        ->render('version');
        }

        $view->addScriptPath($viewScriptError);
        // @codeCoverageIgnoreStart
        if (defined('APPLICATION_ENV') && 'development' == constant('APPLICATION_ENV' )) {
            return $view->set('errorLog',$this->_exception)
                        ->render('error');
        }
        // @codeCoverageIgnoreEnd
        return $view->render('errorEnviroment');
    }
}
