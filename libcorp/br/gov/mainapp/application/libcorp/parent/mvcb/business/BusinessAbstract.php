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
namespace br\gov\mainapp\application\libcorp\parent\mvcb\business;
use br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\exception\ClassMethodNotFoundxception,
    br\gov\mainapp\library\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name BusinessAbstract
  * @package br.gov.mainapp.application.libcorp.parent.mvcb
  * @subpackage business
  * @author Fábio Lima <fabioolima@gmail.com>
  * @version $Id$
  * */
abstract class BusinessAbstract extends ParentBusiness
{
    /**
     * @var string
     * */
    const GET_DATA_ERROR_MESSAGE = 'Não foi possível obter os dados solicitados';

    /**
     * Espelha metodos publicos (finds) para suportar pesquisa para retornar DataViewObject
     *
     * <p>
     *   este comportamento disponibiliza para cada funcionalidade publica outra de mesmo nome acrescendo
     *   o sufixo 'AsDataViewObject'.
     *
     *   exemplo:
     *
     *   <ul>
     *     <li>findByMunicipio | findByMunicipioAsDataViewObject</li>
     *     <li>findByEstado    | findByEstadoAsDataViewObject</li>
     *   </ul>
     * </p>
     *
     * <p>
     *     <b>nota</b>: O paramentro de filtro devera ser o mesmo, tando para funcionalidade principal como espelho
     * </p>
     * @throws ClassMethodNotFoundxception
     * */
    public function __call($method, $arguments)
    {
        # verifica se existe um metodo base para espelha-lo
        $methodName = current(preg_split('/AsDataViewObject$/', $method));
        ClassMethodNotFoundxception::throwsExceptionIfParamIsNull(
            method_exists($this, $methodName), __CLASS__ . "::{$method} indisponível"
        );
        $methodName = "_{$methodName}";
        return $this->$methodName(current($arguments))->getAllDataViewObject();
    }

    /**
     * Varre o VO buscando as dependencias de outras tabelas
     * @param ValueObjectAbstract $valueObject
     * @return array[]
     */
    public function keepUpdateData (ValueObjectAbstract $valueObject)
    {
        $tmpData = array_filter($valueObject->toArray());

        foreach ($tmpData as $value => $key) {
            if (is_array($key)) {
                if (empty($tmpData[$value][$value])) {
                    unset ($tmpData[$value]);
                } else {
                    $tmpData[$value] = $tmpData[$value][$value];
                }
            }
        }

        return $tmpData;
    }
}