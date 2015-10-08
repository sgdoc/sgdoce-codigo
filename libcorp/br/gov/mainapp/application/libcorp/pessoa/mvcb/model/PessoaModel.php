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
namespace br\gov\mainapp\application\libcorp\pessoa\mvcb\model;
use br\gov\sial\core\persist\exception\PersistException,
    br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\model\ModelAbstract as ParentModel,
    br\gov\mainapp\application\libcorp\pessoaFisica\valueObject\PessoaFisicaValueObject,
    br\gov\mainapp\application\libcorp\pessoaJuridica\valueObject\PessoaJuridicaValueObject;

/**
  * SISICMBio
  *
  * @name PessoaModel
  * @package br.gov.mainapp.application.libcorp.pessoa.mvcb
  * @subpackage model
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * */
class PessoaModel extends ParentModel
{
    /**
     * Retorno os dados de Pessoa Por Um CPF
     * @param PessoaFisicaValueObject $voPessoaFisica
     */
    public function findByCpf (PessoaFisicaValueObject $voPessoaFisica)
    {
        try {
            $this->_resultSet = $this->_persist->findByCpf($voPessoaFisica);
            return $this;
        } catch (PersistException $pExcp) {
            throw new ModelException($pExcp->getMessage());
        }
    }

    /**
     * Retorna Pessoa por Nome
     * @param PessoaValueObject $voPessoa
     * @throws ModelException
     */
    public function findByNome (PessoaValueObject $voPessoa)
    {
        try {
            $this->_resultSet = $this->_persist->findByNome($voPessoa);
            return $this;
        } catch (PersistException $pExcp) {
            throw new ModelException($pExcp->getMessage());
        }
    }

    /**
     * Retorna os dados de pessoa por CNPJ
     * @param PessoaJuridicaValueObject $voPessoaJuridica
     */
    public function findByCnpj (PessoaJuridicaValueObject $voPessoaJuridica)
    {
        try {
            $this->_resultSet = $this->_persist->findByCnpj($voPessoaJuridica);
            return $this;
        } catch (PersistException $pExcp) {
            throw new ModelException($pExcp->getMessage());
        }
    }

    /**
     * Retorna pessoa por Nome Fantasia
     * @param PessoaJuridicaValueObject $voPessoaJuridica
     */
    public function findByNomeFantasia (PessoaJuridicaValueObject $voPessoaJuridica)
    {
        try {
            $this->_resultSet = $this->_persist->findByNomeFantasia($voPessoaJuridica);
            return $this;
        } catch (PersistException $pExcp) {
            throw new ModelException($pExcp->getMessage());
        }
    }
}