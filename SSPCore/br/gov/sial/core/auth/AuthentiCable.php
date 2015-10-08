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

/**
 * interface de autenticacao
 *
 * @package br.gov.sial.core
 * @subpackage auth
 * @name AuthentiCable
 * @author J. Augusto <augustowebd@gmail.com>
 * */
interface AuthentiCable
{
    /**
     * Sucesso na  autenticação.
     *
     * @var integer
     * */
    const SUCCESS = 0;

    /**
     * Falha na autenticação.
     *
     * @var integer
     * */
    const FAILURE = 1;

    /**
     * Usuário ou senha inválida.
     *
     * @var integer
     * */
    const FAILURE_IDENTITY_NOT_FOUND = 2;

    /**
     * Encontrado mais de uma ocorrência para as credenciais informadas.
     *
     * @var integer
     * */
    const FAILURE_IDENTITY_AMBIGUOUS = 3;

    /**
     * Senha inválida.
     *
     * @var integer
     * */
    const FAILURE_CREDENTIAL_INVALID = 4;

    /**
     * Erro desconhecido.
     *
     * @var integer
     * */
    const FAILURE_UNCATEGORIZED  = 5;

    /**
     * Método responsável por efetuar autenticação.
     * */
    public function authenticate ();

    /**
     * Verifica se a autenticação foi realizada com sucesso.
     *
     * @return boolean
     * */
    public function isValid ();

    /**
     * Retorna uma constante de AuthentiCable para determinar se ocorreu falha
     * ou sucesso.
     *
     * @return integer
     * */
    public function getCode ();

    /**
     * Retorna a identidade utilizada na tentativa de autenticação.
     *
     * @return string
     * */
    public function getIdenttity ();

    /**
     * Retorna um array de mensagens sobre uma falha de autenticação.
     *
     * @return string[]
     * */
    public function getMessages ();
}