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
namespace br\gov\mainapp\application\libcorp\vinculoFuncional\mvcb\model;
use br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\model\ModelAbstract as ParentModel,
    br\gov\mainapp\application\libcorp\vinculoFuncional\valueObject\VinculoFuncionalValueObject;

 /**
  * SISICMBio
  *
  * @package br.gov.mainapp.application.libcorp.vinculoFuncional.mvcb
  * @subpackage model
  * @author Fabio Lima <fabioolima@gmail.com>
  * */
class VinculoFuncionalModel extends ParentModel
{
    CONST REQUIRE_DATA_ERROR_MESSAGE = "Ocorreu uma falha ao persistir os dados.";
    /**
     * @param VinculoFuncional $voVinculoFuncional
     * @return VinculoFuncionalModel
     * @throws ModelException
     */
    public function save (VinculoFuncionalValueObject $voVinculoFuncional)
    {
        try {
            $this->_resultSet = $this->_persist->save($voVinculoFuncional);
            return $this;
        } catch (PersistException $pExc) {
            dump($pExc);
            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
        }
    }

    /**
     * @param VinculoFuncional $voVinculoFuncional
     * @return VinculoFuncionalModel
     * @throws ModelException
     */
    public function update (VinculoFuncionalValueObject $voVinculoFuncional)
    {
        try {
            $this->_resultSet = $this->_persist->update($voVinculoFuncional);
            return $this;
        } catch (PersistException $pExc) {
            dump($pExc);
            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
        }
    }
}