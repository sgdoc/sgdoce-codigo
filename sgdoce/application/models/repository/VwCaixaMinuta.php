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
 * Classe para Repository de VwCaixaMinuta
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwCaixaMinuta
 * @version      1.0.0
 * @since        2013-02-13
 */
class VwCaixaMinuta extends \Core_Model_Repository_Base
{
    /**
    * Constante para receber o tipo de visualizacao da caixa de minuta recebidas
    * @var integer
    * @name   TIPO_VISUALIZACAO_RECEBIDA
    */
    const TIPO_VISUALIZACAO_RECEBIDA = 1;

    /**
     * Constante para receber o tipo de visualizacao da caixa de minuta enviadas
     * @var integer
     * @name   TIPO_VISUALIZACAO_ENVIADA
     */
    const TIPO_VISUALIZACAO_ENVIADA = 2;

    /**
     * Constante para receber o tipo de visualizacao da caixa de minuta produzidas
     * @var integer
     * @name   TIPO_VISUALIZACAO_PRODUZIDA
     */
    const TIPO_VISUALIZACAO_PRODUZIDA = 3;

    /**
     * Constante para receber o tipo de visualizacao da caixa de minuta em acompanhamento
     * @var integer
     * @name   TIPO_VISUALIZACAO_EM_ACOMPANHAMENTO
     */
    const TIPO_VISUALIZACAO_EM_ACOMPANHAMENTO = 4;

    /**
     * Constante para receber o valor zero
     * @var    integer
     * @name   ZER
     */
    const ZER  = 0;

    /**
     * Constante para receber o valor um
     * @var    integer
     * @name   UNIC
     */
    const UNIC = 1;

    /**
     * método que retorna dados para grid da caixa de minutas
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGridMinutas(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder ()
                                    ->select(
                                            'vcm.sqArtefato',
                                            'vcm.dataCriacao',
                                            'vcm.tipo',
                                            'vcm.origem',
                                            'vcm.assunto',
                                            'vcm.autor',
                                            'vcm.prazo',
                                            'vcm.nuDiasPrazo',
                                            'vcm.inDiasCorridos',
                                            'vcm.status',
                                            'vcm.sqStatusArtefato',
                                            'vcm.sqPessoa',
                                            'vcm.sqOcorrencia',
                                            'vcm.sqHistoricoArtefato',
                                            'td.sqTipoDocumento',
                                            'ta.sqTipoArtefato',
                                            'ass.sqAssunto'
                                            )
                                    ->addSelect('(' . $this->subQueryInAssinatura($dto->getSqPessoa()) .
                                                                                             ') inAssinaturaArtefato')
                                    ->addSelect('(' . $this->subQueryQtdAssinantesArtefato() .
                                                                                             ') qtdAssinantesArtefato')
                                    ->addSelect('(' . $this->subQueryInCampoAssinatura() .
                                                                                             ') inCampoAssinatura')
                                    ->addSelect('(' . $this->subQueryJaAssinou($dto->getSqPessoa()) .
                                                                                             ') assinada');

                                    $reultSelectTableQuery = $this->selectTableQuery($queryBuilder, $dto);

                                    $queryBuilder->innerJoin('vcm.sqArtefatoArtefato', 'a')
                                                 ->innerJoin('a.sqTipoArtefatoAssunto', 'taa')
                                                 ->innerJoin('taa.sqTipoArtefato', 'ta')
                                                 ->innerJoin('taa.sqAssunto', 'ass')
                                                 ->innerJoin('a.sqTipoDocumento', 'td');

                                    // se a query for para caixa em acompanhamento
                                    if($reultSelectTableQuery){
                                        $queryBuilder->addSelect('vcm.noOcorrencia');
                                    }

                                    $this->addWhere($queryBuilder,$dto);

        return $queryBuilder;
    }

    /**
    * Retorna subquery que verifica se a pessoa que esta logada está na assinatura da minuta ou não
    * @param integer $sqUsuario
    * @return \Doctrine\ORM\QueryBuilder
    */
    public function subQueryInAssinatura($sqUsuario)
    {
        return $this->_em->createQueryBuilder ()
                    ->select('distinct sqvwp.sqPessoa')
                    ->from('app:PessoaAssinanteArtefato', 'sqpaa')
                    ->innerJoin('sqpaa.sqPessoaUnidadeOrg', 'puo')
                    ->innerJoin('puo.sqPessoaSgdoce', 'sqps')
                    ->innerJoin('sqpaa.sqArtefato', 'sqa')
                    ->innerJoin('sqps.sqPessoaCorporativo', 'sqvwp')
                    ->andWhere('sqa.sqArtefato = a.sqArtefato')
                    ->andWhere('sqvwp.sqPessoa = :sqPessoa')
                    ->setParameter('sqPessoa', $sqUsuario)
                    ->getQuery()
                    ->getDQL();
    }

    /**
     * Retorna subquery que verifica se a pessoa que esta logada ja assinou a minuta ou não
     * @param integer $sqUsuario
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function subQueryJaAssinou($sqUsuario)
    {
        return $this->_em->createQueryBuilder ()
                    ->select('puo3.sqPessoaUnidadeOrg')
                    ->from('app:PessoaAssinanteArtefato', 'sqpaa3')
                    ->innerJoin('sqpaa3.sqPessoaUnidadeOrg', 'puo3')
                    ->innerJoin('puo3.sqPessoaSgdoce', 'sqps3')
                    ->innerJoin('sqpaa3.sqArtefato', 'sqa3')
                    ->innerJoin('sqps3.sqPessoaCorporativo', 'sqvwp3')
                    ->andWhere('sqa3.sqArtefato = a.sqArtefato')
                    ->andWhere('sqvwp3.sqPessoa = :sqPessoa')
                    ->andWhere('sqpaa3.dtAssinado IS NOT NULL')
                    ->setParameter('sqPessoa', $sqUsuario)
                    ->getQuery()
                    ->getDQL();
    }

    /**
    * Retorna subquery que verifica se o artefato possui assinantes
    * @return \Doctrine\ORM\QueryBuilder
    */
    public function subQueryQtdAssinantesArtefato()
    {
        return $this->_em->createQueryBuilder ()
                    ->select('count(sqa2.sqArtefato)')
                    ->from('app:PessoaAssinanteArtefato', 'sqpaa2')
                    ->innerJoin('sqpaa2.sqArtefato', 'sqa2')
                    ->andWhere('sqpaa2.sqArtefato = a.sqArtefato')
                    ->getQuery()
                    ->getDQL();
    }

    /**
    * Retorna subquery que verifica se existe assinatura no modelo de minuta
    * @return \Doctrine\ORM\QueryBuilder
    */
    public function subQueryInCampoAssinatura()
    {
        return $this->_em
                    ->createQueryBuilder()
                    ->select('c3.sqCampo')
                    ->from('app:Artefato', 'a3')
                    ->innerJoin('a3.sqArtefatoMinuta', 'am3')
                    ->innerJoin('am3.sqModeloDocumento', 'md3')
                    ->leftJoin('md3.sqModeloDocumentoCampo', 'mdc3')
                    ->leftJoin('mdc3.sqPadraoModeloDocumentoCam', 'pmdc3')
                    ->innerJoin('pmdc3.sqCampo', 'c3')
                    ->andWhere('a3.sqArtefato = a.sqArtefato')
                    ->andWhere('c3.sqCampo = 31')
                    ->getQuery()
                    ->getDQL();
    }

    /**
     *
     * Método que seleciona a tabela de acordo com a necessidade da query da caixa de minutas
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Core_Dto_Search $dto
     */
    protected function selectTableQuery(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
        $hasEmAcompanhamento = FALSE;

        switch($dto->getView()) {
            case(self::TIPO_VISUALIZACAO_EM_ACOMPANHAMENTO):
                $queryBuilder->from('app:VwCaixaMinutaAcompanhamento', 'vcm');
                $hasEmAcompanhamento = TRUE;
            break;
            default:
                $queryBuilder->from('app:VwCaixaMinuta', 'vcm');
            break;
        }

        $this->addFilterStatus($queryBuilder, $dto);

        return $hasEmAcompanhamento;
    }

    /**
     *
     * Método que adiciona filtro de acordo com a caixa de minutas
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Core_Dto_Search $dto
     */
    protected function addFilterStatus(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
        switch($dto->getView()) {
            default: // RECEBIDAS E DEMAIS
                    $queryBuilder->andWhere('((vcm.sqStatusArtefato = :sqStatusArtefato2')
                                ->setParameter('sqStatusArtefato2',
                                \Core_Configuration::getSgdoceStatusRecebida())
                                ->orWhere('vcm.sqStatusArtefato = :sqStatusArtefato4))')
                                ->setParameter('sqStatusArtefato4',
                                \Core_Configuration::getSgdoceStatusDevolvida())
                                ->andWhere('vcm.sqPessoa = :sqPessoa')
                                ->setParameter('sqPessoa', $dto->getSqPessoa());
            break;
            case(self::TIPO_VISUALIZACAO_ENVIADA): // ENVIADA
                $queryBuilder->andWhere('(vcm.sqStatusArtefato = :sqStatusArtefato201')
                             ->setParameter('sqStatusArtefato201', self::ZER)
                             ->andWhere('vcm.sqPessoa = :sqPessoa)')
                             ->setParameter('sqPessoa', $dto->getSqPessoa());
            break;
            case(self::TIPO_VISUALIZACAO_EM_ACOMPANHAMENTO): // EM ACOMPANHAMENTO
                $queryBuilder->andWhere('(vcm.sqPessoa = :sqPessoa)')
                             ->setParameter('sqPessoa', $dto->getSqPessoa());
            break;
            case(self::TIPO_VISUALIZACAO_PRODUZIDA): // PRODUZIDA
                $queryBuilder->andWhere('(vcm.sqStatusArtefato = :sqStatusArtefato49')
                            ->setParameter('sqStatusArtefato49', \Core_Configuration::getSgdoceStatusProduzida())
                            ->andWhere('vcm.sqPessoa = :sqPessoa)')
                            ->setParameter('sqPessoa', $dto->getSqPessoa());
            break;
        }
    }

    /**
    * método que adiciona filtro para a pesquisa da caixa de minutas
    * @param \Doctrine\ORM\QueryBuilder &$queryBuilder
    * @param Core_Dto_Search $dto
    */
    protected function addWhere(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
        $isDate = FALSE;
        $data = explode('/', $dto->getDataSearch());
        if (count($data) == 3) {
            if (checkdate($data[1], $data[0], $data[2])) {
                $isDate = TRUE;
                $newDate = $data[2].'-'.$data[1].'-'.$data[0];

                $queryBuilder->andWhere('vcm.dataCriacao = :data')
                ->setParameter('data', $newDate);
                $queryBuilder->orWhere('vcm.prazo = :data')
                ->setParameter('data', $newDate);
            }
        }

        if ((!$isDate) && ($dto->getDataSearch() != '')) {
            $query = mb_strtolower($dto->getDataSearch(), 'UTF-8');

            $queryBuilder->andWhere('(((LOWER(vcm.tipo) like :query)');
            $queryBuilder->orWhere('(LOWER(vcm.origem) like :query)');
            $queryBuilder->orWhere('(LOWER(vcm.assunto) like :query)');
            $queryBuilder->orWhere('(LOWER(vcm.autor) like :query)');
            $queryBuilder->orWhere('(LOWER(vcm.status) like :query)))');
            $queryBuilder->setParameter('query', '%' . $query . '%');

            $queryBuilder->andWhere('vcm.sqStatusArtefato <> :sqStatusArtefato54')
                         ->setParameter('sqStatusArtefato54', \Core_Configuration::getSgdoceStatusExcluida());
        }
    }

    /**
    * Obtem dados da 'vw_caixa_minuta' referente ao artefato passado no parametro
    * @param \Core_Dto_Entity $dto
    * @return array
    */
    public function findCaixaMinuta(\Core_Dto_Entity $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
        ->select('vcm.sqArtefato, vcm.sqStatusArtefato, vcm.sqHistoricoArtefato')
        ->from('app:VwCaixaMinuta', 'vcm')
        ->andWhere('vcm.sqArtefato = :sqArtefato')
        ->setParameter('sqArtefato', $dto->getSqArtefato()->getSqArtefato())
        ->orderBy('vcm.sqHistoricoArtefato', 'DESC')
        ->setMaxResults(self::UNIC)
        ->getQuery()
        ->execute();

        $result = NULL;
        if(!empty($queryBuilder)){
            $result = $queryBuilder[self::ZER];
        }

        return $result;
    }

    /**
    * método que retorna dados para grid da área de trabalho
    * @param \Core_Dto_Abstract $dto
    * @return \Doctrine\ORM\QueryBuilder
    */
    public function listGridAreaTrabalho(\Core_Dto_Search $dto)
    {

        $search = mb_strtolower($dto->getSearch(),'UTF-8');

        /*
         *  se $search is numeric "is_numeric($search)" // procura nas colunas numericas (nuDigital, nuArtefato)
         *
         * caso contrario procura em todas as colunas com "OR"
         *
         */

        $subQuery = $this->_em->createQueryBuilder()
            ->select('max(hh.sqHistoricoArtefato)')
            ->from('app:HistoricoArtefato','hh')
            ->where('hh.sqArtefato = hia.sqArtefato');

        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $queryBuilder
            ->select('a.sqArtefato',
                      'IDENTITY(a.nuDigital) as nuDigital',
                      'a.nuArtefato',
                      'a.dtArtefato',
                      'td.noTipoDocumento as tipo',
                      'td.inAbreProcesso',
                      'ass.txAssunto as assunto',
                      'ps.noPessoa as origem'
//                      ,'sa.sqStatusArtefato'
                    )
            ->distinct('a.sqArtefato')
            ->from('app:Artefato', 'a')
            ->innerJoin('a.sqHistoricoArtefato', 'hia')
//            ->innerJoin('hia.sqStatusArtefato', 'sa')
            ->leftJoin('a.sqTipoDocumento', 'td')
            ->innerJoin('a.sqTipoArtefatoAssunto', 'tas')
            ->innerJoin('tas.sqAssunto', 'ass')
            ->innerJoin('a.sqPessoaArtefato', 'pa', 'WITH', 'pa.sqPessoaFuncao = :sqPessoaFuncao')
            ->innerJoin('pa.sqPessoaSgdoce', 'ps','WITH')
            ->andWhere('tas.sqTipoArtefato = :sqTipoArtefato')
            ->andWhere('hia.sqPessoa = :sqPessoa')
            ->andWhere('hia.sqHistoricoArtefato = ('.$subQuery->getDQL().')')
            ->setParameter('sqPessoa', $dto->sqPessoa)
            ->setParameter('sqTipoArtefato',$dto->sqTipoArtefato)
            ->setParameter('sqPessoaFuncao',\Core_Configuration::getSgdocePessoaFuncaoOrigem())
            ->orderBy('a.sqArtefato');

//        if($search){
//            $nuArtefato = $queryBuilder->expr()
//            ->lower($queryBuilder->expr()->trim('a.nuArtefato'));
//
//            if (is_numeric($search)) {
//                $query->orWhere('a.nuDigital = :search');
//
//
//                $query->orWhere($queryBuilder->expr()->like(
//                            'clear_accentuation(' . $nuArtefato .')',
//                            $queryBuilder->expr()
//                                ->literal($this->removeAccent('%' . $search . '%'))
//                        )
//                    );
//            }else{
//
//            }
//            $query->setParameter('search', $search);
//        }

        return $query;
    }
}