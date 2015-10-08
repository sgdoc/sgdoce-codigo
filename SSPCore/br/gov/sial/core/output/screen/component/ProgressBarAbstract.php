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
namespace br\gov\sial\core\output\screen\component;
use br\gov\sial\core\output\screen\IBuild,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\output\screen\component\ComponentAbstract;

/**
 * @package br.gov.sial.core.output.screen
 * @subpackage component
 * @name ProgressBarAbstract
 */
abstract class ProgressBarAbstract extends ComponentAbstract implements IBuild
{
    /**
     * @var integer
     */
    protected $_completed = 0;

    /**
     * @var \br\gov\sial\core\output\screen\html\Div
     */
    protected $_progress;
    
    /**
     * @var \br\gov\sial\core\output\screen\html\Div
     */
    protected $_bar;
    
    /**
     * @var string
     */
    protected $_type;
    
    /**
     * @var boolean
     */
    protected $_active = FALSE;
    
    /**
     * @var string
     */
    const T_ALERTABSTRACT_INVALID_TYPE = "Tipo de Progress Bar inválido";
    
    /**
     * @param string $type
     * @return ComponentAbstract
     */
    public static function factory ($config, $type)
    {
        $namespace = self::NSComponent('progressBar', $type);
        return new $namespace($config);
    }
    
    /**
     * @return string
     */
    public function render ()
    {
        return $this->_progress->render();
    }
    
    /**
     * @throws IllegalArgumentException
     */
    protected function isValidType ()
    {
        $typesAccepted = array('info', 'success', 'warning', 'danger', 'striped');
        
        $types = (array) $this->_type;
        
        foreach ($types as $type) {
            IllegalArgumentException::throwsExceptionIfParamIsNull(in_array($type, $typesAccepted), self::T_ALERTABSTRACT_INVALID_TYPE);
        }
    }    
}