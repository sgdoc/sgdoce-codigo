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
namespace br\gov\mainapp\application\libcorp\tipoPessoa\mvcb\model;
use br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\mainapp\application\libcorp\tipoPessoa\valueObject\TipoPessoaValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\model\ModelAbstract as ParentModel;

/**
  * SISICMBio
  *
  * @name TipoPessoaModel
  * @package br.gov.mainapp.application.libcorp.tipoPessoa.mvcb
  * @subpackage Model
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * */
class TipoPessoaModel extends ParentModel
{
    /**
     * @param TipoPessoaValueObject
     * @return TipoPessoaModel
     * */
    public function findByPartOfName (TipoPessoaValueObject $tipoPessoa)
    {
        try {
            $this->_resultSet = $this->_persist->findByPartOfName($tipoPessoa);
            return $this;

        } catch (PersistException $mExc) {
            # log de error na requisicao dos dados
            ;

            throw new ModelException(self::REQUIRE_DATA_ERROR_MESSAGE);
        }
    }
}