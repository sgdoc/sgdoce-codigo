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
namespace br\gov\sial\core\util;
use br\gov\sial\core\SIALAbstract,
    br\gov\sial\core\BootstrapAbstract,
    br\gov\sial\core\exception\SIALException,
    br\gov\sial\core\exception\IllegalArgumentException;

/**
 * SIAL
 *
 *  (1) Descricao: Funcionamento das constantes no SIAL
 *  - Cada sistema tera um diretorio nomeado de 'constant' que conterá um ou mais arquivos sendo o arquivo principal
 *  nomeando com o nome do diretorio base, exemplo, se o diretorio base em questao for o 'tools' então teremos:
 *  '(1)tools/(2)constant/(3)tools.php' onde 1 - nome do diretori base; 2 - nome fixo da pasta das constantes;
 *  3 - nome do arquivo que conterá as contantes gerais do sistema.
 *  Para minimizar o uso de recurso (memória), o SIAL sempre irá buscar por arquivos de constantes de mesmo nome do
 *  módulo de execucao.
 *
 *  (2) Exemplo: Tomando como exemplo o sistema 'tools' que é composto de:
 *     - column
 *     - database
 *     - entity
 *     - schema
 *     - vom
 *     - wizard
 *     e suponhamos que o modulo 'wizard' foi carregado, o SIAL tentara' carregar o arquivo tools/constant/tools.php e
 *     tools/constant/wizard.php por ter sido este o modulo carregado, isso permite um francionamento de constantes
 *     carregando apenas o necessário por vez.
 *
 *  (3) Regra: Para definicao do nome da constante:
 *     - 1: 'SIAL'
 *     - 2: SISTEMA
 *     - 3: SUBSISTEMA
 *     - 4: FUNCIONALIDADE (se houver)
 *     - 5: CONSTANTE_NAME
 *
 *  (4) Definição:
 *          1 define como constante do SIAL;
 *          2 nome do sistema que deu origem a constante;
 *          3 nome do subsistema;
 *          4 nome da funcionalide;
 *          5 nome da constante.
 *
 * @package br.gov.sial.core
 * @subpackage util
 * @name ConstantHandler
 * @author J. Augusto <augustowebd@gmail.com>
 * */
class ConstantHandler extends SIALAbstract
{
    /**
     * se definido como TRUE sera lancando uma ConstantHalderException sempre que for invocado uma constante que nao
     * tenha sido carregada
     * 
     * @var boolean
     * */
    public static $throwsExceptionIfConstantNotExists = FALSE;

    /**
     * chache do nome do sistema principal, o nome dado para o agrupador logo abaixo da pasta br\gov\icmbio
     * o qual armazena os demais sistemas (SIAL, no caso do icmbio)
     *
     * @var string|null
     * */
    private $_mainSystemCacheName = NULL;

    /**
     * @var br\gov\sial\core\BootstrapAbstract
     * */
    private $_bootstrap;

    /**
     * define a sequencia com que a constante sera pesquisada: 1- mdl: modulo; 2 - aplicacao; 3 - sistema geral
     *
     * @var string[]
     * */
    private $_sequenceSearch = array('mdl', 'app', 'sys');

    /**
     * este monitorador de quais arquivos foram carregados serve apenas para
     * questoes de bugacao
     *
     * @var string[]
     * */
    private $_stackLoadedConstantFile = array();

    /**
     * @param BootstrapAbstract
     * */
    public function __construct (BootstrapAbstract $bootstrap)
    {
        $this->_bootstrap = $bootstrap;
        $this->_mainSystemCacheName = strtoupper($this->_bootstrap->config('app.mainsystem'));
    }

    /**
     * recupera o valor de uma constant, se o segundo param for informado sera assumido que a chamado foi realizando
     * informadando apenas o nome da consntant (5) deixando a cargo da classe montar o nome completo.
     *
     * @param string $name
     * @param boolean $shortname
     * @return mixed
     * */
    public function get ($name, $shortname = TRUE)
    {
        if (FALSE == $shortname) {
            return $this->getByName($name);
        }

        foreach ($this->_sequenceSearch as $section) {
           $constantValue = $this->getByName(self::_buildConstantName($name, $section));
           if (TRUE == $constantValue) {
               return $constantValue;
           }
        }
    }

    /**
     * monta o nome da constante, o segundo argumento define (sys) que sera montado um nome para uma constante de
     * systema, (app) subsistema e(mdl) funcionalidade
     *
     * @param string $name
     * @param string $section
     * @return string
     * */
    private function _buildConstantName ($name, $section)
    {
        # @implementar verificacao da existencia do metodo
        $nameBuilder = '_buildConstant' . ucfirst($section);
       return self::$nameBuilder($name) . '_' . strtoupper($name);
    }

    /**
     * define a constante do sistema
     * 
     * @return string
     */
    private function _buildConstantSys ()
    {
        return sprintf('SIAL_%s', $this->_mainSystemCacheName);
    }

    /**
     * define a constante da aplicação
     * 
     * @return string
     */
    private function _buildConstantApp ()
    {
        return sprintf('%s_%s', $this->_buildConstantSys(), strtoupper($this->_bootstrap->getModule()));
    }

    /**
     * define a constante da funcionalidade
     * 
     * @param string $name
     * @return string
     */
    private function _buildConstantMdl ($name)
    {
        return sprintf('%s_%s', $this->_buildConstantApp(), strtoupper($this->_bootstrap->getFuncionality()));
    }

    /**
     * retorna o valor da constante informando seu nome completo ou NULL se a constante nao tiver sido carregada
     * posteriormente
     *
     * @param string $fullname
     * @return string|null
     * */
    public function getByName ($fullname)
    {
        if (defined($fullname)) {
            return constant($fullname);
        }
        return NULL;
    }

    /**
     * carrega arquivo de constant
     *
     * @param string $fileContants
     * @throws IllegalArgumentException
     * */
    public function load ($fileContants)
    {
        $message = "Arquivo de constante inexistente ({$fileContants})";
        IllegalArgumentException::throwsExceptionIfParamIsNull(is_file($fileContants), $message);
        // @codeCoverageIgnoreStart
        require_once $fileContants;
        // @codeCoverageIgnoreEnd
    }

    /**
     * carrega arquivo de constante se existir baseando-se na requisicao (bootstrap->request)
     *
     * @param string $fileContants
     * */
    public function loadIfExists ($fileContants)
    {
        try {
          $this->load($fileContants);
        } catch (IllegalArgumentException $iex) {
            /*
             * como o metodo load precisa necessariamente efetuar a verificacao da existencia do arquivo,
             * a este metodo cabe apenas o papal de verificar se uma exception foi lancada e suprimi-la
             * dando a impressao de que ele faz algo alem disso ;D
             * */
        }
    }

    /**
     * carrega as constante do sistema/subsistema/modulo informado deve ser informado o namespace do
     * sistema/subsistema/modulo onde sera encontrado a pasta com as constantes e nao o local exatato da pasta de
     * constantes.
     *
     * @param string $namespace
     * @param boolean $isModule
     * */
    public function loadModuleConstant ($namespace, $isModule = FALSE)
    {
        # motivo de nao colocar: end(explode(parent::NAMESPACE_SEPARATOR, $namespace));
        # eh que o php fica lancado 'Only variables should be passed by reference'
        $arr        = explode(parent::NAMESPACE_SEPARATOR, $namespace);
        $file       = end($arr);

        $namespace  = parent::realpathFromNamespace($namespace)
                    . DIRECTORY_SEPARATOR . 'constant'
                    . DIRECTORY_SEPARATOR . "{$file}.php"
                    ;

        if (TRUE == $isModule) {
            $namespace = implode(explode( DIRECTORY_SEPARATOR . $file, $namespace, 2));
        }
        $this->loadIfExists($namespace);
    }

    /**
     * autoload de arquivo de constantes
     * este metodo, com base na requisicao (bootstrap->request()), carrega o arquivo de constante do modulo ativado
     *
     * @throws br\gov\sial\core\exception\SIALException
     * */
    public function requestConstantAutoload ()
    {
        # recupera o namespace da app
        # observer que para o correto funcionamento eh necesasrio que esta propriedade
        # seja configurada em application/config/config.ini
        $namespace   = substr($this->_bootstrap->config('app.namespace'), 1);

        $message = 'É necessário informar o namespace da aplicação em config.ini::app.namespaces ';
        SIALException::throwsExceptionIfParamIsNull(!empty($namespace), $message);

        # constantes gerais de todos os sistemas que deve esta sob a pasta \br\gov\icmbio\[systemName]\application
        $loadConstants['sys'] = $namespace;

        # constantes geral do sistema carregado: \br\gov\icmbio\[systemName]\application\subsystem
        $loadConstants['app'] = $loadConstants['sys']
                              . self::NAMESPACE_SEPARATOR
                              . $this->_bootstrap->getModule();

        # constante do modulo do sistema selecionado \br\gov\icmbio\[systemName]\application\subsystem\module
        $loadConstants['mdl'] = $loadConstants['app']
                              . self::NAMESPACE_SEPARATOR
                              . $this->_bootstrap->getFuncionality();

        foreach ($loadConstants as $key => $value) {
            $assert = 'mdl' == $key;
            $this->loadModuleConstant($value, $assert);
        };
    }

    /**
     * fábrica de objetos
     * 
     * @return br\gov\sial\core\util\ConstantHandler
     * */
    public static function factory (BootstrapAbstract $bootstrap)
    {
        return new self($bootstrap);
    }
}