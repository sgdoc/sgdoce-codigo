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
namespace br\gov\sial\core\util\file;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\util\Registry,
    br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\mvcb\business\exception\BusinessException;

/**
 * SIAL
 *
 * Mordomo responsável por tratar arquivos
 *
 * @package br.gov.sial.core.util
 * @subpackage file
 * @name Folder
 * @author Fabio Lima <fabioolima@gmail.com>
 * */
class Folder extends SIALAbstract
{
    /**
     * @var br\gov\sial\core\util\file\Folder
     * */
    private static $_instance = NULL;

    /**
     * Permissao das pastas a serem criadas
     * @var string
     */
    private static $_permission;

    /**
     * Efeuta a checagem da estrutura de persistencia de arquivos
     * @param mixed $source
     * @return boolean
     * @todo Transformar esta entrada como generica para qualquer sistema independente do SISICMBI
     */
    public function createFolderStructure ($source)
    {
        self::$_permission = Registry::get('bootstrap')->config()->get('app.file.permission');

        try {
            $repository = dirname(constant('APPLICATION_PATH')) . DIRECTORY_SEPARATOR;

            #@todo lancar exeception se o path não for um diretorio - is_dir($repository)

            return self::createStructPersist($repository);

        } catch (\Exception $excp) {
            dump($excp ,1);
        }
    }

    /**
     * cria estrutura
     * @param string $path
     * @return boolean
     */
    private static function createStructTFile ($path)
    {
        return self::createStructTFileContent($path);
    }

    /**
     * cria estrutura
     * @param string $path
     * @return string
     */
    private static function createStructPersist ($path)
    {

        $year       = date('Y');
        $month      = date('m');
        $path      .= Registry::get('bootstrap')->config()->get('app.file.repository');
        $system     = Registry::get('bootstrap')->request()->getModule();
        $permission = octdec(self::$_permission);

        # obtenho a permissao do diretorio
        $repoSystem = $path . DIRECTORY_SEPARATOR . $system;
        # destino onde os arquivos serao salvos
        $repoCreate = $repoSystem . DIRECTORY_SEPARATOR . $year . DIRECTORY_SEPARATOR . $month;

        #@todo lancar exception se não poder efetuar a escrita is_writable
        $owner   = self::whoIsOwner($path);
        $permDir = self::pathPermissions($path);
        //dump("SEM PERMISSAO PARA ESCRITA, PERMISSAO NA PASTA É {$permDir} E O DONO É {$owner}" ,1);

        # listo os diretorios e procuro na lista de diretorios pelo sistema informado
        if(!array_search($system, scandir($path)) ) {
            # cria repositorio e sub-diretorios referente ao Mes
            mkdir($repoCreate, $permission, TRUE);
        } else {
            # a pasta do sistema existe, agora faz a valizadacao da estrutura
            if (!array_search($year, scandir($repoSystem))) {
                mkdir($repoCreate, $permission, TRUE);
            } else {
                # Ultima verificacao caso o sub referente ao mes nao foi criado
                $listDir = scandir($repoSystem . DIRECTORY_SEPARATOR . $year);
                if (!array_search($month, $listDir)) {
                    mkdir($repoCreate, $permission, TRUE);
                }
            }
        }

        return base64_encode($repoCreate);
    }

    /**
     * returna permissão
     * @return string
     */
    public function permission ()
    {
        return $this->_permission;
    }

    /**
     * Retornar o Owner do caminho informado
     * @param string $path
     * @return string
     */
    private static function whoIsOwner ($path)
    {
        $info = (object) array_filter(posix_getgrgid(fileowner($path)));
        return $info->name;
    }

    /**
     * Verifica quais são as permissões do arquivos
     * @param string $path
     * @return string
     */
    private static function pathPermissions ($path)
    {
        return substr(decoct(fileperms($path)) ,2);
    }

    /**
     * fábrica de objetos
     * @return \br\gov\sial\core\util\file\Folder
     * */
    public static function factory ()
    {
        if (NULL == self::$_instance) {
            self::$_instance = new self;
        }
        return self::$_instance;
    }
}