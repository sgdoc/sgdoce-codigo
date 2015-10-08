<?php

/*
 * Copyright 2012 ICMBio
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

namespace Sica\Model\Repository;

/**
 * SISICMBio
 *
 * Classe para Repository de Endereco
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Menu
 * @version	 1.0.0
 */
class Endereco extends \Sica_Model_Repository
{

    /**
     * Realiza a pesquisa da grid
     * @param \Core_Dto_Abstract $dto
     */
    public function listGrid(\Core_Dto_Abstract $dto)
    {
        return $this->_em->createQueryBuilder()
                        ->select(
                                'p.sqPessoa,
                                e.sqEndereco,
                                e.sqCep,
                                te.noTipoEndereco,
                                e.txEndereco,
                                e.nuEndereco,
                                e.noBairro,
                                m.noMunicipio,
                                es.noEstado'
                        )
                        ->from('app:Endereco', 'e')
                        ->innerJoin('e.sqPessoa', 'p')
                        ->innerJoin('e.sqTipoEndereco', 'te')
                        ->innerJoin('e.sqMunicipio', 'm')
                        ->innerJoin('m.sqEstado', 'es')
                        ->where('p.sqPessoa = :sqPessoa')
                        ->setParameter('sqPessoa', $dto->getSqPessoa());
    }

    /**
     * Retorna o endereco conforme cep
     * @param type $cep
     * @return type
     */
    public function searchCep($cep)
    {
        $fields = array(
            'm.sqMunicipio',
            'p.sqPais',
            'e.sqEstado',
            'c.coCep',
            'c.noBairro',
            'c.noLogradouro'
        );

        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select($fields)
                ->from('app:Cep', 'c')
                ->innerJoin('c.sqMunicipio', 'm')
                ->innerJoin('m.sqEstado', 'e')
                ->innerJoin('e.sqPais', 'p')
                ->where('c.coCep = :coCep')
                ->setParameter('coCep', $cep)
                ->setMaxResults(1);

        $result = $queryBuilder->getQuery()->getResult();

        return $result ? $result[0] : array();
    }

}
