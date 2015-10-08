<?php
/**
 * Copyright 2012 do ICMBio
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */
namespace Etiqueta\Service;
/**
 * Classe para Service de Lote de Etiqueta
 *
 * @package  Etiqueta
 * @category Service
 * @name     LoteEtiqueta
 */

class LoteEtiqueta extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * Variavel para receber o nome da entidade
     *
     * @var string
     * @access protected
     * @name $_entityName
     */
    protected $_entityName = 'app:LoteEtiqueta';

    /**
     * Obtém lista de lotes de etiqueta
     *
     * @param \Core_Dto_Search $search
     * @return json
     */
    public function listGrid(\Core_Dto_Search $search)
    {
        $repository = $this->_getRepository();
        $result     = $repository->searchPageDto('listGrid', $search);

        return $result;
    }

    public function getUserPrintedDigital(\Core_Dto_Search $search)
    {
        $result = array(
            'user' => false,
            'date' => null
        );

        $eLoteEtiqueta = $this->_getRepository()
                             ->find($search->getSqLoteEtiqueta());
        if ($eLoteEtiqueta->getSqUsuario()) {
            $result['user'] = $eLoteEtiqueta->getSqUsuario()->getSqPessoa()->getNoPessoa();
            $result['date'] = $eLoteEtiqueta->getDtImpressao()->get('dd/MM/yyyy');
        }

        return $result;
    }

    public function saveUserPrintedDigital($sqLoteEtiqueta, $sqUsuario)
    {
        $eLoteEtiqueta = $this->_getRepository()->find($sqLoteEtiqueta);
        $eVwUsuario = $this->_getRepository('app:VwUsuario')->find($sqUsuario);

        $eLoteEtiqueta->setSqUsuario($eVwUsuario);
        $eLoteEtiqueta->setDtImpressao(new \Zend_Date());

        $this->getEntityManager()->persist($eLoteEtiqueta);
        $this->getEntityManager()->flush($eLoteEtiqueta);
    }
}