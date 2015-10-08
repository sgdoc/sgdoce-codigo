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
namespace br\gov\mainapp\application\libcorp\tipoPessoa\mvcb\business;
use br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\tipoPessoa\valueObject\TipoPessoaValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name TipoPessoaBusiness
  * @package br.gov.mainapp.application.libcorp.tipoPessoa.mvcb
  * @subpackage business
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * */
class TipoPessoaBusiness extends ParentBusiness
{
    /**
     * Recupera tipo pessoa filtrando por parte do nome (<b>TipoPessoaValueObject</b>::<i>noTipoPessoa</i>)
     *
     * @example TipoPessoaBusiness::findByPartOfName
     * @code
     * <?php
     *    # cria valueObject que servira de filtro
     *    $filter = ValueObjectAbstract::factory('fullnamespace');
     *    $filter->setNoTipoPessoa('fisica');
     *
     *    # solicita ao business que efetue a pesquisa
     *    $business = BusinessAbstract::factory('fullnamespace');
     *    $business->findByPartOfName($filter);
     * ?>
     * @endcode
     *
     * @param TipoPessoaValueObject
     * @return TipoPessoaValueObject[]
     * @throws BusinessException
     * */
    public function findByPartOfName (TipoPessoaValueObject $tipoPessoa)
    {
        try {
            $res = $this->getModelPersist('libcorp')->findByPartOfName($tipoPessoa);
            return $res->getAllValueObject();
        } catch (ModelException $mExc) {
            throw new BusinessException(self::GET_DATA_ERROR_MESSAGE);
        }
    }
}