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
namespace br\gov\mainapp\application\libcorp\email\persist\database;
use br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\persist\database\Persist as ParentPersist,
    br\gov\mainapp\application\libcorp\pessoaFisica\valueObject\PessoaFisicaValueObject;

/**
  * SISICMBio
  *
  * @name EmailPersist
  * @package br.gov.mainapp.application.libcorp.email.persist
  * @subpackage database
  * @author Fabio Lima <fabioolima@gmail.com>
  * @since 2012-03-17
  * @version $Id$
  * */
class EmailPersist extends ParentPersist
{

    /**
     * Obtem Emails por CPF
     * @param PessoaFisicaValueObject $voPessoaFisica
     */
    public function findByCpf (PessoaFisicaValueObject $voPessoaFisica)
    {
        try {
            /*
             *       SELECT tx_email
             *       FROM email AS em
             *       INNER JOIN pessoa_fisica AS pf USING (sq_pessoa)
             *       WHERE nu_cpf = ?;
             * */

            # obtem a entidade com base na anotacao
            $eEmail          = $this->getEntity(array('em' => $this->annotation()->load()->class));

            # cria entidade pessoa_fisica
            $ePessoaFisica   = $this->getEntity(array('pf' => $voPessoaFisica));

            # cria objeto de consulta baseando-se em email
            $query  = $this->getQuery($eEmail)

                # efetua join de email com pessoa_fisica
                ->join($ePessoaFisica, $eEmail->column('sqPessoa')->equals($ePessoaFisica->column('sqPessoa')))

                # aplica filtro
                ->where($ePessoaFisica->column('nuCpf')->equals($voPessoaFisica->getNuCpf()))
            ;
            # executa query
            return $this->execute($query);

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                'Um ou mais paramentros informados para na montagem da query foi avaliado como inválido'
            );
        } catch (\Exception $exp) {
            throw new PersistException(
                'Um erro inesperado ocorreu ao tentar executar a recuperação dos dados'
            );
        }
    }
}