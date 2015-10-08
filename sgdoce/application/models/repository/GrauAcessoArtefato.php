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
 * Classe para Repository de GrauAcessoArtefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         GrauAcessoArtefato
 * @version      1.0.0
 * @since        2012-11-20
 */
class GrauAcessoArtefato extends \Core_Model_Repository_Base
{
    /**
     * Método que verifica a existência de um grau de acesso do artefato
     * @param \Sgdoce\Model\Entity\Artefato $artefato
     * @param \Sgdoce\Model\Entity\GrauAcesso $grauAcesso
     * @return boolean
     */
    public function hasGrauAcessoArtefato(\Sgdoce\Model\Entity\Artefato $artefato
                                            ,\Sgdoce\Model\Entity\GrauAcesso $grauAcesso)
    {
        $return = FALSE;
        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select('gaa')
                             ->from('app:GrauAcessoArtefato', 'gaa')
                             ->andWhere('gaa.sqArtefato = :sqArtefato')
                             ->setParameter(':sqArtefato', $artefato->getSqArtefato())
                             ->andWhere('gaa.stAtivo = TRUE');

        $result = $queryBuilder->getQuery()->execute();
        if (count($result) > 0) {
            if ($result[0]->getSqGrauAcesso()->getSqGrauAcesso() != $grauAcesso->getSqGrauAcesso()) {
                $queryBuilder = $this->_em
                                     ->createQueryBuilder()
                                     ->update('app:GrauAcessoArtefato', 'gaa')
                                     ->set('gaa.stAtivo', "FALSE")
                                     ->andWhere('gaa.sqGrauAcessoArtefato = :sqGrauAcessoArtefato')
                                     ->setParameter(':sqGrauAcessoArtefato', $result[0]->getSqGrauAcessoArtefato());
                $queryBuilder->getQuery()->execute();
                $return = TRUE;
            }
        } else {
            $return = TRUE;
        }
        return $return;
    }

    /**
     * Método que retorna os registros do grau de acesso do artefato que estiveram ativos
     * @param \Core_Dto_Search $dtoSearch
     * @return array
     */
    public function getGrauAcessoArtefato(\Core_Dto_Search $dtoSearch)
    {
        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select('gaa')
                             ->from('app:GrauAcessoArtefato', 'gaa')
                             ->andWhere("gaa.sqArtefato = {$dtoSearch->getSqArtefato()}")
                             ->andWhere('gaa.stAtivo = TRUE');

        $result = $queryBuilder->getQuery()->execute();

        if (count($result) > 0) {
            $result = $result[0];
        } else {
            $result = NULL;
        }
        return $result;
    }
}
