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

namespace Sgdoce\Model\Repository;

/**
 * SISICMBio
 *
 * Classe para Repository de Sistema Telefone
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Telefone
 * @version      1.0.0
 * @since        2012-06-26
 */

class VwTelefone extends \Core_Model_Repository_Base
{
    private $_enName = 'app:VwTelefone';

    /**
     * método para pesquisa de unidades organizacinais para combo
     * @param array $params
     * @return array $out
     */
    public function getDadosTelefone (\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->getEntityManager()
        ->createQueryBuilder()
        ->select('t')
        ->from($this->_enName, 't')
        ->andWhere('t.sqPessoa = :sqPessoa')
        ->setParameter('sqPessoa', $dto->getSqPessoa());

        $result = $queryBuilder->getQuery()->execute();
        if(!$result){
            $result[] = new \Sgdoce\Model\Entity\VwTelefone();
        }
        return $result[0];
    }

    public function listGrid(\Core_Dto_Search $dto)
    {
        return $this->_em->createQueryBuilder()
                        ->select('p.sqPessoa, t.sqTelefone, tt.noTipoTelefone, t.nuDdd, t.nuTelefone')
                        ->from('app:VwTelefone', 't')
                        ->innerJoin('t.sqTipoTelefone', 'tt')
                        ->innerJoin('t.sqPessoa', 'p')
                        ->where('p.sqPessoa = :sqPessoa')
                        ->setParameter('sqPessoa', $dto->getSqPessoa());
    }

}