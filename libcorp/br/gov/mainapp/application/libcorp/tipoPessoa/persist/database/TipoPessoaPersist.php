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
namespace br\gov\mainapp\application\libcorp\tipoPessoa\persist\database;
use br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\persist\database\Persist as ParentPersist,
    br\gov\mainapp\application\libcorp\tipoPessoa\valueObject\TipoPessoaValueObject;

/**
  * SISICMBio
  *
  * @name TipoPessoaPersist
  * @package br.gov.mainapp.application.libcorp.tipoPessoa
  * @subpackage persist
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * */
class TipoPessoaPersist extends ParentPersist
{
    /**
     * efetua por nome to tipo de pessoa
     *
     * @param TipoPessoaValueObject
     * @return ResultSet
     * */
    public function findByPartOfName (TipoPessoaValueObject $tipoPessoa)
    {
        try {
            /*
             * SELECT sqTipoPessoa, noTipoPessoa FROM TipoPessoa WHERE noTipoPessoa LIKE '%?%'
             * */

            # obtem a entidade com base na anotacao
            $eTipoPessoa = $this->getEntity(array('tp' => $tipoPessoa));

            # filtro
            $filter = $eTipoPessoa->column('noTipoPessoa')
                                  ->ilike(sprintf('%%%s%%', $tipoPessoa->getNoTipoPessoa()));

            # cria objeto de consulta baseando-se em bioma
            $query  = $this->getQuery($eTipoPessoa)
                           ->where($filter);

            # executa query
            return $this->execute($query);

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                'Um ou mais paramentros informados para na montagem da query foi avaliado como inválido'
            );
        } catch (\exception $exp) {
            throw new PersistException(
                'Um erro inesperado ocorreu ao tentar executar a recuperação dos dados'
            );
        }
    }
}