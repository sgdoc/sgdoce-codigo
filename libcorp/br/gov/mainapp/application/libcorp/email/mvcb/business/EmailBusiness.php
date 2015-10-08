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
namespace br\gov\mainapp\application\libcorp\email\mvcb\business;
use br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\email\valueObject\EmailValueObject,
    br\gov\mainapp\application\libcorp\pessoaFisica\valueObject\PessoaFisicaValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name EmailBusiness
  * @package br.gov.mainapp.application.libcorp.email.mvcb
  * @subpackage business
  * @author Fábio Lima <fabioolima@gmail.com>
  * @version $Id$
  * */
class EmailBusiness extends ParentBusiness
{
    /**
     * Recupera os emails do CPF informado (<b>PessoaFisicaValueObject</b>::<i>nuCpf</i>)
     *
     * @example EmailBusiness::findByCpf
     * @code
     * <?php
     *     # cria filtro usado pelo email
     *     $pessoaFisicaVO = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $pessoaFisicaVO = PessoaFisicaValueObject::factory();
     *     $pessoaFisicaVO->setNuCpf('12345678909');
     *
     *     # efetua pesquisa
     *     $emailBusiness = EmailBusiness::factory();
     *     $emailBusiness->findByCpf($pessoaFisicaVO);
     * ?>
     * @endcode
     *
     * @param PessoaFisicaValueObject $pessoaFisicaVO
     * @return ValueObjectAbstract[]
     * @throws BusinessException
     */
    public function findByCpf (PessoaFisicaValueObject $pessoaFisicaVO)
    {
        try {
            return $this->getModelPersist('libcorp')->findByCpf($pessoaFisicaVO)->getAllValueObject();
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Insere ou atualiza email
     * @param EmailValueObject $email
     */
    public function save (EmailValueObject $email)
    {
        try {
            $this->_checkOnlyOneEmailByType($email);

            $sqEmail = $email->getSqEmail();

            if (empty($sqEmail)) {
                $this->getModelPersist('libcorp')->save($email);
            } else {
                $this->updateEmail($email);
            }

            return $email;

        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Verifica se existe só um email por tipo
     * @param EmailValueObject $email
     */
    private function _checkOnlyOneEmailByType (EmailValueObject $email)
    {
        $filter = EmailValueObject::factory();
        $filter->setSqPessoa($email->getSqPessoa())
               ->setSqTipoEmail($email->getSqTipoEmail());

        $result = parent::findByParam($filter);

        foreach ($result as $item) {
            if ($item->getSqEmail() == $email->getSqEmail()) {
                continue;
            }
            throw new BusinessException('MN090');
        }
    }

    /**
     * Exclui os dados de Email
     *
     * @example EmailBusiness::deleteEmail
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $emailVO   = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $emailVO = EmailValueObject::factory();
     *     $emailVO->setSqDadoBancario(1);
     *
     *     # efetua exclusao
     *     $emailBusiness = EmailBusiness::factory();
     *     $emailBusiness->deleteEmail($emailVO);
     * ?>
     * @endcode
     *
     * @param DadoBancarioValueObject $voDadoBancario
     * @throws BusinessException
     */
    public function deleteEmail (EmailValueObject $voEmail)
    {
        try {
            $this->getModelPersist('libcorp')->delete($voEmail);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Atualiza os dados de Email
     *
     * @example EmailBusiness::updateEmail
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $voEmail       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voEmail = EmailValueObject::factory();
     *     $voEmail->setSqEmail(1);
     *
     *     # efetua atualizacao
     *     $emailBusiness = EmailBusiness::factory();
     *     $emailBusiness->updateEmail($voEmail);
     * ?>
     * @endcode
     *
     * @param EmailValueObject $voEmail
     * @return EmailValueObject
     * @throws BusinessException
     */
    public function updateEmail (EmailValueObject $voEmail)
    {
        try {
            $this->_checkOnlyOneEmailByType($voEmail);
            $voEmailTmp = EmailBusiness::factory(NULL, 'libcorp')->find($voEmail->getSqEmail());
            $voEmailTmp->loadData($this->keepUpdateData($voEmail));
            $this->getModelPersist('libcorp')->update($voEmailTmp);
            return $voEmailTmp;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * recupera registros de pessoa fisica apartir de uma pesquisa
     *
     * @param EmailValueObject $valueObject
     * @return EmailValueObject
     */
    public function getEmailToPessoa (EmailValueObject $emailVo)
    {
        return $this->getModelPersist('libcorp')->findByParam($emailVo)->getValueObject();
    }
}