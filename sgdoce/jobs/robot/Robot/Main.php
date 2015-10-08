<?php

/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

namespace Robot;

/**
 * Classe principal do robô
 *
 * @package     Robot
 * @name        Main
 * @version     1.0.0
 *
 * @author Sósthenes Neto <sosthenes.neto.terceirizado@icmbio.gov.br>
 */
class Main
{

    /**
     * @var \Robot\ArgumentsManipulator
     */
    private $_args = null;

    /**
     * @var \Exception
     */
    private $_expection = null;

    /**
     * @var \Robot\Main
     */
    private static $_instance = null;

    /**
     * @var string
     */
    private $_shutdownInfo = '';

    /**
     * Determina o step corrente para gerar o aquivo de log correspondente
     *
     * @var string
     */
    private $_stepCurrent = '';

    /**
     * @access public
     * @return \Robot\Main
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new Main();
        }

        return self::$_instance;
    }

    /**
     * @param \Exception $exception
     * @return void
     */
    public function setExpection(\Exception $exception)
    {
        $this->_expection = $exception;
    }

    /**
     * @return \Robot\Main Provides the fluent interface
     */
    public function argsManipulator()
    {
        //argumentos passados por linha de comando...
        $this->_args = new ArgumentsManipulator();

        return $this;
    }

    /**
     * @return \Robot\Main Provides the fluent interface
     */
    public function bootstrap()
    {
        if ($this->_args instanceof ArgumentsManipulator) {
            $this->_stepCurrent = implode('-', $this->_args->getSteps());
        }

        Debug::log(PHP_EOL, Debug::LOG, $this->_stepCurrent);
        Debug::log( 'Startup...', Debug::LOG, $this->_stepCurrent );

        $this->_initLock();

        return $this;
    }

    /**
     * @access public
     * @return void
     */
    public function run()
    {
        Debug::log( __METHOD__, Debug::LOG, $this->_stepCurrent );
        foreach ($this->_args->getSteps() as $step) {
            $this->_writeLock( $this->_lockInformation( 'RUNNING_' . strtoupper( $step ) ) );
            $stepInstance = StepFactory::initialize( $step );
            $stepInstance->exec();
        }
    }

    /**
     * @param \Exception $exception
     * @return void
     */
    public function shutdown()
    {
        Debug::log( __METHOD__, Debug::LOG, $this->_stepCurrent );

        $exception = $this->_expection;
        $error = error_get_last();

        if ($exception instanceof \Exception) {
            $this->_dirtyExit( $exception );
//            if (empty($this->_shutdownInfo)) {
                $this->_cleanExit();
//            }
            exit( PHP_EOL .
                $exception->getMessage() .
                PHP_EOL .
                print_r( $exception->getTraceAsString(), true ) .
                PHP_EOL );
        } else {
            if ((is_null( $error )) &&
                (($error['type'] === E_ERROR) || ($error['type'] === E_USER_ERROR))) {
                $message = "Msg: {$error['message']}" . PHP_EOL . "File: {$error['file']}:{$error['line']}";
                $this->_dirtyExit( $message );
//                if (empty($this->_shutdownInfo)) {
                    $this->_cleanExit();
//                }
                exit( PHP_EOL . $message . PHP_EOL );
            }
        }

        if (empty($this->_shutdownInfo)) {
            $this->_cleanExit();
        }
        exit($this->_shutdownInfo);
    }

    /**
     * @return void
     */
    public function __clone()
    {
        trigger_error( __CLASS__ . ': Não pode clonar essa classe!', E_USER_ERROR );
    }

    /**
     * @access private
     */
    private function __construct()
    {

    }

    /**
     * @param string|int $info Rotulo da informação ou valor
     * @return string|int
     * @throws \Core\Exception
     */
    private function _lockInformation($info, $data = '')
    {
        $locks = array(
            'STARTUP',
            'RECOVERED',
            'ERROR',
            'EXCEPTION'
        );
        if ($this->_args instanceof ArgumentsManipulator) {
            foreach ($this->_args->getSteps() as $step) {
                array_push( $locks, 'RUNNING_' . strtoupper( $step ) );
            }
        }

        $data = trim( $data );
        if (in_array( $info, $locks )) {
            if (empty( $data )) {
                return $info;
            }
            return $info . PHP_EOL . $data;
        }

        trigger_error( "Tipo de informação de lock '{$info}' inválida.", E_USER_ERROR );
    }

    /**
     * @return string
     */
    private function _getLockFile()
    {
        $additional = '';
        if ($this->_args instanceof ArgumentsManipulator) {
            $this->_stepCurrent = implode('-', $this->_args->getSteps());
            $additional = '_' . implode('-', $this->_args->getSteps());
        }
        return APPLICATION_PATH .'/../data/.~robot' . $additional . '.lock';
    }

    /**
     * @return void
     * @todo ver rotina de reparação...
     */
    private function _repairLock()
    {
        $lockInfo = file_get_contents( $this->_getLockFile() );

        Debug::log( sprintf( "LOCK INFO: %s", $lockInfo ), Debug::LOG, $this->_stepCurrent );

        $this->_writeLock( $this->_lockInformation( 'RECOVERED' ) );
    }

    /**
     * @return void
     */
    private function _writeLock($data)
    {
        $write = sprintf( '%s%sem: %s%s', $data, PHP_EOL, date( 'd/m/Y H:i:s' ), PHP_EOL );
        file_put_contents( $this->_getLockFile(), $write );
    }

    /**
     * @return void
     */
    private function _clearLock()
    {
        if (file_exists( $this->_getLockFile() )) {
            unlink( $this->_getLockFile() );
        }
    }

    /**
     * Verificar e executar os procedimentos do lock
     *
     * @return void
     */
    private function _initLock()
    {
        Debug::log( __METHOD__, Debug::LOG, $this->_stepCurrent );

        if (file_exists( $this->_getLockFile() )) {
            $this->_shutdownInfo = file_get_contents( $this->_getLockFile() );
            if (strpos($this->_shutdownInfo, 'RUNNING') === 0) {
                exit( $this->_shutdownInfo );
            }
            $this->_repairLock();
        } else {
            $this->_writeLock( $this->_lockInformation( 'STARTUP' ) );
        }
    }

    /**
     * @param \Exception|string $fail
     * @return void
     */
    private function _dirtyExit($fail)
    {
        Debug::log( 'Ops, um erro ocorreu :-(', Debug::ERROR, $this->_stepCurrent );

        if ($fail instanceof \Exception) {
            Debug::log( $fail->getMessage(), Debug::ERROR, $this->_stepCurrent );
            $this->_writeLock( $this->_lockInformation( 'EXCEPTION', $fail->getMessage() ) );
        } else {
            Debug::log( (string) $fail, Debug::ERROR, $this->_stepCurrent );
            $this->_writeLock( $this->_lockInformation( 'ERROR', (string) $fail ) );
        }
    }

    /**
     * @return void
     */
    private function _cleanExit()
    {
        Debug::log( 'Zero Kill', Debug::LOG, $this->_stepCurrent);

        $this->_clearLock();
    }

}
