<?php

namespace Sgdoce\Model\Repository;

/**
 * SISICMBio
 *
 * Classe para Repository de Documento
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwDocumento
 * @version      1.0.0
 * @since        2012-06-26
 */
class VwDocumento extends \Core_Model_Repository_Base
{
    public function listGrid(\Core_Dto_Abstract $dto)
    {
        $_qb   = $this->_em->createQueryBuilder();
        $query = $_qb->select('
            d.sqDocumento,
            d.txValor,
            td.sqTipoDocumento,
            td.noTipoDocumento,
            atd.sqAtributoTipoDocumento,
            p.sqPessoa,
            ps.sqPessoaSgdoce,
            acd.sqAnexoComprovanteDocumento,
            acd.deCaminhoImagem
        ')
            ->from($this->_entityName, 'd')
            ->innerJoin('d.sqAtributoTipoDocumento', 'atd')
            ->innerJoin('atd.sqAtributoDocumento', 'ad')
            ->innerJoin('atd.sqTipoDocumento', 'td')
            ->innerJoin('d.sqPessoa', 'p')
            //->leftJoin('p.sqPessoaSgdoce', 'ps')
            ->leftJoin('p.sqPessoaCorporativo', 'ps', 'WITH',
                $_qb->expr()->andX()
                    ->add($_qb->expr()->eq('ps.sqPessoaCorporativo', 'p.sqPessoa'))
            )
            ->leftJoin('ps.sqAnexoComprovanteDocumento', 'acd', 'WITH',
                $_qb->expr()->andX()
                    ->add($_qb->expr()->eq('acd.sqTipoDocumento', 'td.sqTipoDocumento'))
                    ->add($_qb->expr()->eq('acd.sqPessoaSgdoce', ':sqPessoaSgdoce'))
            )
            ->where('p.sqPessoa = :sqPessoa')
            ->andWhere(
                $_qb->expr()->in('ad.sqAtributoDocumento', ':sqAtributoDocumento')
            )
            ->setParameter('sqPessoa', $dto->getSqPessoaFisica() ? : null)
            ->setParameter('sqPessoaSgdoce', $dto->getSqPessoaSgdoce() ? : null)
            ->setParameter('sqAtributoDocumento', array(
                \Core_Configuration::getCorpAtributoDocumentoNumero(),
            ));

        return $query;
    }

    public function findDocumentoPessoaFisica($sqPessoa)
    {
        $_qb   = $this->_em->createQueryBuilder();
        $query = $_qb->select('d,atd,ad,td')
                ->from($this->_entityName, 'd')
                ->innerJoin('d.sqAtributoTipoDocumento', 'atd')
                ->innerJoin('atd.sqAtributoDocumento', 'ad')
                ->innerJoin('atd.sqTipoDocumento', 'td')
                ->andWhere('d.sqPessoa = :sqPessoa')
                ->setParameter('sqPessoa',$sqPessoa)
                ->andWhere('atd.sqAtributoDocumento = :sqAtributoDocumento')
                ->setParameter('sqAtributoDocumento', '1');

        return $query->getQuery()->execute();
    }

    public function getDocumento($dto)
    {
        $_qb   = $this->_em->createQueryBuilder();
        $query = $_qb->select('d, atd, ad, td')
            ->from($this->_entityName, 'd')
            ->innerJoin('d.sqAtributoTipoDocumento', 'atd')
            ->innerJoin('atd.sqAtributoDocumento', 'ad')
            ->innerJoin('atd.sqTipoDocumento', 'td')
            ->andWhere('d.sqPessoa = :sqPessoa')
            ->andWhere('td.sqTipoDocumento = :sqTipoDocumento')
            ->setParameter('sqPessoa', $dto->getSqPessoa())
            ->setParameter('sqTipoDocumento', $dto->getSqTipoDocumento());

        return $query->getQuery()->execute();
    }
}