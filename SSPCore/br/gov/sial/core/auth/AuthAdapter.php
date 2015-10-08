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
namespace br\gov\sial\core\auth;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\mvcb\model\ModelAbstract,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * Classe adpatora para autenticação.
 *
 * @package br.gov.sial.core
 * @subpackage auth
 * @name AuthAdapter
 * @author J. Augusto <augustowebd@gmail.com>
 * @author Cleiton Coimbra <cleiton.coimbra@gmail.com>
 * */
class AuthAdapter extends SIALAbstract implements AuthentiCable
{
    /**
     * Mensagens sobre uma tentativade de autenticação mal-sucedida.
     *
     * @var array
     */
    private $_messages = array();

    /**
     * Código de erro ou sucesso ocorrido na autenticacao.
     *
     * @var integer
     */
    private $_code;

    /**
     * Refêrencia do valueObject para autenticação.
     *
     *  @var ValueObjectAbstract
     * */
    private $_valueObject;

    /**
     * Referência da model a ser usada para autenticação.
     *
     * @var br\gov\sial\core\mvcb\model\ModelAbstract
     */
    private $_model;

    /**
     * Nome da propriedade no valueObject usada para obter o nome de usuário.
     *
     * @var string
     * */
    protected $_attrIdentity;

    /**
     * Nome da propriedade no valueObject usada para obter a senha.
     *
     * @param string
     * */
    private $_attrCredential;

    /**
     * Nome do método criptografico que será aplicado sob a senha.
     *
     * @param string
     * */
    private $_attCredentialTreatment;

    /**
     * Construtor.
     *
     * @param ValueObjectAbstract $valueObject
     * @param ModelAbstract $model
     * */
    public function __construct (ValueObjectAbstract $valueObject, ModelAbstract $model)
    {
        $this->_valueObject = $valueObject;
        $this->_model       = $model;
    }

    /**
     * Fábrica de AuthAdapter.
     *
     * @param \br\gov\sial\core\valueObject\ValueObjectAbstract $valueObject
     * @param \br\gov\sial\core\mvcb\model\ModelAbstract $model
     * @return \self
     */
    public static function factory (ValueObjectAbstract $valueObject, ModelAbstract $model)
    {
        return new self($valueObject, $model);
    }

    /**
     * Método responsável por efetuar autenticação.
     *
     * */
    public function authenticate ()
    {
        $model       = $this->_model;
        $valueObject = $this->_getIdentityValueObject();

        $identitys   = $model->findByParam($valueObject)->getAllValueObject();

        if (TRUE == $this->_validadeResult($identitys)) {
            $this->_bindSession($identitys);
        }
    }

    /**
     * Verifica se a autenticação foi realizada com sucesso.
     *
     * @return boolean
     * */
    public function isValid ()
    {
        return self::SUCCESS == $this->_code;
    }

    /**
     * Retorna uma constante de AuthentiCable para determinar se houve falha
     * ou sucesso na autenticação.
     *
     * @return integer
     * */
    public function getCode ()
    {
        return $this->_code;
    }

    /**
     * Retorna a identidade utilizada na tentativa de autenticação.
     *
     * @return string
     * */
    public function getIdenttity ()
    {
        $method = $this->_getAcessorMethod('get', $this->_attrIdentity);
        return $this->_valueObject->$method();
    }

    /**
     * Retorna um array de mensagens sobre uma falha na autenticação.
     *
     * @return string[]
     * */
    public function getMessages ()
    {
        return $this->_messages;
    }

    /**
     * Registra o nome do attr do valueObject que contém o nome de usuário.
     *
     * @param string $attrIdentity
     * @return AuthAdapterAbstract
     * @throws IllegalArgumentException
     * */
    public function setIdentityColumn ($attrIdentity)
    {
        $this->_checkPropertyExists($attrIdentity);
        $this->_attrIdentity = $attrIdentity;
        return $this;
    }

    /**
     * Registra nome do attr do valueObject que contém a credencial de usuário.
     *
     * @param string $attrCredential
     * @return AuthAdapterAbstract
     * @throws IllegalArgumentException
     * */
    public function setCredentialColumn ($attrCredential)
    {
        $this->_checkPropertyExists($attrCredential);
        $this->_attrCredential = $attrCredential;
        return $this;
    }

    /**
     * Registra nome do método criptográfico que será aplicado sob a senha.
     *
     * @param string $attrCredentialTreatment
     * @return AuthAdapterAbstract
     * @throws IllegalArgumentException
     * */
    public function setCredentialTreatment ($attrCredentialTreatment)
    {
        IllegalArgumentException::throwsExceptionIfParamIsNull(!empty($attrCredentialTreatment)
                                                               , 'Nome do método criptográfico é inválido.');
        $this->_attCredentialTreatment = $attrCredentialTreatment;
        return $this;
    }

    /**
     * Lança exceção caso o atributo recebido não exista no objeto $_valueObject.
     *
     * @param string $attrIdentity
     * @throws IllegalArgumentException
     */
    private function _checkPropertyExists ($attrIdentity)
    {
        $attrExists = property_exists($this->_valueObject, '_' . $attrIdentity);
        IllegalArgumentException::throwsExceptionIfParamIsNull($attrExists, 'Nome do attr do valueObject é inválido');
    }

    /**
     * Retorna a senha criptografada.
     *
     * @return string
     * */
    private function _getPassword ()
    {
        $method   = $this->_getAcessorMethod('get', $this->_attrCredential);
        $password = $this->_valueObject->$method();
        $credentialTreatment = $this->_attCredentialTreatment;

        return $credentialTreatment($password);
    }

    /**
     * Retorna o valueObject com a senha criptografada e o usuário setados.
     *
     * @return ValueObjectAbstract
     */
    private function _getIdentityValueObject ()
    {
        #cria uma nova instancia do valueObject.
        $valueObject = $this->_valueObject->factory();

        #seta o identity.
        $setIdentity = $this->_getAcessorMethod('set', $this->_attrIdentity);
        $valueObject->$setIdentity($this->getIdenttity());

        #seta a senha
        $setPassword = $this->_getAcessorMethod('set', $this->_attrCredential);
        $valueObject->$setPassword($this->_getPassword());

        return $valueObject;
    }

    /**
     * Obtém o nome do método acessor e do atributo informados.
     *
     * @param string $typeAcessor
     * @param string $attrAtribute
     * @return string
     * @throws IllegalArgumentException
     */
    private function _getAcessorMethod ($typeAcessor, $attrAtribute)
    {
        $this->_checkPropertyExists($attrAtribute);
        IllegalArgumentException::throwsExceptionIfParamIsNull(in_array($typeAcessor, array('get', 'set')),
                                                               'Tipo de método acessor é inválido.');

        $annotation = $this->_valueObject->annotation()->load();
        $attrs      = $annotation->attrs;
        $attrValue  = $attrs->$attrAtribute;

        return $attrValue->$typeAcessor;
    }

    /**
     * Realiza validação do resultado obtido no processo de autenticação.
     *
     * @param array $identitys
     * @return boolean
     */
    private function _validadeResult ($identitys)
    {
        switch (count($identitys)) {
            case 0 :
                $this->_code = self::FAILURE_IDENTITY_NOT_FOUND;
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            case 1 :
                $this->_code = self::SUCCESS;
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd

            default :
                $this->_code = self::FAILURE_IDENTITY_AMBIGUOUS;
                // @codeCoverageIgnoreStart
                break;
                // @codeCoverageIgnoreEnd
        }

        return $this->isValid();
    }

    /**
     * Armazena os dados do usuário autenticado na sessão.
     *
     * @param array $identitys
     */
    private function _bindSession ($identitys)
    {
        $valueObject = current($identitys);
        $this->_cleanPassword($valueObject);
        AuthStorage::setStorage($valueObject);
    }

    /**
     * Reseta a senha do objeto autenticado.
     *
     * @param ValueObjectAbstract
     */
    private function _cleanPassword($valueObject)
    {
        $setPassword = $this->_getAcessorMethod('set', $this->_attrCredential);
        $valueObject->$setPassword(NULL);
    }
}