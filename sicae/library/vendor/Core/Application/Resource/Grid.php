<?php
/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
/**
 * @package    Core
 * @subpackage Application
 * @subpackage Resource
 * @name       Message
 * @category   Resource
 */
class Core_Application_Resource_Grid extends Zend_Application_Resource_ResourceAbstract
{
    protected $translate;

    /**
     */
    public function init()
    {
        if ($this->getBootstrap()->hasPluginResource('translate') && !$this->translate) {
            $this->getBootstrap()->bootstrap('translate');
            $this->translate = $this->getBootstrap()->getResource('translate');
        }

        if ($this->translate) {
            Core_View_Helper_Grid::setDefaultTranslator($this->translate);
        }
    }

    public function setRange(array $range)
    {
        Core_View_Helper_Grid::setDefaultRange($range);
    }

    public function setTranslate($options)
    {
        switch (true) {
            case $options instanceof Zend_Translate:
                $this->translate = $options;
                break;
            case is_array($options):
                $this->translate = new Zend_Translate($options);
                break;
            case Zend_Registry::isRegistered($options):
                $this->translate = Zend_Registry::get($options);
                if (!$this->translate instanceof Zend_Translate) {
                    throw new UnexpectedValueException();
                }
                break;
            default:
                throw new InvalidArgumentException();
        }
    }
}
