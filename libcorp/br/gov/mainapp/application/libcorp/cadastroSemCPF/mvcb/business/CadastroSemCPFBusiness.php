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
namespace br\gov\mainapp\application\libcorp\cadastroSemCPF\mvcb\business;
use br\gov\sial\core\lang\Date,
    br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\cadastroSemCPF\valueObject\CadastroSemCPFValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name CadastroSemCPFBusiness
  * @package br.gov.icmbio.sisicmbio.application.libcorp.cadastroSemCPF.mvcb
  * @subpackage business
  * @author Fabio Lima <fabioolima@gmail.com>
  * @version $Id$
  * */
class CadastroSemCPFBusiness extends ParentBusiness
{
    /**
     * Insere os dados de Cadastro Sem CPF
     *
     * @example CadastroSemCPFBusiness::save
     * @code
     * <?php
     *     # cria filtro usado pelo cadastro
     *     $cadastroVO       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $cadastroVO = CadastroSemCPFValueObject::factory();
     *     $cadastroVO->setSqPessoa(1);
     *
     *     # efetua persistência dos dados
     *     $cadastroSemCPFBusiness = CadastroSemCPFBusiness::factory();
     *     $cadastroSemCPFBusiness->save($cadastroVO);
     * ?>
     * @endcode
     *
     * @param PessoaValueObject $voPessoa
     * @param PessoaFisicaValueObject $voPessoaFisica
     * @return CadastroSemCPFValueObject
     */
    public function save (CadastroSemCPFValueObject $voCadastroSemCPF)
    {
        try {
            # Efetua validacao para salvar dados
            $this->_validateCadastro($voCadastroSemCPF);

            # Salva Cadastro
            $this->getModelPersist('libcorp')->save($voCadastroSemCPF);

            return $voCadastroSemCPF;

        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }

    /**
     * Efetua a validação
     * @param CadastroSemCPFValueObject $voCadastroSemCPF
     * @throws \br\gov\icmbio\sial\exception\IllegalArgumentException
     */
    private function _validateCadastro (CadastroSemCPFValueObject $voCadastroSemCPF)
    {
        if (trim($voCadastroSemCPF->getDtInclusao())) {
            # Efetua sanitizacao de datas
            $dateFromVo = $voCadastroSemCPF->getDtInclusao();
            $voCadastroSemCPF->setDtInclusao(Date::factory($dateFromVo, 'd/m/Y')->output());
        }
    }

    /**
     * @example CadastroSemCPFBusiness::update
     * @code
     * <?php
     *     # cria filtro usado pelo Pessoa Fisica
     *     $cadastroVO       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $cadastroVO = CadastroSemCPFValueObject::factory();
     *     $cadastorVO->setSqPessoa(1);
     *
     *     # efetua alteracao dos dados
     *     $cadastroSemCPFBusiness = CadastroSemCPFBusiness::factory();
     *     $cadastrpBusiness->update($cadastroVO);
     * ?>
     * @endcode
     *
     * @param CadastroSemCPFValueObject $voCadastroSemCPF
     * @throws BusinessException
     */
    public function update (CadastroSemCPFValueObject $voCadastroSemCPF)
    {
        try {

            $this->_validateCadastro($voCadastroSemCPF);
            $voTmp = self::factory(NULL, 'libcorp')->find($voCadastroSemCPF->getSqCadastroSemCPF());

            BusinessException::throwsExceptionIfParamIsNull(
                $voTmp->getSqCadastroSemCPF() && $voCadastroSemCPF->getSqPessoa(),
                'Na atualização é obrigatório informar o ID da Pessoa a ser alterada'
            );

            $voCadastroSemCPF->copySaveObjectData($voTmp);

            $this->getModelPersist('libcorp')
                 ->update($voCadastroSemCPF);

            return $voCadastroSemCPF;

        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }
}