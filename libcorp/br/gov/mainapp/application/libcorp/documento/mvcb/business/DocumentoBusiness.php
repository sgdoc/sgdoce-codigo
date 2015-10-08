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
namespace br\gov\mainapp\application\libcorp\documento\mvcb\business;
use br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\pessoa\valueObject\PessoaValueObject,
    br\gov\mainapp\application\libcorp\documento\valueObject\DocumentoValueObject,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness;

/**
  * SISICMBio
  *
  * @name DocumentoBusiness
  * @package br.gov.icmbio.sisicmbio.application.libcorp.documento.mvcb
  * @subpackage business
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * */
class DocumentoBusiness extends ParentBusiness
{
    /**
     * Salva o documento
     * @example DocumentoBusiness::saveDocumento
     * @code
     * <?php
     *     # cria filtro usado pelo tipo
     *     $voDocumento = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voDocumento = DocumentoValueObject::factory();
     *     $voDocumento->setSqPessoa(1);
     *
     *     # persist os dados
     *     $documentoLograBusiness = DocumentoBusiness::factory();
     *     $documentoLograBusiness->saveDocumento($voDocumento);
     * ?>
     * @endcode
     *
     * @param DocumentoValueObject $voDocumento
     * @return DocumentoValueObject
     * @throws BusinessException
     */
    public function saveDocumento (DocumentoValueObject $voDocumento)
    {
        try {
            $this->getModelPersist('libcorp')->save($voDocumento);
            return $voDocumento;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Exclui um documento
     * @param DocumentoValueObject $voDocumento
     * @throws BusinessException
     */
    public function deleteDocumento (DocumentoValueObject $voDocumento)
    {
        try {
            $this->getModelPersist('libcorp')->delete($voDocumento);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Retornar os dados de endereço de um Documento específicado. (<b>PessoaValueObject</b>::<i>sqPessoa</i>)
     *
     * @example DocumentoBusiness::findByPessoa
     * @code
     * <?php
     *     # cria filtro usado pelo tipo
     *     $voPessoa = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voPessoa = PessoaValueObject::factory();
     *     $voPessoa->setSqPessoa(1);
     *
     *     # efetua pesquisa
     *     $cepLograBusiness = DocumentoBusiness::factory();
     *     $cepLograBusiness->findByPessoa($voPessoa);
     * ?>
     * @endcode
     *
     * @param CepLogradouroValueObject $voCep
     * @return DataViewObject[]
     * @throws BusinessException
     */
    public function findByPessoa (PessoaValueObject $voPessoa)
    {
        return $this->_findByPessoa($voPessoa)->getAllDataViewObject();
    }

    /**
     * Metodo privado auxiliar para findByPessoa
     * @param PessoaValueObject $voPessoa
     * @throws BusinessException
     */
    private function _findByPessoa (PessoaValueObject $voPessoa)
    {
        try {
            return $this->getModelPersist('libcorp')->findByPessoa($voPessoa);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * Atualiza os dados de Documento
     *
     * @example DocumentoBusiness::updateDocumento
     * @code
     * <?php
     *     # cria filtro usado por agencia
     *     $voDocumento       = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voDocumento = EmailValueObject::factory();
     *     $voDocumento->setSqDocumento(1);
     *
     *     # efetua atualizacao
     *     $documentoBusiness = DocumentoBusiness::factory();
     *     $documentoBusiness->updateEmail($voDocumento);
     * ?>
     * @endcode
     *
     * @param DocumentoValueObject $voDocumento
     * @return DocumentoValueObject
     * @throws BusinessException
     * */
    public function updateDocumento (DocumentoValueObject $voDocumento)
    {
        try {
            $voDocumentoTmp = DocumentoBusiness::factory(NULL, 'libcorp')->find($voDocumento->getSqDocumento());
            $voDocumentoTmp->loadData($this->keepUpdateData($voDocumento));
            $this->getModelPersist('libcorp')->update($voDocumentoTmp);
            return $voDocumentoTmp;
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage(), $mExcp->getCode());
        }
    }

    /**
     * recupera um determinando documento ou uma de suas propriedades da pessoa informada
     *
     * @param DocumentoValueObject $voDocumento
     * @param string $constantName
     * @return DocumentoValueObject
     * @throws BusinessException
     * */
    public function findDocumentOrPropertyByPessoa (PessoaValueObject $valueObject, $constantName = NULL)
    {
        return $this->getModelPersist('libcorp')->findDocumentOrPropertyByPessoa($valueObject, $constantName);
    }

    /**
     * @param integer $sqDocumento
     * @return DataViewObject[]
     * */
    public function findAtributosDocumento ($sqDocumento)
    {
        // dump($sqDocumento);
        return $this->getModelPersist('libcorp')
                    ->findAtributosDocumento($sqDocumento)
                    ->getAllDataViewObject();
    }
}