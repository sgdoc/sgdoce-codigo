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
namespace br\gov\mainapp\application\libcorp\telefone\mvcb\business;
use br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\telefone\valueObject\TelefoneValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name TelefoneBusiness
  * @package br.gov.icmbio.sisicmbio.application.libcorp.telefone.mvcb
  * @subpackage business
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * */
class TelefoneBusiness extends ParentBusiness
{
    /**
     * @var string
     */
    const IDENTIFIER_NOT_FOUND = 'O identificador do telefone não foi encontrado.';

    /**
     * Insere ou atualiza telefone
     * @param TelefoneValueObject $telefone
     */
    public function save (TelefoneValueObject $telefone)
    {
        $this->_checkOnlyOneTelefoneByType($telefone);

        $sqTelefone = $telefone->getSqTelefone();

        if (empty($sqTelefone)) {
            $this->getModelPersist('libcorp')->save($telefone);
        } else {
            $this->getModelPersist('libcorp')->update($telefone);
        }

        return $telefone;
    }

    /**
     * Verifica se existe só um telefone por tipo
     * @param TelefoneValueObject $telefone
     */
    private function _checkOnlyOneTelefoneByType (TelefoneValueObject $telefone)
    {
        $filter = TelefoneValueObject::factory();
        $filter->setSqPessoa($telefone->getSqPessoa())
               ->setSqTipoTelefone($telefone->getSqTipoTelefone());

        $result = parent::findByParam($filter);

        foreach ($result as $item) {
            if ($item->getSqTelefone() == $telefone->getSqTelefone()) {
                continue;
            }
            throw new BusinessException('MN089');
        }
    }

    /**
     * Exclui os dados de Telefone
     *
     * @example TelefoneBusiness::deleteTelefone
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $voTelefone       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voTelefone = TelefoneValueObject::factory();
     *     $voTelefone->setSqTelefone(1);
     *
     *     # efetua exclusao
     *     $telefoneBusiness = TelefoneBusiness::factory();
     *     $telefoneBusiness->deleteTelefone($voTelefone);
     * ?>
     * @endcode
     *
     * @param TelefoneValueObject $voTelefone
     * @throws BusinessException
     */
    public function deleteTelefone (TelefoneValueObject $voTelefone)
    {
        try {
            $this->delete($voTelefone);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }

    /**
     * Deleta telefone
     * @param TelefoneValueObject $telefone
     */
    public function delete (TelefoneValueObject $voTelefone)
    {
        $voTmp = TelefoneBusiness::factory(NULL, 'libcorp')->findByParam($voTelefone);
        $voTelefone = current($voTmp);
        if ($voTelefone) {
            $this->getModelPersist('libcorp')->delete(current($voTmp));
        }
    }

    /**
     * Atualiza os dados de Telefone
     *
     * @example TelefoneBusiness::updateTelefone
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $voTelefone       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voTelefone = TelefoneValueObject::factory();
     *     $voTelefone->setSqTelefone(1);
     *
     *     # efetua exclusao
     *     $telefoneBusiness = TelefoneBusiness::factory();
     *     $telefoneBusiness->updateTelefone($voTelefone);
     * ?>
     * @endcode
     *
     * @param TelefoneValueObject $voTelefone
     * @return TelefoneValueObject
     * @throws BusinessException
     */
    public function updateTelefone (TelefoneValueObject $voTelefone)
    {
        try {
            $voTmp = TelefoneBusiness::factory(NULL, 'libcorp')->find($voTelefone->getSqTelefone());
            BusinessException::throwsExceptionIfParamIsNull($voTmp->getSqTelefone(), self::IDENTIFIER_NOT_FOUND);

            $voTelefone->copySaveObjectData($voTmp);

            $this->getModelPersist('libcorp')
                 ->update($voTelefone);

            return $voTelefone;

        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }
}