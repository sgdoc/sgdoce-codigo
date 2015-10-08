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
namespace br\gov\mainapp\application\libcorp\atributoTipoDocumento\mvcb\business;
use br\gov\sial\core\mvcb\model\exception\ModelException,
    br\gov\sial\core\mvcb\business\exception\BusinessException,
    br\gov\mainapp\application\libcorp\parent\mvcb\business\BusinessAbstract as ParentBusiness,
    br\gov\mainapp\application\libcorp\atributoTipoDocumento\valueObject\AtributoTipoDocumentoValueObject;

/**
  * SISICMBio
  *
  * @name AtributoTipoDocumentoBusiness
  * @package br.gov.icmbio.sisicmbio.application.libcorp.atributoTipoDocumento.mvcb
  * @subpackage business
  * @author J. Augusto <augustowebd@gmail.com>
  * @version $Id$
  * */
class AtributoTipoDocumentoBusiness extends ParentBusiness
{
    /**
     * Retornar a lista de atributos do documento (<b>AtributoTipoDocumentoValueObject</b>::<i>sqAtributoDocumento</i>)
     *
     * @example AtributoTipoDocumentoBusiness::findByTipo
     * @code
     * <?php
     *     # cria filtro usado pelo tipo
     *     $voAttrTipoDocumento = ValueObjectAbstract::factory('fullnamespace');
     *     # outra forma de utilizar
     *     # $voAttrTipoDocumento = AtributoTipoDocumentoValueObject::factory();
     *     $voAttrTipoDocumento->setSqAtributoDocumento(1);
     *
     *     # efetua pesquisa
     *     $attrTipoDocBusiness = AtributoTipoDocumentoBusiness::factory();
     *     $attrTipoDocBusiness->findByTipo($voAttrTipoDocumento);
     * ?>
     * @endcode
     *
     * @param AtributoTipoDocumentoValueObject $voAttrTipoDocumento
     * @return DataViewObject[]
     * @throws BusinessException
     */
    public function findByTipo (AtributoTipoDocumentoValueObject $voAttrTipoDocumento)
    {
        return $this->_findByTipo ($voAttrTipoDocumento)->getAllDataViewObject();
    }

    /**
     * Metodo privado auxiliar para findByTipo
     * @param AtributoTipoDocumentoValueObject $voAttrTipoDocumento
     * @throws BusinessException
     */
    private function _findByTipo (AtributoTipoDocumentoValueObject $voAttrTipoDocumento)
    {
        try {
            return $this->getModelPersist('libcorp')->findByTipo($voAttrTipoDocumento);
        } catch (ModelException $mExcp) {
            throw new BusinessException($mExcp->getMessage());
        }
    }
}