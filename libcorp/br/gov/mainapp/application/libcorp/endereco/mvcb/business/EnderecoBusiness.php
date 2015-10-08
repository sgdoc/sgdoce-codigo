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
namespace br\gov\mainapp\application\libcorp\endereco\mvcb\business;
use br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\endereco\valueObject\EnderecoValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name EnderecoBusiness
  * @package br.gov.icmbio.sisicmbio.application.libcorp.endereco.mvcb
  * @subpackage business
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * */
class EnderecoBusiness extends ParentBusiness
{
    /**
     * Insere / atualiza endereço de pessoa
     * @param EnderecoValueObject $telefone
     */
    public function save (EnderecoValueObject $endereco)
    {
        try {
            $this->_checkOnlyOneEnderecoByType($endereco);

            $sqEndereco = $endereco->getSqEndereco();

            if (empty($sqEndereco)) {
                $this->getModelPersist('libcorp')->save($endereco);
            } else {
                $this->getModelPersist('libcorp')->update($endereco);
            }
            return $endereco;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(),$mExcp->getCode());
        }
    }

    /**
     * Verifica se existe só um endereço por tipo
     * @param EnderecoValueObject $endereco
     */
    private function _checkOnlyOneEnderecoByType (EnderecoValueObject $endereco)
    {
        $filter = EnderecoValueObject::factory();
        $filter->setSqPessoa($endereco->getSqPessoa())
               ->setSqTipoEndereco($endereco->getSqTipoEndereco());

        $result = parent::findByParam($filter);

        foreach ($result as $item) {
            if ($item->getSqEndereco() == $endereco->getSqEndereco()) {
                continue;
            }
            throw new BusinessException('MN088');
        }
    }

    /**
     * Exclui os dados de Endereco
     *
     * @example EnderecoBusiness::deleteEndereco
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $voEndereco       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voEndereco = EnderecoValueObject::factory();
     *     $voEndereco->setSqEndereco(1);
     *
     *     # efetua exclusao
     *     $enderecoBusiness = DocumentoBusiness::factory();
     *     $enderecoBusiness->deleteEndereco($voEndereco);
     * ?>
     * @endcode
     *
     * @param EnderecoValueObject $voEndereco
     * @throws BusinessException
     */
    public function deleteEndereco (EnderecoValueObject $voEndereco)
    {
        try {
            $this->getModelPersist('libcorp')->delete($voEndereco);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Atualiza os dados de Endereco
     *
     * @example EnderecoBusiness::updateEndereco
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $voEndereco       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voEndereco = EnderecoValueObject::factory();
     *     $voEndereco->setSqEndereco(1);
     *
     *     # efetua atualizacao
     *     $enderecoBusiness = DocumentoBusiness::factory();
     *     $enderecoBusiness->updateEndereco($voEndereco);
     * ?>
     * @endcode
     *
     * @param EnderecoValueObject $voEndereco
     * @return EnderecoValueObject
     * @throws BusinessException
     */
    public function updateEndereco (EnderecoValueObject $voEndereco)
    {
        try {
            $voEnderecoTmp = EnderecoBusiness::factory(NULL, 'libcorp')->find($voEndereco->getSqEndereco());
            $voEnderecoTmp->loadData($this->keepUpdateData($voEndereco));

            if (NULL == $voEndereco->getNuEndereco()) {
                $voEndereco->setNuEndereco(NULL);
            }

            if(NULL == $voEndereco->getTxComplemento()) {
                $voEndereco->setTxComplemento(NULL);
            }

            $this->getModelPersist('libcorp')->update($voEnderecoTmp);

            return $voEnderecoTmp;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }
}
