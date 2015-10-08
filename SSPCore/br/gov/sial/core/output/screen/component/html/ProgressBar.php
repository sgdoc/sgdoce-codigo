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
namespace br\gov\sial\core\output\screen\component\html;
use br\gov\sial\core\output\screen\IBuild,
    br\gov\sial\core\output\screen\html\Div,
    br\gov\sial\core\output\screen\component\ProgressBarAbstract;

/**
 * @package br.gov.sial.core.output.screen.component
 * @subpackage html
 * @name ProgressBar
 * */
class ProgressBar extends ProgressBarAbstract implements IBuild
{
    /**
     * @param \stdClass $config
     * @param integer   $config->completed
     */
    public function __construct (\stdClass $config)
    {
        $this->_completed = isset($config->completed) ? $config->completed : 0;
        $this->_type      = isset($config->type) ? $config->type : NULL;
        $this->_active    = isset($config->active) ? $config->active : NULL;
        $this->_progress  = Div::factory();
        $this->_bar       = Div::factory();
    }

    /**
     * @return AlertAbstract
     */
    public function build ()
    {
        $this->_progress->addClass('progress');
        
        if (!empty($this->_type)) {
            $this->isValidType();
            foreach ((array) $this->_type as $type) {
                $this->_progress->addClass('progress-' . $type);
            }
        }
        
        if ($this->_active) {
            $this->_progress->addClass('active');
        }
        
        $this->_bar->addClass('bar')
                   ->attr('style', sprintf('width: %02.1f%%', $this->_completed));
        
        $this->_progress->add($this->_bar);
        
        return $this;
    }
}