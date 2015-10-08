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
use Doctrine\Common\Util\Debug;

/**
 * SISICMBio
 *
 * Classe para Repository de AnexoArtefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         AnexoArtefato
 * @version      1.0.0
 * @since        2012-11-20
 */
class AnexoArtefato extends \Core_Model_Repository_Base
{

    /**
     *
     * @param \Core_Dto_Abstract $dto
     * @return array
     */
    public function listGridAnexos(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('af.sqArtefato')
            ->from('app:ArtefatoVinculo', 'av')
            ->innerJoin('av.sqArtefatoFilho', 'af')
            ->andWhere('av.sqTipoVinculoArtefato != :tipoVinculo')
            ->andWhere('av.sqArtefatoPai = :sqArtefatoPai')
            ->andWhere('av.dtRemocaoVinculo is null')
            ->setParameters(array('tipoVinculo'   => \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia(),
                                  'sqArtefatoPai' => $dto->getSqArtefato()));
        $arrSqArtefatoVinculo = $queryBuilder->getQuery()->execute();

        $arrSqArtefato = array((int)$dto->getSqArtefato());
        if (!empty($arrSqArtefatoVinculo)) {
            foreach ($arrSqArtefatoVinculo as $artefatoVinculo) {
                $arrSqArtefato[] = $artefatoVinculo['sqArtefato'];
            }
        }
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('aa,a')
                     ->from('app:AnexoArtefato', 'aa')
                     ->innerJoin('aa.sqArtefato','a')
                     ->andWhere('aa.sqArtefato in(:sqArtefato)')
                     ->setParameters(array('sqArtefato' => $arrSqArtefato))
                     ->orderBy('aa.nuPagina');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    /**
     * método que retorna dados para grid de interessados nos Artefato
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGridImagem(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                        ->select('aa')
                        ->from('app:AnexoArtefato', 'aa')
                        ->andWhere('aa.sqArtefato = :sqArtefato')
                        ->setParameter('sqArtefato', $dto->getSqArtefato())
                        ->orderBy('aa.nuPagina');
        return $queryBuilder;
    }
}