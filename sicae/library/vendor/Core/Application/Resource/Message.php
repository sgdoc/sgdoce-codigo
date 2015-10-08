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
 * Registra e disponibiliza os recursos de mensagens necessários para a aplicação
 *
 * @package    Core
 * @subpackage Application
 * @subpackage Resource
 * @name       Message
 * @category   Resource
 */
class Core_Application_Resource_Message extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Zend_Translate
     */
    protected $_translate;

    /**
     * @return Zend_Controller_Plugin_Abstract
     */
    public function init()
    {
        $translate = $this->getTranslate();
        Core_Registry::setMessage($translate);
        return $translate;
    }

    /**
     * @return Zend_Translate
     */
    public function getTranslate()
    {
        if (null === $this->_translate) {
            $this->_translate = new Zend_Translate(array(
                'adapter' => 'Core_Translate_Message'
            ) + $this->getOptions());
        }

        return $this->_translate;
    }
}
