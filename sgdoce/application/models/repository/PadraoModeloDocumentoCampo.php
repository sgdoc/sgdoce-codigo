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
 * Classe para Repository de PadraoModeloDocumentoCampo
 *
 * @package      Model
 * @subpackage   Repository
 * @name         PadraoModeloDocumentoCampo
 * @version      1.0.0
 * @since        2012-11-20
 */
class PadraoModeloDocumentoCampo extends \Core_Model_Repository_Base
{
    /**
     * Método de consulta de campos dos padrões de documentos campos para preenchimento do combo
     * @return array
     */
    public function listItensPadraoModeloDocCampos($dtoSearch)
    {
        if ($dtoSearch->getSqModeloDocumento()){
            $sql = " SELECT pmdc.sq_padrao_modelo_documento_cam,
            marcado.sq_padrao_modelo_documento checked,
            c.sq_campo,c.no_campo,gc.sq_grupo_campo,gc.no_grupo_campo,pmd.no_padrao_modelo_documento
            ,pmdc.in_visivel_documento
            FROM campo c
            INNER JOIN grupo_campo gc
                        ON (c.sq_grupo_campo = gc.sq_grupo_campo)
            INNER JOIN padrao_modelo_documento_campo pmdc
                        ON (pmdc.sq_campo = c.sq_campo)
            INNER JOIN padrao_modelo_documento pmd
                        ON (pmd.sq_padrao_modelo_documento = pmdc.sq_padrao_modelo_documento)
            LEFT JOIN
                        (SELECT pmdc.sq_padrao_modelo_documento_cam, pmdc.sq_padrao_modelo_documento
                        FROM modelo_documento md
                        INNER JOIN modelo_documento_campo mdc
                            ON (md.sq_modelo_documento = mdc.sq_modelo_documento)
                        INNER JOIN padrao_modelo_documento_campo pmdc
                            ON (mdc.sq_padrao_modelo_documento_cam = pmdc.sq_padrao_modelo_documento_cam)
                        WHERE md.sq_modelo_documento = {$dtoSearch->getSqModeloDocumento()}) marcado
                        ON (marcado.sq_padrao_modelo_documento_cam = pmdc.sq_padrao_modelo_documento_cam
                            )
            WHERE pmd.sq_padrao_modelo_documento = {$dtoSearch->getSqPadraoModeloDocumento()}
            ORDER BY pmdc.nu_ordem, gc.sq_grupo_campo, c.sq_campo ASC
            ";
            $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
            $rsm->addScalarResult('sq_padrao_modelo_documento_cam', 'sqPadraoModeloDocumentoCam', 'string');
            $rsm->addScalarResult('checked', 'checked', 'string');
            $rsm->addScalarResult('no_padrao_modelo_documento', 'noPadraoModeloDocumento', 'string');
            $rsm->addScalarResult('no_campo', 'noCampo', 'string');
            $rsm->addScalarResult('no_grupo_campo', 'noGrupoCampo', 'string');
            $rsm->addScalarResult('sq_grupo_campo', 'sqGrupoCampo', 'string');
            $rsm->addScalarResult('in_visivel_documento', 'inVisivelDocumento', 'boolean');
            $result = $this->_em->createNativeQuery($sql, $rsm)->execute();
            return $result;
        }else{
             $queryBuilder = $this->_em
                                  ->createQueryBuilder()
                                  ->select(
                                          "pmdc.sqPadraoModeloDocumentoCam,
                                           pmd.noPadraoModeloDocumento,
                                           c.noCampo,
                                           gc.noGrupoCampo,
                                           gc.sqGrupoCampo,
                                           '' checked",
                                          'pmdc.nuOrdem,
                                           pmdc.inVisivelDocumento'
                                           )
                                  ->from('app:PadraoModeloDocumentoCampo', 'pmdc')
                                  ->innerJoin('pmdc.sqPadraoModeloDocumento', 'pmd')
                                  ->innerJoin('pmdc.sqCampo', 'c')
                                  ->innerJoin('c.sqGrupoCampo', 'gc')
                                  ->orderBy('pmdc.nuOrdem, gc.sqGrupoCampo, c.sqCampo', 'ASC');

             if($dtoSearch->getSqPadraoModeloDocumento()) {
                     $queryBuilder->andWhere('pmd.sqPadraoModeloDocumento = :padraoModeloDoc')
                                  ->setParameter('padraoModeloDoc', $dtoSearch->getSqPadraoModeloDocumento());
             }

             $out = $queryBuilder->getQuery()->getArrayResult();

             return $out;
        }
     }
}
