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
namespace Sgdoce\Model\Repository;

/**
 * SISICMBio
 *
 * Classe para Repository de ModeloDocumentoCampo
 *
 * @package      Model
 * @subpackage   Repository
 * @name         ModeloDocumentoCampo
 * @version      1.0.0
 * @since        2012-11-20
 */
class ModeloDocumentoCampo extends \Core_Model_Repository_Base
{
    /**
     * Obtém modelo documento assunto
     * @param \Core_Dto_Search $dtoSearch
     * @return array
     */
    public function getModeloDocAssunto(\Core_Dto_Search $dtoSearch)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('pmd.noPadraoModeloDocumento', 'c.noCampo'
                               ,'c.noColunaTabela'
                               ,'gp.sqGrupoCampo'
                               ,'pmdc.inObrigatorio')
                     ->from('app:ModeloDocumentoCampo', 'mdc')
                     ->innerJoin('mdc.sqPadraoModeloDocumentoCam', 'pmdc')
                     ->innerJoin('pmdc.sqPadraoModeloDocumento', 'pmd')
                     ->innerJoin('pmdc.sqCampo', 'c')
                     ->innerJoin('c.sqGrupoCampo', 'gp')
                     ->innerJoin('mdc.sqModeloDocumento', 'md');
        $queryBuilder->andWhere('md.sqTipoDocumento = :sqTipoDocumento')
                     ->setParameter('sqTipoDocumento', $dtoSearch->getSqTipoDocumento());
        $queryBuilder->andWhere('md.inAtivo = :inAtivo')
                    ->setParameter('inAtivo', "TRUE");
        return $queryBuilder->getQuery()->execute();
    }

    /**
     * Obtém campos do modelo documento
     * @param \Core_Dto_Search $dtoSearch
     * @return array
     */
    public function getCampoModeloDocumento (\Core_Dto_Abstract $dtoSearch)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('pmd.sqPadraoModeloDocumento,pmd.noPadraoModeloDocumento'
                             , 'c.noCampo', 'c.noColunaTabela', 'gp.sqGrupoCampo'
                             ,'pmdc.inObrigatorio')
                     ->from('app:ModeloDocumentoCampo', 'mdc')
                     ->innerJoin('mdc.sqPadraoModeloDocumentoCam', 'pmdc')
                     ->innerJoin('pmdc.sqPadraoModeloDocumento', 'pmd')
                     ->innerJoin('pmdc.sqCampo', 'c')
                     ->innerJoin('c.sqGrupoCampo', 'gp')
                     ->innerJoin('mdc.sqModeloDocumento', 'md');

        if ($dtoSearch->getSqModeloDocumento()){
            $queryBuilder->andWhere('md.sqModeloDocumento = :sqModeloDocumento');
            $queryBuilder->setParameter('sqModeloDocumento', $dtoSearch->getSqModeloDocumento());
        }else{
            $queryBuilder->andWhere('md.inAtivo = :inAtivo')
            ->setParameter('inAtivo', "TRUE");
        }
        $out = $queryBuilder->getQuery()->execute();
        $filter = new \Zend_Filter_Word_UnderscoreToCamelCase();

        $arrayField = array();
        foreach ($out as $key => $value) {
            $arrayField[$key]['sqPadraoModeloDocumento'] = $value['sqPadraoModeloDocumento'];
            $arrayField[$key]['noPadraoModeloDocumento'] = $value['noPadraoModeloDocumento'];
            $arrayField[$key]['noCampo'] = $value['noCampo'];
            $arrayField[$key]['noColunaTabela'] = lcfirst($filter->filter($value['noColunaTabela']));
            $arrayField[$key]['sqGrupoCampo'] = $value['sqGrupoCampo'];
            $arrayField[$key]['inObrigatorio'] = $value['inObrigatorio'];
        }
        return $arrayField;
    }

    /**
     * Obtém campos de um modelo
     * @param integer $sqModeloDocumento
     * @return array
     */
    public function getCamposModelo($sqModeloDocumento)
    {
        return $this->_em
                      ->createQueryBuilder()
                      ->select('c.sqCampo, c.noCampo, gc.sqGrupoCampo, gc.noGrupoCampo, pmdc.inVisivelDocumento')
                      ->from('app:ModeloDocumentoCampo', 'mdc')
                      ->innerJoin('mdc.sqPadraoModeloDocumentoCam', 'pmdc')
                      ->innerJoin('pmdc.sqCampo', 'c')
                      ->innerJoin('c.sqGrupoCampo', 'gc')
                      ->andWhere('mdc.sqModeloDocumento = :sqModeloDocumento')
                      ->setParameter('sqModeloDocumento', $sqModeloDocumento)
                      ->orderBy('gc.sqGrupoCampo')
                      ->getQuery()
                      ->execute();
    }
}
