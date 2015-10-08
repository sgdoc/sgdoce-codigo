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
 * @package    Core
 * @subpackage View
 * @subpackage Helper
 * @name       SecureToken
 * @category   View Helper
 */
class Core_View_Helper_SecureToken extends Zend_View_Helper_Abstract
{
    /**
     * @var Core_Secure_Token
     */
    protected $_objectToken;

    /**
     * @return void
     */
    public function __construct()
    {
        $this->_objectToken = new Core_Secure_Token();
    }

    /**
     * @param string $identifier
     * @param int    $timeout
     */
    public function secureToken($identifier = null, $timeout = null)
    {
        if (0 === func_num_args()) {
            return $this;
        }

        if (null !== $identifier) {
            $this->_objectToken->setIdentifier($identifier);
        }

        if (null !== $timeout) {
            $this->_objectToken->setTimeout($timeout);
        }

        return $this->_objectToken->getToken();
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->view->hidden(
            $this->_objectToken->getIdentifier(),
            $this->_objectToken->getToken()
        );
    }

    /**
     * @param  mixed $method
     * @param  array $args
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (!method_exists($this->_objectToken, $method)) {
            throw new BadMethodCallException(
                sprintf('Método "%s::%s()" inexistente.', get_class($this->_objectToken), $method)
            );
        }

        return call_user_func_array(array($this->_objectToken, $method), $args);
    }
}
