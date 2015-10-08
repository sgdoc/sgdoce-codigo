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

namespace Robot\Step;

use \Robot\Debug;

/**
 * Classe da rotina de merge de PDFs
 *
 * @package     Robot
 * @category    Step
 * @name        MergePdf
 * @version     1.0.0
 *
 * @author Juliano Buzanello <juliano.buzanello.terceirizado@icmbio.gov.br>
 */
class MergePdf implements \Robot\StepInterface
{
    /**
     * @var Artefato\Service\ArtefatoImagem
     */
    private $_service;

    /**
     * @var int
     */
    public function __construct()
    {
        $this->_service = \Zend_Registry::get( 'serviceLocator' )->getService( 'ArtefatoImagem' );
    }

    /**
     * @see \Robot\StepInterface
     * @return void
     */
    public function exec()
    {
        Debug::log( sprintf( '%s: %s', __METHOD__,
                'Executar merge das imagens solicitadas para download...' ),
            Debug::INFO, Debug::STEP_MERGE );

        Debug::log( sprintf('\Zend_Version::VERSION: %s', \Zend_Version::VERSION), Debug::WARN, Debug::STEP_MERGE );

        try {
            $this->_service->processMerge();
        } catch (\Exception $e) {
            $this->_fail( $e );
        }
    }

    /**
     * @param \Exception $exception
     */
    private function _fail(\Exception $exception)
    {
        Debug::log( 'fail :(' );
        Debug::log( $exception->getTraceAsString(),
            sprintf( "%s::getMessage(): %s", get_class( $exception ), $exception->getMessage() ) );
    }
}
