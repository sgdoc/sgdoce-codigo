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
 * Classe para Repository de Motivacao
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Motivacao
 * @version      1.0.0
 * @since        2012-11-20
 */
class Motivacao extends \Core_Model_Repository_Base
{
    /**
     * Deleta motivação
     * @param \Core_Dto_Search $dto
     * @return boolean
     */
    public function deleteMotivacao($dto)
    {
    	$queryBuilder = $this->_em->createQueryBuilder()
        ->delete('app:Motivacao', 'm')
        ->andWhere('m.sqArtefato = :sqArtefato')
        ->setParameter('sqArtefato', $dto->getSqArtefato()->getSqArtefato() )
        ->andWhere('m.sqPessoaUnidadeOrg = :sqPessoaUnidadeOrg')
        ->setParameter('sqPessoaUnidadeOrg', $dto->getSqPessoaUnidadeOrg()->getSqPessoaUnidadeOrg() );

    	$out = $queryBuilder->getQuery()->execute();

        return $out;
    }

    /**
     * Deleta motivação
     * @param \Core_Dto_Search $dto
     * @return boolean
     */
    public function deleteTodaMotivacao($sqArtefato)
    {
    	$queryBuilder = $this->_em->createQueryBuilder()
    	->delete('app:Motivacao', 'm')
    	->andWhere('m.sqArtefato = :sqArtefato')
    	->setParameter('sqArtefato', $sqArtefato);

    	$out = $queryBuilder->getQuery()->execute();

    	return $out;
    }

    /**
     * Obtém pessoa artefato assinatura
     * @param  $dto
     * @return array
     */
    public function getPessoaArtefatoAssinatura($dto)
    {
        $query = $this->_em->createQueryBuilder()
                      ->select('p.noPessoa, p.noProfissao, p.noUnidadeOrg, tm.noTipoMotivacao, tm.sqTipoMotivacao,
                                m.deMotivacao')
                      ->from('app:Motivacao', 'm')
                      ->innerJoin('m.sqTipoMotivacao', 'tm')
                      ->innerJoin('m.sqPessoa', 'p')
                      ->innerJoin('p.sqPessoaFuncao', 'pf')
                      ->leftJoin('p.sqTratamentoVocativo', 'tv')
                      ->leftJoin('tv.sqTratamento', 't')
                      ->leftJoin('tv.sqVocativo', 'v')
                      ->leftJoin('p.sqPessoaCorporativo', 'pc')
                      ->leftJoin('p.sqMunicipioEndereco', 'cid')
                      ->leftJoin('cid.sqEstado', 'est')
                      ->andWhere('p.sqArtefato = :sqArtefato')
                      ->setParameter('sqArtefato', $dto->getSqArtefato())
                      ->andWhere('p.sqPessoaFuncao = :sqPessoaFuncao')
                      ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoAssinatura())
                      ->getQuery()
                      ->execute();

        if(empty($query)){
            return NULL;
        }
        return $query;
    }
}
