<?php

/*
 * Copyright 2012 ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos
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

namespace Principal\Service;

class Usuario extends \Sica_Service_Crud
{

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:Usuario';

    /**
     * Entidade Usuario
     * @return \Doctrine\ORM\EntityRepository
     */
    public function userEntity()
    {
        return $this->_getRepository($this->_entityName);
    }

    /**
     *
     * @param \Core_Dto_Mapping $dtoPass
     * @param \Core_Dto_Entity $dtoUser
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    public function changePass($dtoPass, $dtoUser, $checkPass = TRUE, $recover = FALSE)
    {
        $this->validatePass($dtoPass);

        $repository = $this->_getRepository();
        $user = $repository->find($dtoUser->getSqUsuario());

        $config = \Zend_Registry::get('configs');

        if ($config['authenticate']['ldap']) {
            $this->_changePassLdap($dtoPass, $user, $recover);
            $this->_changePassDb($dtoPass, $user, false);
        } else {
            $this->_changePassDb($dtoPass, $user, $checkPass);
        }
    }

    public function changePassWithMail(\Core_Dto_Mapping $dtoPass, \Core_Dto_Entity $dtoUser = NULL)
    {
        $this->validatePass($dtoPass);

        $repository = $this->_getRepository();
        $user = $repository->find($dtoUser->getSqUsuario());
        $user->setTxSenha(md5($dtoPass->getTxSenhaNova()));

        $config = \Zend_Registry::get('configs');

        if ($config['authenticate']['ldap']) {
            $this->_changePassLdap($dtoPass, $user);
            $this->_changePassDb($dtoPass, $user, false);
        } else {
            $this->_changePassDb($dtoPass, $user);
        }
    }

    /**
     *
     * @param \Core_Dto_Mapping $dtoPass
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    public function validatePass(\Core_Dto_Mapping $dtoPass)
    {
        $data = $dtoPass->toArray();

        $filters = array();

        $validators = array(
            'txSenha' => array(
                array('NotEmpty'),
                array('StringLength', array('min' => 6)),
                'messages' => array(
                    0 => 'O campo Senha Antiga é de preenchimento obrigatório',
                    1 => 'MN043')
            ),
            'txSenhaNova' => array(
                array('NotEmpty'),
                array('StringLength', array('min' => 6)),
                'messages' => array(
                    0 => 'O campo Nova Senha é de preenchimento obrigatório',
                    1 => 'MN043')
            ),
            'txSenhaNovaConfirm' => array(
                array('NotEmpty'),
                array('StringLength', array('min' => 6)),
                array('Identical', $data['txSenhaNova']),
                'messages' => array(
                    0 => 'O campo Confirmação Nova Senha é de preenchimento obrigatório',
                    1 => 'MN043',
                    2 => 'A confirmação da nova senha não confere.'
                )
            )
        );

        $input = new \Zend_Filter_Input($filters, $validators, $data);

        if (!$input->isValid()) {
            foreach ($input->getMessages() as $msgError) {
                switch (key($msgError)) {
                    case 'isEmpty' :
                        $this->getMessaging()->addErrorMessage($msgError['isEmpty']);
                        break;
                    case 'stringLengthTooShort' :
                        $this->getMessaging()->addErrorMessage($msgError['stringLengthTooShort']);
                        break;
                    case 'notSame' :
                        $this->getMessaging()->addErrorMessage($msgError['notSame']);
                        break;
                }
            }
            $this->getMessaging()->dispatchPackets();
            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    /**
     *
     * @param \Core_Dto_Entity $dtoPerson
     * @param \Core_Dto_Entity $dtoMail
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    public function recoverPass(\Core_Dto_Abstract $dtoPerson, \Core_Dto_Entity $dtoMail = NULL)
    {
        try {
            $person = $this->_getRepository()->findUserByCpfMail($dtoPerson, $dtoMail);

            $this->_validateUser($person);
            $hash = $this->_generateHash($person['txSenha'], $person['sqUsuario']);
            $this->_mailPassword($hash, $person);
        } catch (\Zend_Mail_Exception $exc) {
            throw new $exc;
        }
    }

    /**
     * Envio de email para recuperação de senha
     * @param \Doctrine\ORM\EntityRepository $entityUser
     */
    protected function _mailPassword($hash, $person)
    {
        $config = \Zend_Registry::getInstance()->get('configs');
        $urlSistema = rtrim($config['urlSica'], '/') . '/';

        $mail = new \Zend_Mail('UTF-8');

        $html = new \Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/modules/principal/views/scripts/mail/');

        $html->assign('name', $person['noPessoa']);
        $html->assign('urlSica', $urlSistema . 'usuario/change-pass-token/token/' . $hash);

        $bodyText = $html->render('mail.phtml');

        $mail->addTo($person['txEmail'])
                ->setSubject('[ICMBio] Alteração de Senha')
                ->setBodyHtml($bodyText)
                ->send();
    }

    /**
     *
     * @param int $code
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    public function validateCaptcha($code)
    {
        $captcha = \Sica_Captcha_Adapter::factory();
        if ($captcha->checkCode($code) === FALSE) {
            $this->getMessaging()->addErrorMessage('MN011');
            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    /**
     * Valida os dados do usuario para o envio de email
     * @param array $person
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    protected function _validateUser($person, $dto = NULL)
    {
        if ($person === NULL) { //Usuario inexistente
            $this->getMessaging()->addErrorMessage('MN124');
            $this->getMessaging()->dispatchPackets();
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        if ($person['stAtivo'] === FALSE) { //Usuario Inativo
            $this->getMessaging()->addErrorMessage('MN003');
            $this->getMessaging()->dispatchPackets();
            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    public function validateToken($params)
    {
        $sqUsuario = base64_decode(current(array_reverse(explode('-', $params['token']))));
        $user = $this->find($sqUsuario);

        if (isset($user) == FALSE) {
            $this->getMessaging()->addErrorMessage('MN012');
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        $data = array(
            'nuCpf' => $user->getSqPessoa()->getSqPessoaFisica()->getNuCpf(),
            'txSenha' => $user->getTxSenha(),
            'sqUsuario' => $user->getSqUsuario()
        );

        if ($this->_generateHash($data['txSenha'], $data['sqUsuario']) !== $params['token']) {
            $this->getMessaging()->addErrorMessage('MN012');
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        return $user->getSqUsuario();
    }

    protected function _generateHash($password, $identifierUser)
    {
        $string = $password;
        $options = array('salt' => self::SALT);
        $encrypt = new \Sica_Filter_Encrypt($options);
        $sqEncripted = '-' . base64_encode($identifierUser);

        return md5($encrypt->filter($string)) . $sqEncripted;
    }

    protected function _changePassDb(\Core_Dto_Mapping $dtoPass, $userEntity, $checkPass)
    {
        if ($checkPass) {
            $this->_checkPass($dtoPass, $userEntity);
        }

        $userEntity->setTxSenha(md5($dtoPass->getTxSenhaNova()));

        $this->getEntityManager()->persist($userEntity);
        $this->getEntityManager()->flush();
    }

    private function _adminAuthLDAP ()
    {
        $smb4 = $this->getEntityManager('ldap');
        $config = \Zend_Registry::get('configs');
        $admUsrSmb4 = $config['authenticate']['username'];
        $admPwdSmb4 = $config['authenticate']['password'];

        return $smb4->getPersist()->bind($admUsrSmb4, $admPwdSmb4);
    }

    protected function _changePassLdap(\Core_Dto_Mapping $dtoPass, $userEntity, $recover = FALSE)
    {
        try {
            $ldapUser = $userEntity->getSqPessoa()->getSqPessoaFisica()->getNuCpf();
            $userPasswd = $dtoPass->getTxSenha();
            $adminAuth = $this->_adminAuthLDAP();
            if (!$recover) {
                $adminAuth->bind($ldapUser, $userPasswd);
            }
            $userDn = current($adminAuth->search("samAccountName={$ldapUser}")->toArray());
            if (!$userDn) {
                throw new \Core_Exception_ServiceLayer_Verification("Usuário inexistente no LDAP");
            }
            $userData = array();
            \Zend_Ldap_Attribute::setPassword($userData, $dtoPass->getTxSenhaNova(), \Zend_Ldap_Attribute::PASSWORD_UNICODEPWD);
            $this->_adminAuthLDAP()->update($userDn['dn'], $userData);

        } catch (\Zend_Ldap_Exception $exc) {
            $message = sprintf('[SICA-e] LDAP Error in %s: "%s"', __METHOD__, $exc->getMessage());
            error_log( $message );
            $this->getMessaging()->addErrorMessage($exc->getMessage());
            $message = sprintf('[Erro no LDAP] %s', $exc->getMessage());
            $ldapCode = $exc->getCode();
            if ($ldapCode > 0) {
                $message = sprintf('LDAP0x%x',$ldapCode);
            }
            throw new \Core_Exception_ServiceLayer_Verification($message);
        }
    }

    protected function _checkPass(\Core_Dto_Mapping $dtoPass, $userEntity)
    {
        if (count($userEntity) && ($userEntity->getTxSenha() !== md5($dtoPass->getTxSenha()))) {
            $this->getMessaging()->addErrorMessage('MN008');
            $this->getMessaging()->dispatchPackets();

            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    public function listGridUsersInternals(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->searchPageDto('listGridUsersInternals', $dto);
    }

    public function findProfilesBind($identifier)
    {
        return $this->_getRepository()->findProfilesBind($identifier);
    }

    public function findDataViewUserInternal($identifier)
    {
        return $this->_getRepository()->findDataViewUserInternal($identifier);
    }

    public function deleteProfile(\Core_Dto_Mapping $mapping)
    {
        $criteria = array(
            'sqUsuario' => $mapping->getUsuario(),
            'sqUnidadeOrgPessoa' => $mapping->getUnidade(),
            'sqPerfil' => $mapping->getPerfil()
        );
        $perfis = $this->_getRepository('app:UsuarioPerfil')->findBy($criteria);

        foreach ($perfis as $perfil){
            $this->getEntityManager()->remove($perfil);
            $this->getEntityManager()->flush();
        }
        if (!$this->getServiceLocator()
                        ->getService('UsuarioPerfil')
                        ->findBy(array('sqUsuario' => $mapping->getUsuario()))) {

            $entity = $this->find($mapping->getUsuario());

            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush($entity);
        }
    }

    public function saveBindProfile(\Core_Dto_Mapping $mapping, array $perfis)
    {
        $this->getServiceLocator()->getService('UsuarioPerfil')
                ->countPerfil($mapping);

        $this->_getRepository()->saveBindProfile($mapping, $perfis);
    }

    public function sendMail(\Core_Dto_Mapping $mapping, array $perfis)
    {
        $data = $this->_getRepository()->getDataMail($mapping, $perfis);

        $mail = \Core_Registry::getMailer();

        $mail->getView()
                ->assign('usuario', $data['usuario'])
                ->assign('perfis', $data['perfis']);

        $mail->setSubject('[ICMBio] Atribuição de Perfil');
        $mail->addTo($data['usuario']['txEmail']);
        $mail->setBodyHtml('usuario-interno/mail-liberacao.phtml');
        $mail->send();

        return TRUE;
    }

    public function save(\Core_Dto_Entity $dto, \Core_Dto_Mapping $mapping = NULL)
    {
        if ($this instanceof \Principal\Service\UsuarioExterno) {
            return parent::save($dto, $mapping);
        }
        $usuario = $this->findOneBy(
                array('sqPessoa' => $dto->getEntity()->getSqPessoa()->getSqPessoa())
        );

        if ($usuario !== NULL) {
            $this->getMessaging()->addErrorMessage('MN036');
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        $result = $this->_getRepository('app:PessoaFisica')->findDataInstitucional(
                $dto->getEntity()->getSqPessoa()->getSqPessoa()
        );

        if (!isset($result['nuCpf'], $result['nuDdd'], $result['nuTelefone'], $result['txEmail'])) {
            $this->getMessaging()->addErrorMessage('MN121');
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        $this->getEntityManager()->getUnitOfWork()->registerManaged(
                $dto->getEntity()->getSqPessoa(), array('sqPessoa' => $dto->getEntity()
                    ->getSqPessoa()->getSqPessoa()), array()
        );

        $entity = $dto->getEntity();
        $entity->setStAtivo(TRUE);

        $config = \Zend_Registry::get('configs');

        if ($config['authenticate']['ldap'] == TRUE) {
            /**
             * @todo caso usuário não exista no LDAP, falta regra de exceção
             */
        } else {
            $entity->setTxSenha(md5(\Zend_Filter::filterStatic($mapping->getNuCpf(), 'Digits')));
        }

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function findUsers(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->findUsers($dto);
    }

    public function mountAcl($mapping)
    {
        $rotas = $this->getServiceLocator()
                ->getService('Funcionalidade')
                ->getAllByPerfil($mapping->getSqPerfil());

        if (!$rotas) {
            return FALSE;
        }

        $acl = new \Zend_Acl();
        $acl->addRole(new \Zend_Acl_Role($mapping->getNoPerfil()));

        foreach ($rotas as $rota) {
            if (!$acl->has(trim($rota))) {
                $acl->addResource(new \Zend_Acl_Resource(trim($rota)));
                $acl->allow($mapping->getNoPerfil(), trim($rota));
            }
        }

        return new \Core_Acl_AclSession($acl);
    }

}
