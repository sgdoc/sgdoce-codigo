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
namespace br\gov\mainapp\application\libcorp\pessoaVinculo\mvcb\business;
use br\gov\sial\core\lang\Date,
    br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\pessoaVinculo\valueObject\PessoaVinculoValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name PessoaVinculoBusiness
  * @package br.gov.icmbio.sisicmbio.application.libcorp.pessoaVinculo.mvcb
  * @subpackage business
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * */
class PessoaVinculoBusiness extends ParentBusiness
{
    /**
     * Salva os dados de Pessoa Vinculo
     *
     * @example PessoaVinculoBusiness::save
     * @code
     * <?php
     *     # cria filtro usado por Pessoa Vinculo
     *     $voPessoaVinculo       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voPessoaVinculo = PessoaVinculoValueObject::factory();
     *
     *     # efetua exclusao
     *     $pessoaVinculoBusiness = DocumentoBusiness::factory();
     *     $pessoaVinculoBusiness->save($voPessoaVinculo);
     * ?>
     * @endcode
     *
     * @param PessoaVinculoValueObject $voPessoaVinculo
     * @return PessoaVinculoValueObject
     * @throws BusinessException
     */
    public function save (PessoaVinculoValueObject $voPessoaVinculo)
    {
        try {
            #sanitiza Datas
            $this->_validateSavePessoaVinculo($voPessoaVinculo);
            $this->getModelPersist('libcorp')->save($voPessoaVinculo);
            return $voPessoaVinculo;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Efetua a validação das informações contidas no VO
     * @param \br\gov\mainapp\application\libcorp\pessoaVinculo\valueObject\PessoaVinculoValueObject $voPessoaVinculo
     */
    private function _validateSavePessoaVinculo (PessoaVinculoValueObject $voPessoaVinculo)
    {
        if (trim($voPessoaVinculo->getDtFimVinculo())) {
            $dtValue = $voPessoaVinculo->getDtFimVinculo();
            if ("''" == $dtValue || '""' == $dtValue || 'NULL' == $dtValue ) {
                $voPessoaVinculo->setDtFimVinculo(NULL);
            } else {
                # Efetua sanitizacao de datas
                $dtFormat = strstr($voPessoaVinculo->getDtFimVinculo(),'-') ? 'Y-m-d' : 'd/m/Y';
                $dateFromVo = $voPessoaVinculo->getDtFimVinculo();
                $voPessoaVinculo->setDtFimVinculo(Date::factory($dateFromVo, $dtFormat)->output());
            }
        }
        if (trim($voPessoaVinculo->getDtInicioVinculo())) {
            $dtFormat = strstr($voPessoaVinculo->getDtInicioVinculo(),'-') ? 'Y-m-d' : 'd/m/Y';
            # Efetua sanitizacao de datas
            $dateFromVo = $voPessoaVinculo->getDtInicioVinculo();
            $voPessoaVinculo->setDtInicioVinculo(Date::factory($dateFromVo, $dtFormat)->output());
        }
    }

    /**
     * Exclui os dados de Pessoa Vinculo
     *
     * @example PessoaVinculoBusiness::deletePessoaVinculo
     * @code
     * <?php
     *     # cria filtro usado por Pessoa Vinculo
     *     $voPessoaVinculo       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voPessoaVinculo = PessoaVinculoValueObject::factory();
     *     $voPessoaVinculo->setSqPessoaVinculo(1);
     *
     *     # efetua exclusao
     *     $pessoaVinculoBusiness = DocumentoBusiness::factory();
     *     $pessoaVinculoBusiness->deletePessoaVinculo($voPessoaVinculo);
     * ?>
     * @endcode
     *
     * @param PessoaVinculoValueObject $voPessoaVinculo
     * @throws BusinessException
     */
    public function deletePessoaVinculo (PessoaVinculoValueObject $voPessoaVinculo)
    {
        try {
            $this->getModelPersist('libcorp')->delete($voPessoaVinculo);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Atualiza os dados de Pessoa Vinculo
     *
     * @example PessoaVinculoBusiness::updatePessoaVinculo
     * @code
     * <?php
     *     # cria filtro usado por Pessoa Vinculo
     *     $voPessoaVinculo       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voPessoaVinculo = PessoaVinculoValueObject::factory();
     *     $voPessoaVinculo->setSqPessoaVinculo(1);
     *
     *     # efetua atualizacao
     *     $pessoaVinculoBusiness = DocumentoBusiness::factory();
     *     $pessoaVinculoBusiness->updatePessoaVinculo($voPessoaVinculo);
     * ?>
     * @endcode
     *
     * @param PessoaVinculoValueObject $voPessoaVinculo
     * @return PessoaVinculoValueObject
     * @throws BusinessException
     */
    public function updatePessoaVinculo (PessoaVinculoValueObject $voPessoaVinculo)
    {
        try {
            $voTmp = self::factory(NULL, 'libcorp')->find($voPessoaVinculo->getSqPessoaVinculo());
            $voPessoaVinculo->copySaveObjectData($voTmp);
            #sanitiza Datas
            $this->_validateSavePessoaVinculo($voPessoaVinculo);
            $this->getModelPersist('libcorp')->update($voPessoaVinculo);
            return $voPessoaVinculo;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }
}