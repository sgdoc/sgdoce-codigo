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
 * Classe para Repository de SolicitacaoMigracaoImagem
 *
 * @package      Model
 * @subpackage   Repository
 * @name         SolicitacaoMigracaoImagem
 * @version      1.0.0
 * @since        2015-07-02
 */
class SolicitacaoMigracaoImagem extends \Core_Model_Repository_Base
{

    public function findRequestsToProcess($limit=100)
    {
        $config       = \Core_Registry::get('configs');

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder
                ->select('smi')
                ->from('app:SolicitacaoMigracaoImagem', 'smi')
                ->innerJoin('smi.sqArtefato', 'a')
                ->innerJoin('smi.sqUnidadeOrg', 'un')
                ->innerJoin('smi.sqPessoa', 'p')

                ->andWhere('smi.stProcessado = FALSE')
                ->andWhere($queryBuilder->expr()->lt('smi.inTentativa', $config['migration']['qtdeTentativa']))
                ->orderBy('smi.dtSolicitacao')
                ->setMaxResults($limit);
        
        return $queryBuilder->getQuery()->execute();
    }

    /**
     * @param \Core_Dto_Search $dto
     */
    public function findRequest(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder
                ->select('smi')
                ->from('app:SolicitacaoMigracaoImagem', 'smi')
                ->andWhere($queryBuilder->expr()->eq("smi.sqArtefato", $dto->getSqArtefato()));

        return $queryBuilder->getQuery()->execute();
    }

}
