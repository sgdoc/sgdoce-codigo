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
 * Manipulador de argumentos
 *
 * @package     Robot
 * @name        ArgumentsManipulator
 * @version     1.0.0
 *
 * @author Sósthenes Neto <sosthenes.neto.terceirizado@icmbio.gov.br>
 */
class ArgumentsManipulator
{

    /**
     * @var \Zend_Console_Getopt
     */
    private $_opts = null;

    /**
     * @var array
     */
    private $_stepsOptionsArgs = array();

    /**
     * Ambientes do SARR
     *
     * @var array
     */
    private $_argsDefault = array(
        'production',
        'staging',
        'development',
        'testing',
        'homologacao',
        'tcti'
    );

    /**
     * Class constuctor
     */
    public function __construct()
    {
        //@todo colocar as etapas vindas do array
        $this->_opts = new \Zend_Console_Getopt( array(
            'help|h' => 'Mostra as informações de uso.',
            'quiet|q' => 'Não exibe as mensagens na saida padrão e no log e eventos da aplicação.',
            'env|e=w' => 'Parâmetro obrigatório. Ambiente da aplicação. As opções são: "production", "staging" ou "development".',
            'step|s=s' => "Etapas internas da aplicação. As opções são: \"{$this->_getStepsOptions()}\" or \"all\". Pode-se passar mais de uma separadas por virgula. Por padrão é executada todas (\"all\")."
            ) );

        $this->_stepsOptionsArgs = $this->_searchForSteps();
        if (count( $this->_stepsOptionsArgs ) === 0) {
            throw new \Exception( 'No steps found! Go to Step folder and include one or more PHP classes (StepInterface).' );
        }
        $this->_needsHelp();
        $this->_makesSilence();
        $this->_setApplicationEnvironment();
        $this->_steps();
    }

    /**
     * @return array
     */
    public function getSteps()
    {
        $stepArg = $this->_getArg( 'step' );
        if (is_null( $stepArg ) || $stepArg === 'all') {
            return $this->_stepsOptionsArgs;
        } else {
            $steps = array_map( 'trim', explode( ',', $stepArg ) );
            return $steps;
        }
    }

    private function _searchForSteps()
    {
        $steps = array();
//        $appDir = str_replace( basename( $_SERVER["PHP_SELF"] ), '', realpath( $_SERVER["PHP_SELF"] ) );
//        $stepDirectory = new \DirectoryIterator( $appDir . DIRECTORY_SEPARATOR . 'Robot' . DIRECTORY_SEPARATOR . 'Step' );
        $stepDirectory = new \DirectoryIterator('Robot' . DIRECTORY_SEPARATOR . 'Step' );
        foreach ($stepDirectory as $file) {
            if (false === in_array( $file->getFilename(), array('.', '..', 'Factory.php', 'StepInterface.php') )) {
                if ($file->getExtension() === 'php') {
                    array_push( $steps, str_replace( '.php', '', $file->getFilename() ) );
                }
            }
        }
        return $steps;
    }

    /**
     * @param string $option
     * @return string
     */
    private function _getArg($option)
    {
        return $this->_opts->getOption( $option );
    }

    /**
     * @return void
     */
    private function _needsHelp()
    {
        if ($this->_getArg( 'help' )) {
            exit( $this->_opts->getUsageMessage() );
        }
    }

    /**
     * @return void
     */
    private function _makesSilence()
    {
        $enabledDebugNoise = !$this->_getArg( 'quiet' );
        Debug::initializeDebugNoise( $enabledDebugNoise );
    }

    /**
     * @return string
     */
    private function _getApplicationEnvironment()
    {
        $env = $this->_getArg( 'env' );
        if (false === in_array( $env, $this->_argsDefault )) {
            $usage = $this->_opts->getUsageMessage();
            $message = 'Option "env" requires only this parameters: ';
            foreach ($this->_argsDefault as $pos => $argDefault) {
                if (($pos + 1) === count( $this->_argsDefault )) {
                    $message .= ' or ';
                } else {
                    if ($pos > 0) {
                        $message .= ', ';
                    }
                }
                $message .= $argDefault;
            }
            $message .= " but was given \"{$env}\".";
            throw new \Zend_Console_Getopt_Exception( $message, $usage );
        }
        return $env;
    }

    /**
     * @return void
     */
    private function _setApplicationEnvironment()
    {
        defined( "APPLICATION_ENV" ) || define( "APPLICATION_ENV", $this->_getApplicationEnvironment() );
    }

    /**
     * @return void
     */
    private function _steps()
    {
        $steps = $this->getSteps();

        foreach ($steps as $step) {
            if (false === in_array( $step, $this->_stepsOptionsArgs )) {
                $usage = $this->_opts->getUsageMessage();
                $message = 'Option "step" requires only this parameters: ' .
                    "\"{$this->_getStepsOptions()}\" or \"all\" " .
                    "but was given \"{$step}\"" .
                    (count( $steps ) > 1 ?
                        " from \"{$this->_getArg( 'step' )}.\"" : '.');
                throw new \Zend_Console_Getopt_Exception( $message, $usage );
            }
        }
    }

    /**
     * @return string
     */
    private function _getStepsOptions()
    {
        return implode( '", "', $this->_stepsOptionsArgs );
    }

}
