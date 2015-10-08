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
namespace br\gov\mainapp\application\libcorp\pessoaJuridica\mvcb\business;
use br\gov\sial\core\lang\Date,
    br\gov\sial\core\util\validate\Validate,
    br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\pessoaJuridica\valueObject\PessoaJuridicaValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name PessoaJuridicaBusiness
  * @package br.gov.mainapp.application.libcorp.pessoaJuridica.mvcb
  * @subpackage business
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * */
class PessoaJuridicaBusiness extends ParentBusiness
{
    /**
     * Determina o tipo de Pessoa Fisica
     */
    const TIPO_PESSOA_JURIDICA = 2;

    /**
     * @var string
     */
    const INVALID_CNPJ = 'O CNPJ informado é invalido.';

    /**
     * @var string
     */
    const MISSING_KEY = 'Na atualização é obrigatório informar o ID da Pessoa Juridica a ser alterada.';

    /**
     * Insere os dados de Pessoa e Pessoa Juridica
     * - Dados Obrigatórios : Nome
     * - Dados Validadados  : CNPJ
     *
     * @example PessoaJuridicaBusiness::savePessoaJuridica
     * @code
     * <?php
     *     $pessoaVO       = ValueObjectAbstract::factory('fullnamespace');
     *     $pessoaJuridicaVO = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $pessoaVO = PessoaValueObject::factory();
     *     # $pessoaJuridicaVO = PessoaJuridicaValueObject::factory();
     *     $pessoaJuridicaVO->setNuCnpj('12345678901234');
     *
     *     # efetua pesquisa
     *     $pessoaJuridicaBusiness = PessoaJuridicaBusiness::factory();
     *     $pessoaJuridicaBusiness->savePessoaJuridica($pessoaVO, $pessoaJuridicaVO);
     * ?>
     * @endcode
     *
     * @param PessoaValueObject $voPessoa
     * @param PessoaJuridicaValueObject $voPessoaJuridica
     * @throw BusinessException
     */
    public function savePessoaJuridica (PessoaValueObject $voPessoa,
                                        PessoaJuridicaValueObject $voPessoaJuridica)
    {
        try {
            # retira a máscara do CNPJ
            $voPessoaJuridica->setNuCnpj(preg_replace("/\D+/", "", $voPessoaJuridica->getNuCnpj()));

            # Efetua validacao para salvar dados
            $this->_validatePessoaJuridica($voPessoa, $voPessoaJuridica);

            # Salva Pessoa
            $voPessoa->setSqTipoPessoa(self::TIPO_PESSOA_JURIDICA);
            PessoaBusiness::factory(NULL, 'libcorp')->save($voPessoa);
            $sqPessoa = $voPessoa->getSqPessoa();

            # Salva PessoaJuridica
            $voPessoaJuridica->setSqPessoa($sqPessoa);
            $this->getModelPersist('libcorp')->save($voPessoaJuridica);

            return $voPessoaJuridica;

        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Efetua a validação
     * @param PessoaValueObject $voPessoa
     * @param PessoaJuridicaValueObject $voPessoaJuridica
     * @throws BusinessException
     */
    private function _validatePessoaJuridica (PessoaValueObject $voPessoa,
                                              PessoaJuridicaValueObject $voPessoaJuridica)
    {
        if (trim($voPessoaJuridica->getNuCnpj())) {
            BusinessException::throwsExceptionIfParamIsNull(Validate::isCnpj($voPessoaJuridica->getNuCnpj()),
            self::INVALID_CNPJ);
        }

        if (trim($voPessoaJuridica->getDtAbertura())) {
            # Efetua sanitizacao de datas
            $dateAbertura = $voPessoaJuridica->getDtAbertura();
            $voPessoaJuridica->setDtAbertura(Date::factory($dateAbertura, 'd/m/Y')->output());
        }
    }

    /**
     * Atualiza os dados de Pessoa Juridica
     *
     * @example PessoaJuridicaBusiness::updatePessoaJuridica
     * @code
     * <?php
     *     $pessoaVO = ValueObjectAbstract::factory('fullnamespace');
     *     $pessoaJuridicaVO = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $pessoaVO = PessoaValueObject::factory();
     *     # $pessoaJuridicaVO = PessoaJuridicaValueObject::factory();
     *     $pessoaJuridicaVO->setNuCnpj('12345678901234');
     *
     *     # efetua pesquisa
     *     $pessoaJuridicaBusiness = PessoaJuridicaBusiness::factory();
     *     $pessoaJuridicaBusiness->updatePessoaJuridica($pessoaVO, $pessoaJuridicaVO);
     * ?>
     * @endcode
     *
     * @param PessoaValueObject $voPessoa
     * @param PessoaJuridicaValueObject $voPessoaJuridica
     * @throws BusinessException
     */
    public function updatePessoaJuridica (PessoaValueObject $voPessoa,
                                          PessoaJuridicaValueObject $voPessoaJuridica)
    {
        try {
            # efetua validação
            $this->_validatePessoaJuridica($voPessoa, $voPessoaJuridica);
            $sqPessoa = $voPessoa->getSqPessoa();

            BusinessException::throwsExceptionIfParamIsNull($sqPessoa,
                self::MISSING_KEY);

            # atualiza a pessoa Juridica
            $voPessoa->setSqTipoPessoa(self::TIPO_PESSOA_JURIDICA);
            PessoaBusiness::factory(NULL, 'libcorp')->update($voPessoa);

            # retira a máscara do CNPJ
            $voPessoaJuridica->setNuCnpj(preg_replace("/\D+/", "", $voPessoaJuridica->getNuCnpj()));

            # Salva PessoaJuridica
            $voPessoaJuridica->setSqPessoa($sqPessoa);
            $this->getModelPersist('libcorp')->update($voPessoaJuridica);

            return $voPessoaJuridica;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    public function findByParamFilterByName (PessoaValueObject $voPessoa, $limit = 10, $offset = 0)
    {
        $result = $this->getModelPersist('libcorp')->findByParamFilterByName($voPessoa, (integer) $limit, (integer) $offset);
        return $result->getAllDataViewObject();
    }

    /**
     * recupera os dados da pessoa juridica informado o CNPJ ou parte dele
     * @example PessoaJuridicaBusiness::updatePessoaJuridica
     * @code
     * <?php
     *     # pesquisa por cnpj iniciado por: 12345
     *     $result = PessoaJuridicaBusiness::factory()->findByCnpj('123456');
     *
     *     # pesquisa por cnpj iniciado por: 123456789
     *     $result = PessoaJuridicaBusiness::factory()->findByCnpj('123456789');
     * ?>
     * @endcode
     * @param integer $nuCnpj
     * @return PessoaJuridica[]
     * */
    public function findByCnpj ($nuCnpj)
    {
        $nuCnpj = preg_replace('/\D/', '', $nuCnpj);
        $result = $this->getModelPersist('libcorp')
                       ->findByCnpj($nuCnpj);
        return $result->getAllDataViewObject();
    }
}