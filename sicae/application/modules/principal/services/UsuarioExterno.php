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

use Doctrine\Common\Util\Debug;

class UsuarioExterno extends \Principal\Service\Usuario
{
    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:UsuarioExterno';

    /**
     *
     * @var type
     */
    protected $_entityComp = array(
        'entity' => '\Sica\Model\Entity\UsuarioExternoDadoComplementar',
        'mapping' => array(
            'sqUsuarioExterno' => '\Sica\Model\Entity\UsuarioExterno',
            'sqPais' => '\Sica\Model\Entity\Pais',
            'sqEstado' => '\Sica\Model\Entity\Estado',
            'sqMunicipio' => '\Sica\Model\Entity\Municipio'
            ));

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
        $user = $repository->find($dtoUser->getSqUsuarioExterno());

        $this->_changePassDb($dtoPass, $user, $checkPass);
    }

    public function changePassWithMail(\Core_Dto_Mapping $dtoPass, \Core_Dto_Entity $dtoUser = NULL)
    {
        $this->validatePass($dtoPass);

        $user = $this->_getRepository()->find($dtoPass->getSqUsuarioExterno());
        $user->setTxSenha(md5($dtoPass->getTxSenhaNova()));

        $this->_changePassDb($dtoPass, $user, FALSE);
    }

    /**
     *
     * @param \Core_Dto_Entity $dto
     * @param \Core_Dto_Entity $dtoMail
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    public function recoverPass(\Core_Dto_Abstract $dto, \Core_Dto_Entity $dtoMail = NULL)
    {
        try {
            $person = $this->_getRepository()->recoverPass($dto);

            $this->_validateUser($person, $dto);
            $hash = $this->_generateHash($person['txSenha'], $person['sqUsuarioExterno']);
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

        $html->assign('name', $person['noUsuarioExterno']);
        $html->assign('urlSica', $urlSistema . 'usuario-externo/change-pass-token/token/' . $hash);

        $bodyText = $html->render('mail-usuario-externo.phtml');

        $mail->addTo($person['txEmail'])
                ->setSubject('[ICMBio] Alteração de Senha')
                ->setBodyHtml($bodyText)
                ->send();
    }

    /**
     * Valida os dados do usuario para o envio de email
     * @param array $person
     * @throws \Core_Exception_ServiceLayer_Verification
     */
    protected function _validateUser($person, $dto = NULL)
    {
        # Usuario inexistente
        if (!$person) {
            switch ($dto->getTpValidacao()) {
                case 'cpf':
                    $msg = 'MN124';
                    break;
                case 'cnpj':
                    $msg = 'MN120';
                    break;
                case 'passaporte':
                    $msg = 'MN119';
                    break;
            }

            $this->getMessaging()->addErrorMessage($msg);
            $this->getMessaging()->dispatchPackets();
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        # Usuario Inativo
        if (!$person['stRegistroAtivo']) {
            $this->getMessaging()->addErrorMessage('MN003');
            $this->getMessaging()->dispatchPackets();
            throw new \Core_Exception_ServiceLayer_Verification();
        }
    }

    public function validateToken($params)
    {
        $sqUsuario = base64_decode(current(array_reverse(explode('-', $params['token']))));
        $user = $this->find($sqUsuario);

        if (!$user) {
            $this->getMessaging()->addErrorMessage('MN169');
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        $data = array(
            'txSenha' => $user->getTxSenha(),
            'sqUsuarioExterno' => $user->getSqUsuarioExterno()
        );

        $hash = $this->_generateHash($user->getTxSenha(), $user->getSqUsuarioExterno());
        if ($hash !== $params['token']) {
            $this->getMessaging()->addErrorMessage('MN012');
            throw new \Core_Exception_ServiceLayer_Verification();
        }

        return $user->getSqUsuarioExterno();
    }

    public function getAllCombos($getEscolaridade = TRUE)
    {
        $cmb = array();

        if ($getEscolaridade) {
            $cmb['sqTipoEscolaridade'] = $this->getServiceLocator()
                    ->getService('TipoEscolaridade')
                    ->getComboDefault(array(), array('noTipoEscolaridade' => 'ASC'));
        }

        $cmb['sqPais'] = $this->getServiceLocator()
                ->getService('Pais')
                ->getComboDefault(array(), array('noPais' => 'ASC'));

        $idBrasil = \Sica\Model\Repository\Pais::ID_BRAZIL;
        $cmb['sqEstado'] = $this->getServiceLocator()
                ->getService('Estado')
                ->getComboDefault(array('sqPais' => $idBrasil), array('noEstado' => 'ASC'));

        $cmb['sqMunicipio'] = array();

        $cmb['sqSistema'] = $this->_getRepository('app:Sistema')->getSistemasPerfilExterno();

        return $cmb;
    }

    public function checkCredencials($dto)
    {
        if ($dto->getTxEmail()) {
            $optionsValidate = array(array('mx' => TRUE));
            if (!\Zend_Validate::is($dto->getTxEmail(), 'EmailAddress', $optionsValidate)) {
                return \Core_Registry::getMessage()->_('MN095');
            }
        }

        return !$this->_getRepository()->checkCredencials($dto);
    }

    /**
     * Envio de email para recuperação de senha
     * @param \Doctrine\ORM\EntityRepository $entityUser
     */
    protected function _sendMailActivation($person)
    {
        $hash = $this->_generateHash($person['txSenha'], $person['sqUsuarioExterno']);

        $config = \Zend_Registry::getInstance()->get('configs');
        $urlSistema = rtrim($config['urlSica'], '/') . '/';

        $mail = new \Zend_Mail('UTF-8');

        $html = new \Zend_View();
        $html->setScriptPath(APPLICATION_PATH . '/modules/principal/views/scripts/mail/');

        $html->assign('name', $person['noUsuarioExterno']);
        $html->assign('urlSica', $urlSistema . 'usuario-externo/confirm-mail-activation/token/' . $hash);

        $bodyText = $html->render('mail-usuario-externo-activation.phtml');

        $mail->addTo($person['txEmail'])
                ->setSubject('[ICMBio] Ativação de cadastro de usuário externo')
                ->setBodyHtml($bodyText)
                ->send();
    }

    public function preUpdate($entity, $dto = NULL)
    {
        $data = $entity->toArray();
        $data['txSenha'] = NULL;

        $this->getEntityManager()
                ->getUnitOfWork()
                ->registerManaged($entity->getSqUsuarioPessoaFisica(), array('sqUsuarioExterno' => 0), array());

        $this->getEntityManager()
                ->getUnitOfWork()
                ->registerManaged($entity->getSqUsuarioPessoaJuridica(), array('sqUsuarioExterno' => 0), array());
    }

    public function preInsert($entity, $dto = NULL)
    {
        if( !((array)\Core_Integration_Sica_User::get()) ) {
            $systemEntity = $this->getEntityManager()->getRepository( 'app:Sistema' )->findOneBySqSistema( \Core_Configuration::getSicaeSqSistema() );
            \Core_Integration_Sica_User::set( (object) array(
                'sqUsuario' => 0,
                'sqSistema' => $systemEntity->getSqSistema(),
                'inPerfilExterno' => true
            ));
            \Core_Integration_Sica_User::setSystems( array(
                $systemEntity->getSqSistema() => array('sgSistema' => $systemEntity->getSgSistema())
            ));
        }

        $entity->setTxSenha(md5($entity->getTxSenha()));
        $entity->setStRegistroAtivo(\Core_Configuration::getSicaeUsuarioExtStRegistroPendAtivacao());
        // Para tratamento do Usuário Externo do CANIE -- INICIO
        ########
        $repository = 'app:Pessoa';
        $data       = array('noPessoa' => $entity->getNoUsuarioExterno(), 
                            'stRegistroAtivo' => TRUE, 
                            'stUsuarioExterno' => TRUE, 
                            'sqTipoPessoa' => 1
                           );

        $method     = 'libCorpSavePessoa';
        $sqPessoa   = $this->getServiceLocator()->getService('Pessoa')->saveLibCorp($repository, $method, $data);
        $entity->setSqUsuarioExterno($sqPessoa);
        ########
        // Para tratamento do Usuário Externo do CANIE -- FIM
    }

    public function postInsert($entity, $dto = NULL)
    {
        $arrMail = array(
            'sqUsuarioExterno' => $entity->getSqUsuarioExterno(),
            'noUsuarioExterno' => $entity->getNoUsuarioExterno(),
            'txEmail' => $entity->getTxEmail(),
            'txSenha' => $entity->getTxSenha()
        );

        $this->_sendMailActivation($arrMail);
    }

    public function postSave($entity, $dto = NULL)
    {
        $this->saveDadosComplementares($entity, $dto);
        $this->saveSistemas($entity, $dto);
    }

    public function confirmMailActivation($params)
    {
        $entity = $this->find($this->validateToken($params));
        $entity->setStRegistroAtivo(\Core_Configuration::getSicaeUsuarioExtStRegistroAtivo());

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush($entity);
    }

    public function saveDadosComplementares($entity, $dto)
    {
        $data = $dto->getComplementar();
        $entityCmp = $this->_getRepository('app:UsuarioExternoDadoComplementar')->find($entity->getSqUsuarioExterno());

        foreach ($data as $key => $value) {
            if (strstr($key, 'sq') && $value) {
                $entityName = 'app:' . ucfirst(str_replace('sq', '', $key));
                $data[$key] = $this->_getRepository($entityName)->find($value);
            }

            $data[$key] = \Zend_Filter::filterStatic($data[$key], 'Null', array(array('type' => 'string')));
        }

        if ($entityCmp) {
            $entityCmp->fromArray($data);
        } else {
            $entityCmp = \Core_Dto::factoryFromData($dto->getComplementar(), 'entity', $this->_entityComp)->getEntity();
            $entityCmp->setSqPais($data['sqPais']);
            $entityCmp->setSqEstado($data['sqEstado']);
            $entityCmp->setSqMunicipio($data['sqMunicipio']);
        }

        $telefoneFixo = explode(' ', $entityCmp->getNuTelefoneFixo());
        $telefoneCelular = explode(' ', $entityCmp->getNuTelefoneCelular());

        $entityCmp->setNuDddTelefoneCelular(\Zend_Filter::filterStatic(current($telefoneCelular), 'Digits'));
        $entityCmp->setNuDddTelefoneFixo(\Zend_Filter::filterStatic(current($telefoneFixo), 'Digits'));

        $entityCmp->setNuTelefoneCelular(\Zend_Filter::filterStatic(end($telefoneCelular), 'Digits'));
        $entityCmp->setNuTelefoneFixo(\Zend_Filter::filterStatic(end($telefoneFixo), 'Digits'));

        $entityCmp->setCoCep(\Zend_Filter::filterStatic($entityCmp->getCoCep(), 'Digits'));
        $entityCmp->setSqUsuarioExterno($entity);

        $this->getEntityManager()->persist($entityCmp);
        $this->getEntityManager()->flush($entityCmp);
    }

    public function saveSistemas($entity, $dto)
    {
        $data = $dto->getSistemas();
        $this->_getRepository()->deletePerfilPadraoUsuario($entity->getSqUsuarioExterno());

        $entityManager = $this->getEntityManager();

        foreach ($data['sqPerfil'] as $sqPerfil) {
            $entityPerfil = $entityManager->getPartialReference('app:Perfil', $sqPerfil);

            $entityUsuarioPerfilExterno = new \Sica\Model\Entity\UsuarioExternoPerfil();
            $entityUsuarioPerfilExterno->setSqUsuarioExterno($entity);
            $entityUsuarioPerfilExterno->setSqPerfil($entityPerfil);
            $entityManager->persist($entityUsuarioPerfilExterno);
        }
    }

    public function listGridUsersExternals(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->searchPageDto('listGridUsersExternals', $dto);
    }

    public function listGridUsersExternalsCount(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->listGridUsersExternalsCount($dto);
    }

    public function findProfilesBind($identifier)
    {
        return $this->_getRepository('app:Usuario')->findProfilesBind($identifier, TRUE);
    }

    public function deleteProfile(\Core_Dto_Mapping $mapping)
    {
        $criteria = array(
            'sqUsuarioExterno' => $mapping->getUsuario(),
            'sqPerfil' => $mapping->getPerfil()
        );
        $perfis = $this->_getRepository('app:UsuarioExternoPerfil')->findBy($criteria);
        foreach ($perfis as $perfil ){
            $this->getEntityManager()->remove($perfil);
            $this->getEntityManager()->flush();
        }
        if (!$this->getServiceLocator()
                        ->getService('UsuarioExternoPerfil')
                        ->findBy(array('sqUsuarioExterno' => $mapping->getUsuario()))) {

            $entity = $this->find($mapping->getUsuario());
            $entity->setStRegistroAtivo(\Core_Configuration::getSicaeUsuarioExtStRegistroInativo());

            $this->getEntityManager()->persist($entity);
            $this->getEntityManager()->flush($entity);
        }
    }

    public function saveBindProfile(\Core_Dto_Mapping $mapping, array $perfis)
    {
        $this->getServiceLocator()->getService('UsuarioExternoPerfil')
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
        $mail->setBodyHtml('usuario-externo/mail-liberacao.phtml');
        $mail->send();

        return TRUE;
    }

    /**
     * @param integer $identifier
     * @return array
     */
    public function findDataViewUserExternal($identifier)
    {
        return $this->_getRepository()->findDataViewUserExternal($identifier);
    }

    /**
     * @param integer $sqUsuarioExterno
     */
    public function resendMail($sqUsuarioExterno)
    {
        try {
            $entity = $this->find($sqUsuarioExterno);

            $arrMail = array(
                'sqUsuarioExterno' => $entity->getSqUsuarioExterno(),
                'noUsuarioExterno' => $entity->getNoUsuarioExterno(),
                'txEmail' => $entity->getTxEmail(),
                'txSenha' => $entity->getTxSenha()
            );

            $this->_sendMailActivation($arrMail);
        } catch (\Exception $exc) {
            $message = sprintf( '[SICA-e] in %s, %s: "%s"', 
                                __METHOD__,
                                get_class($exc),
                                $exc->getMessage() );
            error_log( $message );
            throw new \Core_Exception('MN172');
        }
    }

}