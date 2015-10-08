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
namespace br\gov\sial\core\mvcb\view;
use br\gov\sial\core\util\Location,
    br\gov\sial\core\mvcb\controller\ControllerAbstract;

/**
 * SIAL
 *
 * View em Html.
 *
 * @package br.gov.sial.core.mvcb
 * @subpackage view
 * @name View
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class Json extends Html
{
    /**
     * Construtor.
     *
     * @param string[] $config
     * */
    public function __construct (array $config = array())
    {
        parent::__construct($config);
    }

    /**
     * {@inheritdoc}
     * */
    public function mime ()
    {
        header('Content-type: application/json; charset=UTF-8');
    // @codeCoverageIgnoreStart
    }
    // @codeCoverageIgnoreEnd

    /**
     * Registra o caminho dos scripts de view baseando-se no controller informado.
     *
     * @param br\gov\sial\core\mvcb\controller\ControllerAbstract $ctrl
     * @return br\gov\sial\core\mvcb\view\View
     * */
    public function registerViewScriptBasedFromController (ControllerAbstract $ctrl)
    {
        $scriptPath = current(explode('mvcb', $ctrl->getClassName()))
                    . 'mvcb'    . self::NAMESPACE_SEPARATOR
                    . 'view'    . self::NAMESPACE_SEPARATOR
                    . 'scripts' . self::NAMESPACE_SEPARATOR
                    . 'json'
                    ;
        $this->addScriptPath(Location::realpathFromNamespace($scriptPath));
        return $this;
    }
}