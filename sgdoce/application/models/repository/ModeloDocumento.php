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

use Sarr\Model\Entity as Entities;

/**
 * SISICMBio
 *
 * Classe para Repository de ModeloDocumento
 *
 * @package      Model
 * @subpackage   Repository
 * @name         ModeloDocumento
 * @version      1.0.0
 * @since        2012-11-20
 */
class ModeloDocumento extends \Core_Model_Repository_Base
{
    /**
     * Procura modelo de documentos de acordo com os parâmetros
     * @param  array $params Parâmetros da requisição
     * @return mixed Query Builder
     */
    public function listGrid(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select(
                                      'DISTINCT
                                      md.sqModeloDocumento,
                                      pmd.sqPadraoModeloDocumento,
                                      pmd.noPadraoModeloDocumento,
                                      td.noTipoDocumento,
                                      a.txAssunto,
                                      md.inAtivo'
                                     )
                             ->from('app:ModeloDocumento', 'md')
                             ->innerJoin('md.sqModeloDocumentoCampo', 'mdc')
                             ->innerJoin('md.sqTipoDocumento', 'td')
                             ->leftJoin('md.sqAssunto', 'a')
                             ->innerJoin('mdc.sqPadraoModeloDocumentoCam', 'pmdc')
                             ->innerJoin('pmdc.sqPadraoModeloDocumento', 'pmd')
                             ->andWhere('md.inAtivo = TRUE')
                             ->orderBy('pmd.noPadraoModeloDocumento, td.noTipoDocumento, a.txAssunto', 'ASC');

        if ($dto->hasSqPadraoModeloDocumento() && $dto->getSqPadraoModeloDocumento()) {
            $queryBuilder->andWhere('pmdc.sqPadraoModeloDocumento = :PadraoModeloDoc')
                         ->setParameter('PadraoModeloDoc', $dto->getSqPadraoModeloDocumento(), 'integer');
        }

        if ($dto->hasSqAssunto() && $dto->getSqAssunto()) {
            $queryBuilder->andWhere('md.sqAssunto = :assunto')
                         ->setParameter('assunto', $dto->getSqAssunto(), 'integer');
        }

        if ($dto->hasSqTipoDocumento() && $dto->getSqTipoDocumento()) {
            $queryBuilder->andWhere('md.sqTipoDocumento = :tipoDoc')
                         ->setParameter('tipoDoc', $dto->getSqTipoDocumento(), 'integer');
        }

        return $queryBuilder;
    }

    /**
     * Procura modelo de documentos cujo sqAssunto seja NULL
     * @param  \Core_Dto_Search $dtoSearch
     * @return array
     */
    public function getModeloDocAssunto(\Core_Dto_Search $dtoSearch)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
        ->select('md.sqModeloDocumento')
        ->from('app:ModeloDocumento', 'md')
        ->andWhere('md.sqTipoDocumento = :sqTipoDocumento')
        ->setParameter('sqTipoDocumento', $dtoSearch->getSqTipoDocumento())
         ->andWhere('md.sqAssunto is null');
         $queryBuilder->andWhere('md.inAtivo = TRUE');
         $out = $queryBuilder->getQuery()->execute();

         return count($out) > 0 ? $out[0] : $out;
    }

    /**
     * Verifica a existencia do cadastro de um modelo
     * @param  \Core_Dto_Search $dtoSearch,$result
     * @return array
     */
    public function hasModeloDocumentoCadastrado(\Core_Dto_Search $dtoSearch,$result = FALSE)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                                  ->select('md.sqModeloDocumento')
                                  ->from('app:ModeloDocumento', 'md')
                                  ->andWhere('md.sqTipoDocumento = :sqTipoDocumento')
                                  ->andWhere('md.inAtivo = TRUE')
                                  ->setParameter('sqTipoDocumento', $dtoSearch->getSqTipoDocumento())
                                  ->andWhere('md.sqAssunto = :sqAssunto')
                                  ->setParameter('sqAssunto', $dtoSearch->getSqAssunto());
        $out = $queryBuilder->getQuery()->execute();

        if ($result){
            return (count($out) > 0) ? $out[0] : $this->getModeloDocAssunto($dtoSearch);
        }else{
            return (count($out) > 0) ? count($out[0]) : count($this->getModeloDocAssunto($dtoSearch));
        }
    }

    /**
     * Deleta um modelo
     * @param  $dto
     * @return array
     */
    public function delete($dto)
    {
        $out = NULL;
        foreach($dto as $dtoAux){

            $queryBuilder = $this->_em->createQueryBuilder()
                                      ->delete('app:ModeloDocumentoCampo', 'mdc')
                                      ->where('mdc.sqModeloDocumentoCampo = :sqModeloDocCam')
                                      ->setParameter('sqModeloDocCam', $dtoAux->getSqModeloDocumentoCampo());

            $out = $queryBuilder->getQuery()->execute();
        }
        return $out;
    }

    /**
     * Obtém um modelo
     * @param  \Core_Dto_Abstract $dto
     * @return array
     */
    public function findModelo(\Core_Dto_Abstract $dto)
    {
        $queryBuilder = $this->_em
        ->createQueryBuilder()
                  ->select('  DISTINCT
                              md.sqModeloDocumento,
                              pmd.sqPadraoModeloDocumento,
                              pmd.noPadraoModeloDocumento,
                              td.sqTipoDocumento,
                              td.noTipoDocumento,
                              a.sqAssunto,
                              a.txAssunto,
                              md.inAtivo,
                              ptd.sqPosicaoTipoDocumento'
        )
        ->from('app:ModeloDocumento', 'md')
        ->innerJoin('md.sqModeloDocumentoCampo', 'mdc')
        ->innerJoin('md.sqTipoDocumento', 'td')
        ->leftJoin('md.sqAssunto', 'a')
        ->leftJoin('md.sqPosicaoTipoDocumento', 'ptd')
        ->innerJoin('mdc.sqPadraoModeloDocumentoCam', 'pmdc')
        ->innerJoin('pmdc.sqPadraoModeloDocumento', 'pmd')
        ->andWhere('md.sqModeloDocumento = :sqModeloDocumento')
            ->setParameter('sqModeloDocumento', $dto->getSqModeloDocumento())
        ->orderBy('pmd.noPadraoModeloDocumento, td.noTipoDocumento, a.txAssunto', 'ASC');
        return $queryBuilder->getQuery()->getSingleResult();
    }

    /**
     * Obtém um modelo
     * @param  \Core_Dto_Abstract $dto
     * @return array
     */
    public function validaDocumento($entity,$modelo = FALSE)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
        ->select('md.sqModeloDocumento')
        ->from('app:ModeloDocumento', 'md')
        ->andWhere('md.sqTipoDocumento = :sqTipoDocumento')
        ->setParameter('sqTipoDocumento', $entity->getSqTipoDocumento()->getSqTipoDocumento());
        if($entity->getSqAssunto()->getSqAssunto()){
            $queryBuilder->andWhere('md.sqAssunto = :sqAssunto')
            ->setParameter('sqAssunto', $entity->getSqAssunto()->getSqAssunto());
        }else{
            $queryBuilder->andWhere('md.sqAssunto is null');
        }
        $queryBuilder->andWhere('md.inAtivo = TRUE');
        $out = $queryBuilder->getQuery()->execute();
        if($modelo){
            return $out;
        }
        return count($out);
    }

    /**
     * Atualiza um modelo existente
     * @param  $entity
     * @return integer
     */
    public function updateExistente($entity)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
        ->update('app:ModeloDocumento', 'md')
        ->set('md.inAtivo', 'FALSE')
        ->andWhere('md.sqTipoDocumento = :sqTipoDocumento')
        ->setParameter('sqTipoDocumento', $entity->getSqTipoDocumento());
        if($entity->getSqAssunto()->getSqAssunto()){
            $queryBuilder->andWhere('md.sqAssunto = :sqAssunto')
            ->setParameter('sqAssunto', $entity->getSqAssunto());
        }else{
            $queryBuilder->andWhere('md.sqAssunto is null');
        }
        $out = $queryBuilder->getQuery()->execute();

        return count($out);
    }

    /**
     * Deleta um modelo
     * @param  \Core_Dto_Search $dtoSearch
     * @return integer
     */
    public function deleteModelo(\Core_Dto_Search $dtoSearch)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
        ->update('app:ModeloDocumento', 'md')
        ->set('md.inAtivo', 'FALSE')
        ->andWhere('md.sqModeloDocumento = :sqModeloDocumento')
        ->setParameter('sqModeloDocumento', $dtoSearch->getSqModeloDocumento());
        $out = $queryBuilder->getQuery()->execute();
        return count($out);
    }

}
