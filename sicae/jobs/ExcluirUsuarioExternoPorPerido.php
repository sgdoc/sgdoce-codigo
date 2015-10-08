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
define('APPLICATION_ENV', $argv[1]);

chdir(dirname(__DIR__));
require_once 'init_autoloader.php';
require_once 'init_bootstrap.php';

$application->bootstrap('doctrine');

class ExcluirUsuarioExternoPorPerido
{
    const ST_REGISTRO_PENDENTE = 2;
    const NU_DIAS = 7;

    /** @var Doctrine\ORM\EntityManager */
    protected $_em;

    public function __construct()
    {
        $this->_em = \Zend_Registry::get('doctrine')->getEntityManager();
        $this->execute();
    }

    /**
     * Realiza verificacao dos usuario com status pendente de ativacao
     */
    public function getUsuariosExterno()
    {
        $sql = 'SELECT u.sq_usuario_externo FROM sicae.usuario_externo u
                WHERE u.st_registro_ativo = ' . self::ST_REGISTRO_PENDENTE .
                'AND  (DATE(NOW()) - DATE(u.dt_cadastro)) >= ' . self::NU_DIAS;

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_usuario_externo', 'sqUsuarioExterno', 'integer');

        return $this->_em->createNativeQuery($sql, $rsm)->getResult();
    }

    /**
     * Realiza envio de email para fases antigas
     */
    public function execute()
    {
        foreach ($this->getUsuariosExterno() as $value) {
            $sqUsuario = $value['sqUsuarioExterno'];

            # excluir dados complementares
            $sql = "DELETE FROM sicae.usuario_externo_dado_complementar u WHERE u.sq_usuario_externo = {$sqUsuario}";
            $this->_em->getConnection()->exec($sql);

            # excluir pessoa fisica
            $sql = "DELETE FROM sicae.usuario_pessoa_fisica u WHERE u.sq_usuario_externo = {$sqUsuario}";
            $this->_em->getConnection()->exec($sql);

            # excluir pessoa juridica
            $sql = "DELETE FROM sicae.usuario_pessoa_juridica u WHERE u.sq_usuario_externo = {$sqUsuario}";
            $this->_em->getConnection()->exec($sql);

            # excluir usuario perfil externo
            $sql = "DELETE FROM sicae.usuario_externo_perfil u WHERE u.sq_usuario_externo = {$sqUsuario}";
            $this->_em->getConnection()->exec($sql);

            # excluir usuario externo
            $sql = "DELETE FROM sicae.usuario_externo u WHERE u.sq_usuario_externo = {$sqUsuario}";
            $this->_em->getConnection()->exec($sql);
        }
    }

}

new ExcluirUsuarioExternoPorPerido();