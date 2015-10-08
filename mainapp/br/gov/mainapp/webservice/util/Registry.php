<?php
/*
 * Copyright 2011 ICMBio
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
namespace br\gov\mainapp\webservice\util;
use br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness,
    br\gov\sial\core\util\Registry as ParentRegistry;

class Registry extends ParentBusiness
{
    public function setCredential($userCredential = NULL)
    {

        $this->_validateCredential($userCredential);

        $backtrace = debug_backtrace();
        ParentRegistry::get('bootstrap')
                    ->request()
                    ->setAction($backtrace[1]['function'])
                    ->setModule("wsdl")
                    ->setFuncionality('libcorp');

        $_SESSION['USER'] = (object)$userCredential;
    }

    private function _validateCredential($userCredential = NULL)
    {
        BusinessException::throwsExceptionIfParamIsNull(!empty($userCredential), "As credencias são de preenchimento obrigatório.");

        BusinessException::throwsExceptionIfParamIsNull(isset($userCredential['sqUsuario']) ||  !empty($userCredential['sqUsuario']), "O campo sqUsuário é de preenchimento obrigatório.");

        BusinessException::throwsExceptionIfParamIsNull(isset($userCredential['sgSistema']) || !empty($userCredential['sgSistema']), "O campo sgSistema é de preenchimento obrigatório.");

        BusinessException::throwsExceptionIfParamIsNull(isset($userCredential['inPerfilExterno']), "O campo inPerfilExterno é de preenchimento obrigatório.");

    }

    /**
     * @return string
     * */
    public function arrayToXml ($param)
    {
        $data   = $param;
        $output = NULL;

        foreach ((array) $data as $node => $value) {
            $output .= sprintf('<%1$s>%2$s</%1$s>', $node, is_array($value) ? $this->arrayToXml($value, TRUE) : $value);
        }
        return $output;
    }
}