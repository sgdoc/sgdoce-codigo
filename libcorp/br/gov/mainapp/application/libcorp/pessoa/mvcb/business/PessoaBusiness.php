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
namespace br\gov\mainapp\application\libcorp\pessoa\mvcb\business;
use br\gov\sial\core\valueObject\ValueObjectAbstract,
    br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\pessoaFisica\mvcb\business\PessoaFisicaBusiness,
    br\gov\mainapp\application\libcorp\pessoaFisica\valueObject\PessoaFisicaValueObject,
    br\gov\mainapp\application\libcorp\pessoaJuridica\mvcb\business\PessoaJuridicaBusiness,
    br\gov\mainapp\application\libcorp\pessoaJuridica\valueObject\PessoaJuridicaValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name PessoaBusiness
  * @package br.gov.mainapp.application.libcorp.pessoa.mvcb
  * @subpackage business
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * */
class PessoaBusiness extends ParentBusiness
{
    /**
     * @var string
     */
    const NAME_REQUIRED = 'É obrigatório informar um Nome.';

    /**
     * Insere ou atualiza Pessoa
     * @param PessoaValueObject
     */
    public function save (PessoaValueObject $voPessoa)
    {
        try {
            $sqPessoa = $voPessoa->getSqPessoa();
            $this->_validatePessoa($voPessoa);

            if (empty($sqPessoa)) {
                $this->getModelPersist('libcorp')->save($voPessoa);
            } else {
                $this->getModelPersist('libcorp')->update($voPessoa);
            }
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    private function _validatePessoa (PessoaValueObject $voPessoa)
    {
        try {
            BusinessException::throwsExceptionIfParamIsNull(trim($voPessoa->getNoPessoa()), self::NAME_REQUIRED);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Atualiza a pessoa
     *
     * @example PessoaBusiness::update
     * @code
     * <?php
     *     # cria filtro usado pelo email
     *     $pessoaVO = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $pessoaVO = PessoaValueObject::factory();
     *     $pessoaVO->setNoPessoa('Foo Bar');
     *
     *
     *     # efetua pesquisa
     *     $pessoaBusiness = PessoaBusiness::factory();
     *     $pessoaBusiness->update($pessoaFisicaVO);
     * ?>
     * @endcode
     *
     * @param PessoaValueObject $voPessoa
     * @throws BusinessException
     */
    public function update (PessoaValueObject $voPessoa)
    {
        try {
            # efetua a validação
            $this->_validatePessoa($voPessoa);

            $this->getModelPersist('libcorp')->update($voPessoa);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Obtem os dados de Pessoa Pelo CPF (<b>PessoaFisicaValueObject</b>::<i>nuCpf</i>)
     *
     * @example PessoaBusiness::findByCpf
     * @code
     * <?php
     *     # cria filtro usado pelo email
     *     $pessoaFisicaVO = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $pessoaFisicaVO = PessoaFisicaValueObject::factory();
     *     $pessoaFisicaVO->setNuCpf('12345678909');
     *
     *
     *     # efetua pesquisa
     *     $pessoaBusiness = PessoaBusiness::factory();
     *     $pessoaBusiness->findByCpf($pessoaFisicaVO);
     * ?>
     * @endcode
     *
     * @param PessoaFisicaValueObject $voPessoaFisica
     * @return ValueObjectAbstract[]
     * @throws BusinessException
     */
    public function findByCpf (PessoaFisicaValueObject $voPessoaFisica)
    {
        try {
            $result = $this->getModelPersist('libcorp')->findByCpf($voPessoaFisica);
            return $result->getValueObject();
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Obtem os dados de Pessoa Pelo CNPJ (<b>PessoaJuridicaValueObject</b>::<i>nuCnpj</i>)
     *
     * @example PessoaBusiness::findByCnpj
     * @code
     * <?php
     *     # cria filtro usado pelo email
     *     $voPessoaJuridica = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voPessoaJuridica = PessoaJuridicaValueObject::factory();
     *     $voPessoaJuridica->setNuCpf('123456789000123');
     *
     *
     *     # efetua pesquisa
     *     $pessoaBusiness = PessoaBusiness::factory();
     *     $pessoaBusiness->findByCnpj($voPessoaJuridica);
     * ?>
     * @endcode
     *
     * @param PessoaJuridicaValueObject $voPessoaJuridica
     * @return ValueObjectAbstract[]
     * @throws BusinessException
     */
    public function findByCnpj (PessoaJuridicaValueObject $voPessoaJuridica)
    {
        try {
            $result = $this->getModelPersist('libcorp')->findByCnpj($voPessoaJuridica);
            return $result->getAllDataViewObject();
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Retorna os dados de Pessoa por Nome (<b>ilike</b>) (<b>PessoaValueObject</b>::<i>noPessoa</i>)
     *
     * @example PessoaBusiness::findByNome
     * @code
     * <?php
     *     # cria filtro usado pela PessoaBusiness
     *     $pessoaVO = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $pessoaVO = PessoaValueObject::factory();
     *     $pessoaVo->setNoPessoa('foo');
     *
     *     # efetua pesquisa
     *     $pessoaBusiness = PessoaBusiness::factory();
     *     $pessoaBusiness->findByNome($pessoaVO);
     * ?>
     * @endcode
     *
     * @param PessoaValueObject $voPessoa
     * @throws BusinessException
     * @return ModelObject
     */
    public function findByNome (PessoaValueObject $voPessoa, $limit = 10, $offSet = 0)
    {
        try {
            $result = $this->getModelPersist('libcorp')->findByNome($voPessoa, $limit, $offSet);
            return $result->getAllDataViewObject();
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Retorna os dados de Pessoa por Nome Fantasia (<b>ilike</b>) (<b>PessoaJuridicaValueObject</b>::<i>noFantasia</i>)
     *
     * @example PessoaBusiness::findByNomeFantasia
     * @code
     * <?php
     *     # cria filtro usado pela PessoaBusiness
     *     $voPessoaJuridica = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voPessoaJuridica = PessoaJuridicaValueObject::factory();
     *     $voPessoaJuridica->setNoFantasia('foo');
     *
     *     # efetua pesquisa
     *     $pessoaBusiness = PessoaBusiness::factory();
     *     $pessoaBusiness->findByNomeFantasia($voPessoaJuridica);
     * ?>
     * @endcode
     *
     * @param PessoaJuridicaValueObject $voPessoaJuridica
     * @throws BusinessException
     * @return ModelObject
     */
    public function findByNomeFantasia (PessoaJuridicaValueObject $voPessoaJuridica)
    {
        try {
            $result = $this->getModelPersist('libcorp')->findByNomeFantasia($voPessoaJuridica);
            return $result->getAllDataViewObject();
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }
}