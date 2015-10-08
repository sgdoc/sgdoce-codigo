<?php

class Services
{

    /**
     * Verifica autenticação do usuário interno retorna 'true' caso este possua acesso
     *
     * @param string $credential
     * @param string $password
     * @return boolean
     */
    public function verificaAutenticacaoUsuarioInterno($credential, $password)
    {
        $repository = $this->getEntityManager()
                ->getRepository('app:Usuario');

        return $this->authenticate($repository, $credential, $password);
    }

    /**
     * Verifica autenticação do usuário externo retorna 'true' caso este possua acesso
     *
     * @param string $credential
     * @param string $password
     * @return boolean
     */
    public function verificaAutenticacaoUsuarioExterno($credential, $password)
    {
        $repository = $this->getEntityManager()
                ->getRepository('app:UsuarioExterno');

        return $this->authenticate($repository, $credential, $password);
    }

    private function authenticate($repository, $credential, $password)
    {
        $adapter = new Sica_Auth_Adapter($repository, $credential, $password);
        $result = Zend_Auth::getInstance()
                ->setStorage(new Zend_Auth_Storage_NonPersistent())
                ->authenticate($adapter);

        return $result->isValid();
    }

    private function getEntityManager()
    {
        return Zend_Registry::get('doctrine')->getEntityManager();
    }

}
