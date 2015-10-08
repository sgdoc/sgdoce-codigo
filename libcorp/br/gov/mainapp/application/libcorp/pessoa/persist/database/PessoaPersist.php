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
namespace br\gov\mainapp\application\libcorp\pessoa\persist\database;
use br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\exception\IllegalArgumentException,
    br\gov\sial\core\persist\database\Persist as ParentPersist,
    br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\pessoaFisica\valueObject\PessoaFisicaValueObject,
    br\gov\mainapp\application\libcorp\pessoaJuridica\valueObject\PessoaJuridicaValueObject;

/**
  * SISICMBio
  *
  * @name PessoaPersist
  * @package br.gov.mainapp.application.libcorp.pessoa.persist
  * @subpackage database
  * @author Fabio Lima <fabioolima@gmail.com>
  * @since 2012-03-15
  * @version $Id$
  * */
class PessoaPersist extends ParentPersist
{
    /**
     * @var string
     */
    const INVALID_PARAMETER = 'Um ou mais parâmentros informados para a montagem da query é inválido.';

    /*
     * @var string
     */
    const UNEXPECTED_EXCEPTION = 'Um erro inesperado ocorreu ao tentar executar a recuperação dos dados.';


    /**
     * Join para obter Pessoas por Nome
     * @param PessoaFisicaValueObject $voPessoaFisica
     */
    public function findByCpf (PessoaFisicaValueObject $voPessoaFisica)
    {
        try {
            /*
             *       SELECT *
             *       FROM pessoa as ps
             *       INNER JOIN pessoa_fisica as pf USING (sq_pessoa)
             *       WHERE nu_cpf = '69905231153'
             * */

            # obtem a entidade com base na anotacao
            $ePessoa          = $this->getEntity(array('pe' => $this->annotation()->load()->class));

            # cria entidade pessoa_fisica
            $ePessoaFisica   = $this->getEntity(array('pf' => $voPessoaFisica));

            # cria objeto de consulta baseando-se em email
            $query  = $this->getQuery($ePessoa)

                # efetua join de email com pessoa_fisica
                ->join($ePessoaFisica, $ePessoa->column('sqPessoa')->equals($ePessoaFisica->column('sqPessoa')))

                # aplica filtro
                ->where($ePessoaFisica->column('nuCpf')->equals($voPessoaFisica->getNuCpf()))
            ;
            # executa query
            return $this->execute($query);

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                self::INVALID_PARAMETER
            );
        } catch (\Exception $exp) {
            throw new PersistException(
                self::UNEXPECTED_EXCEPTION
            );
        }
    }

    /**
     * retorna os dados de Pessoa por Nome (Ilike)
     * @param PessoaValueObject $voPessoa
     * @throws PersistException
     */
    public function findByNome (PessoaValueObject $voPessoa, $limit = 10, $offSet = 0)
    {
        try {
            /*
             *       SELECT pess.sq_pessoa,
             *              pess.sq_tipo_pessoa,
             *              pess.no_pessoa
             *       FROM   corporativo.pessoa pess
             *       WHERE  pess.no_pessoa ilike $voPessoa->getNoPessoa()
             **/

            # obtem a entidade com base na anotacao
            $ePessoa          = $this->getEntity(array('pess' => $this->annotation()->load()->class));

            # cria objeto de consulta baseando-se em email
            $query  = $this->getQuery($ePessoa)

                # aplica filtro
                ->where($ePessoa->column('noPessoa')->ilike('%' . $voPessoa->getNoPessoa() . '%'));
            
            # filtro de tipo pessoa
            if ($voPessoa->getSqTipoPessoa()) {
                $query->and($ePessoa->column('sqTipoPessoa')->equals($voPessoa->getSqTipoPessoa()->getSqTipoPessoa()));
            }
            
            /* limit query */
            $query->limit((integer) $limit, (integer) $offSet);
            
            # executa query
            return $this->execute($query);

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                self::INVALID_PARAMETER
            );
        } catch (\Exception $exp) {
            throw new PersistException(
                self::UNEXPECTED_EXCEPTION
            );
        }
    }

    /**
     * Obtem pessoa por CNPJ
     * @param PessoaJuridicaValueObject $voPessoaJuridica
     */
    public function findByCnpj (PessoaJuridicaValueObject $voPessoaJuridica)
    {
        try {
            /*
             *       SELECT
             *           PJ.sq_pessoa,
             *           PJ.nu_cnpj,
             *           PJ.no_fantasia,
             *           PJ.sg_empresa,
             *           PJ.dt_abertura,
             *           P.no_pessoa,
             *           P.sq_tipo_pessoa
             *       FROM
             *           corporativo.pessoa_juridica PJ
             *       JOIN
             *           corporativo.pessoa P ON P.sq_pessoa = PJ.sq_pessoa
             *       WHERE PJ.nu_cnpj = :nu_cnpj
             **/

            # obtem a entidade com base na anotacao
            $ePessoa         = $this->getEntity(array('pes' => $this->annotation()->load()->class));

            $ePessoaJuridica = $this->getEntity(array('pej' => PessoaJuridicaValueObject::factory()));

            # cria objeto de consulta baseando-se em email
            $query  = $this->getQuery($ePessoa)

                # efetua join de email com pessoa_fisica
                ->join($ePessoaJuridica, $ePessoa->column('sqPessoa')->equals($ePessoaJuridica->column('sqPessoa')))

                # aplica filtro
                ->where($ePessoaJuridica->column('nuCnpj')->equals($voPessoaJuridica->getNuCnpj()))
            ;
            # executa query
            return $this->execute($query);

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                self::INVALID_PARAMETER
            );
        } catch (\Exception $exp) {
            throw new PersistException(
                self::UNEXPECTED_EXCEPTION
            );
        }
    }

    /**
     * Retorna os dados de Pessoa por Nome Fantasia
     * @param PessoaJuridicaValueObject $voPessoaJuridica
     */
    public function findByNomeFantasia (PessoaJuridicaValueObject $voPessoaJuridica)
    {
        try {
            /*
             *       SELECT
             *           PJ.sq_pessoa,
             *           PJ.nu_cnpj,
             *           PJ.no_fantasia,
             *           PJ.sg_empresa,
             *           PJ.dt_abertura,
             *           P.no_pessoa,
             *           P.sq_tipo_pessoa
             *       FROM
             *           corporativo.pessoa_juridica PJ
             *       JOIN
             *           corporativo.pessoa P ON P.sq_pessoa = PJ.sq_pessoa
             *       WHERE
             *           PJ.no_fantasia ILIKE '%no_fantasia%');
             **/

            # obtem a entidade com base na anotacao
            $ePessoa         = $this->getEntity(array('pes' => $this->annotation()->load()->class));

            $ePessoaJuridica = $this->getEntity(array('pej' => PessoaJuridicaValueObject::factory()));

            # cria objeto de consulta baseando-se em email
            $query  = $this->getQuery($ePessoa)

                # efetua join de email com pessoa_fisica
                ->join($ePessoaJuridica, $ePessoa->column('sqPessoa')->equals($ePessoaJuridica->column('sqPessoa')))

                # aplica filtro
                ->where($ePessoaJuridica->column('noFantasia')->ilike('%' . $voPessoaJuridica->getNoFantasia() . '%'))
            ;
            # executa query
            return $this->execute($query);

        } catch (IllegalArgumentException $iae) {
            throw new PersistException(
                self::INVALID_PARAMETER
            );
        } catch (\Exception $exp) {
            throw new PersistException(
                self::UNEXPECTED_EXCEPTION
            );
        }
    }
}