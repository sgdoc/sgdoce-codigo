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
namespace br\gov\mainapp\application\libcorp\pessoaFisica\mvcb\business;
use br\gov\sial\core\lang\Date,
    br\gov\sial\core\util\validate\Validate,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\pessoa\mvcb\business\PessoaBusiness,
    br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\pessoaFisica\valueObject\PessoaFisicaValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name PessoaFisicaBusiness
  * @package br.gov.mainapp.application.libcorp.pessoaFisica.mvcb
  * @subpackage business
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * */
class PessoaFisicaBusiness extends ParentBusiness
{
    /**
     * Determina o tipo de Pessoa Fisica
     */
    const TIPO_PESSOA_FISICA = 1;

    /**
     * @var string
     */
    const INVALID_CPF = 'O CPF informado é inválido.';

    /**
     * @var string
     */
    const REQUIRED_SQ_PESSOA = 'Na atualização é obrigatório informar o ID da Pessoa Fisica a ser alterada.';

    /**
     * Insere os dados de Pessoa e Pessoa Fisica
     * - Dados Obrigatórios : Nome
     * - Dados Validadados  : CPF
     *
     * @example PessoaFisicaBusiness::savePessoaFisica
     * @code
     * <?php
     *     # cria filtro usado pelo email
     *     $pessoaVO       = ValueObjectAbstract::factory('fullnamespace');
     *     $pessoaFisicaVO = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $pessoaVO = PessoaValueObject::factory();
     *     # $pessoaFisicaVO = PessoaFisicaValueObject::factory();
     *     $pessoaFisicaVO->setNuCpf('12345678909');
     *
     *     # efetua pesquisa
     *     $pessoaBusiness = PessoaFisicaBusiness::factory();
     *     $pessoaBusiness->savePessoaFisica($pessoaVO, $pessoaFisicaVO);
     * ?>
     * @endcode
     *
     * @param PessoaValueObject $voPessoa
     * @param PessoaFisicaValueObject $voPessoaFisica
     */
    public function savePessoaFisica (PessoaValueObject $voPessoa, PessoaFisicaValueObject $voPessoaFisica)
    {
        try {
            # retira a máscara do CPF
            $voPessoaFisica->setNuCpf(preg_replace('/\D+/', '', $voPessoaFisica->getNuCpf()));

            # Efetua validacao para salvar dados
            $this->_validatePessoaFisica($voPessoa, $voPessoaFisica);

            # Salva Pessoa
            $voPessoa->setSqTipoPessoa(self::TIPO_PESSOA_FISICA);
            PessoaBusiness::factory(NULL, 'libcorp')->save($voPessoa);
            $sqPessoa = $voPessoa->getSqPessoa();

            # Salva PessoaFisica
            $voPessoaFisica->setSqPessoa($sqPessoa);
            $this->getModelPersist('libcorp')->save($voPessoaFisica);

            return $voPessoaFisica;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Efetua a validação
     * @param PessoaValueObject $voPessoa
     * @param PessoaFisicaValueObject $voPessoaFisica
     * @throws BusinessException
     */
    private function _validatePessoaFisica (PessoaValueObject $voPessoa, PessoaFisicaValueObject $voPessoaFisica)
    {
        try {
            if (trim($voPessoaFisica->getNuCpf())) {
                BusinessException::throwsExceptionIfParamIsNull(Validate::isCpf($voPessoaFisica->getNuCpf()),
                self::INVALID_CPF);
            }

            if (trim($voPessoaFisica->getDtNascimento())) {
                # Efetua sanitizacao de datas
                $dateFromVo = $voPessoaFisica->getDtNascimento();
                $voPessoaFisica->setDtNascimento(Date::factory($dateFromVo, 'd/m/Y')->output());
            }

        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * @example PessoaFisicaBusiness::updatePessoaFisica
     * @code
     * <?php
     *     # cria filtro usado pelo Pessoa Fisica
     *     $pessoaVO       = ValueObjectAbstract::factory('fullnamespace');
     *     $pessoaFisicaVO = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $pessoaVO = PessoaValueObject::factory();
     *     # $pessoaFisicaVO = PessoaFisicaValueObject::factory();
     *     $pessoaFisicaVO->setNuCpf('12345678909');
     *
     *     # efetua pesquisa
     *     $pessoaBusiness = PessoaFisicaBusiness::factory();
     *     $pessoaBusiness->updatePessoaFisica($pessoaVO, $pessoaFisicaVO);
     * ?>
     * @endcode
     *
     * @param PessoaValueObject $voPessoa
     * @param PessoaFisicaValueObject $voPessoaFisica
     * @throws BusinessException
     */
    public function updatePessoaFisica (PessoaValueObject $voPessoa, PessoaFisicaValueObject $voPessoaFisica)
    {
        try {
            $voPessoaFisicaClone = clone $voPessoaFisica;

            # retira a máscara do CPF
            $voPessoaFisica->setNuCpf(preg_replace("/\D+/", "", $voPessoaFisica->getNuCpf()));

            # efetua validação
            $this->_validatePessoaFisica($voPessoa, $voPessoaFisica);
            $sqPessoa = $voPessoa->getSqPessoa();
            BusinessException::throwsExceptionIfParamIsNull($sqPessoa, self::REQUIRED_SQ_PESSOA);

            $voTmp = PessoaBusiness::factory(NULL, 'libcorp')->find($voPessoa->getSqPessoa());
            $voPessoa->copySaveObjectData($voTmp);
            PessoaBusiness::factory(NULL, 'libcorp')->update($voPessoa);

            # Salva PessoaFisica
            $voPessoaTmp = self::factory(NULL, 'libcorp')->find($voPessoa->getSqPessoa());
            $voPessoaFisica->copySaveObjectData($voPessoaTmp);

            # anula nacionalidade
            if (0 === $voPessoaFisicaClone->getSqNacionalidade()) {
                $voPessoaFisica->setSqNacionalidade(NULL);
            }

            # anula naturalidade
            if(0 === $voPessoaFisicaClone->getSqNaturalidade()) {
                $voPessoaFisica->setSqNaturalidade(NULL);
            }

            $this->getModelPersist('libcorp')->update($voPessoaFisica);

            return $voPessoaFisica;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }
}