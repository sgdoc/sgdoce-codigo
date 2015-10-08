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
 * Classe para Repository de Artefato
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Artefato
 * @version      1.0.0
 * @since        2012-11-20
 */
class Artefato extends ArtefatoExtensao
{

    /**
     * Constante para receber o valor zero
     * @var    integer
     * @name   ZER
     */
    const ZER = 0;

    /**
     * Constante para receber o valor um
     * @var    integer
     * @name   UNIC
     */
    const UNIC = 1;

    /**
     * método que retorna dados para grid
     * @param \Core_Dto_Search $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGridVinculacao(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select('af.nuArtefato',
                         'IDENTITY(af.nuDigital) as nuDigital',
                         'ta.noTipoArtefato',
                         'ps.noPessoa',
                         'av.sqArtefatoVinculo',
                         'art_pro.coAmbitoProcesso')
                ->from('app:ArtefatoVinculo', 'av')
                ->innerJoin('av.sqArtefatoPai', 'ap')
                ->innerJoin('av.sqArtefatoFilho', 'af')
                ->innerJoin('af.sqTipoArtefatoAssunto', 'taa')
                ->innerJoin('taa.sqTipoArtefato', 'ta')
                ->innerJoin('af.sqPessoaArtefato', 'pa')
                ->innerJoin('pa.sqPessoaSgdoce', 'ps')
                ->leftJoin('af.sqArtefatoProcesso', 'art_pro')
                ->andWhere('av.sqTipoVinculoArtefato = :material')
                ->setParameter('material', $dto->getSqTipoVinculo())
                ->andWhere('av.dtRemocaoVinculo IS NULL')
                ->andWhere('pa.sqPessoaFuncao = :sqPessoaFuncao')
                ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoOrigem())
                ->orderBy('af.nuArtefato', 'DESC');

        if ($dto->getSqArtefato()) {
            $queryBuilder->andWhere('ap.sqArtefato = :id')
                    ->setParameter('id', $dto->getSqArtefato());
        } else {
            $queryBuilder->andWhere("1 != 1");
        }

        return $queryBuilder;
    }

    /**
     * método que retorna dados para grid
     * @param \Core_Dto_Search $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGridDocumentos(\Core_Dto_Search $dto)
    {
        $inValue = $this->_em->createQueryBuilder()->select('av')
                ->from('app:ArtefatoVinculo', 'av')->andWhere('av.sqArtefatoPai = :id')
                ->setParameter('id', $dto->getSqArtefato());

        $setDQL = $this->_em->createQueryBuilder()
                ->addSelect('a2.nuDigital')->from('app:Artefato', 'a2')
                ->andWhere('a2.sqArtefato = av.sqArtefatoFilho');
        $setSq = $this->_em->createQueryBuilder()
                ->addSelect('a3.sqArtefato')->from('app:Artefato', 'a3')
                ->andWhere('a3.sqArtefato = av.sqArtefatoFilho');

        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('av.inOriginal', 'ta.sqTipoArtefato', "({$setDQL->getDQL()}) nuDigital", 'av.sqArtefatoVinculo', "({$setSq->getDQL()}) sqArtefato")
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqArtefatoPai', 'av')
                ->leftJoin('a.sqTipoArtefatoAssunto', 'taa')
                ->leftJoin('taa.sqTipoArtefato', 'ta')
                ->andWhere('av.dtRemocaoVinculo IS NULL');
        if ($inValue) {
            $queryBuilder->andWhere('av.sqArtefatoPai = :id')->setParameter('id', $dto->getSqArtefato());
            $queryBuilder->andWhere('av.sqTipoVinculoArtefato = :TipoVinculoArtefato')->setParameter('TipoVinculoArtefato', 3);
        } else {
            $queryBuilder->andWhere("1 != 1");
        }

        return $queryBuilder;
    }

    /**
     * método que retorna dados para grid
     * @param \Core_Dto_Abstract $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGridVinculacaoInsercao(\Core_Dto_Search $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('a.nuArtefato', 'ta.noTipoArtefato', 'a.nuDigital', 'ps.noPessoa', 'av.sqArtefatoVinculo')
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqArtefatoPai', 'av')
                ->leftJoin('a.sqTipoArtefatoAssunto', 'taa')
                ->leftJoin('taa.sqTipoArtefato', 'ta')
                ->leftJoin('a.sqPessoaArtefato', 'pa')
                ->leftJoin('pa.sqPessoaSgdoce', 'ps')
                ->andWhere('av.sqTipoVinculoArtefato = :material')
                ->setParameter('material', \Core_Configuration::getSgdoceTipoVinculoArtefatoInsercao())
                ->andWhere('av.dtRemocaoVinculo IS NULL')
                ->andWhere('av.sqArtefatoPai = :id')
                ->setParameter('id', $dto->getSqArtefato());
        return $queryBuilder;
    }

    /**
     * método que retorna dados para grid
     * @param \Core_Dto_Search $dto
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function listGridMaterialApoio(\Core_Dto_Search $dto)
    {
        $inValue = $this->getArtefatoFilhoVinculoArtefato($dto);

        $setNudigital = $this->_em->createQueryBuilder()
                ->addSelect('a2.nuDigital')->from('app:Artefato', 'a2')
                ->andWhere('a2.sqArtefato = av.sqArtefatoFilho');
        $setNuArtefato = $this->_em->createQueryBuilder()
                ->addSelect('ta2.noTipoArtefato')->from('app:Artefato', 'a3')
                ->leftJoin('a3.sqTipoArtefatoAssunto', 'tafa')
                ->leftJoin('tafa.sqTipoArtefato', 'ta2')
                ->andWhere('a3.sqArtefato = av.sqArtefatoFilho');
        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('a.nuArtefato', "({$setNuArtefato->getDQL()}) noTipoArtefato", "({$setNudigital->getDQL()}) nuDigital", 'ps.noPessoa', 'ad.noTitulo', 'av.sqArtefatoVinculo', 'a.txAssuntoComplementar')
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqArtefatoPai', 'av')
                ->leftJoin('a.sqArtefatoDossie', 'ad')
                ->leftJoin('a.sqTipoArtefatoAssunto', 'taa')
                ->leftJoin('taa.sqTipoArtefato', 'ta')
                ->leftJoin('a.sqPessoaArtefato', 'pa')
                ->leftJoin('pa.sqPessoaSgdoce', 'ps')
                ->andWhere('av.dtRemocaoVinculo IS NULL');

        if ($inValue) {
            $queryBuilder->andWhere('av.sqTipoVinculoArtefato = :materialApoio')
                    ->setParameter('materialApoio', \Core_Configuration::getSgdoceTipoVinculoArtefatoApoio())
                    ->andWhere('av.sqArtefatoPai = :id')
                    ->setParameter('id', $dto->getSqArtefato());
        } else {
            $queryBuilder->andWhere("1 != 1");
        }


        return $queryBuilder;
    }

    /**
     * Adiciona condição
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     * @param \Core_Dto_Search $dto
     */
    protected function addWhere(\Doctrine\ORM\QueryBuilder &$queryBuilder, \Core_Dto_Search $dto)
    {
        $isDate = FALSE;
        $dataSearch = strtotime($dto->getDataSearch());
        if (checkdate(date('m', $dataSearch), date('d', $dataSearch), date('Y', $dataSearch))) {
            $isDate = TRUE;
            $newDate = date('Y-m-d', $dataSearch);
            $queryBuilder->andWhere('vcm.dataCriacao = :data')
                    ->setParameter('data', $newDate);
            $queryBuilder->orWhere('vcm.prazo = :data')
                    ->setParameter('data', $newDate);
        }

        if ((!$isDate) && ($dto->getDataSearch() != '')) {
            $query = mb_strtolower($dto->getDataSearch(), 'UTF-8');

            $queryBuilder->andWhere('(LOWER(vcm.tipo) like :query)');
            $queryBuilder->orWhere('(LOWER(vcm.origem) like :query)');
            $queryBuilder->orWhere('(LOWER(vcm.assunto) like :query)');
            $queryBuilder->orWhere('(LOWER(vcm.autor) like :query)');
            $queryBuilder->setParameter('query', '%' . $query . '%');
        }
    }

    /**
     * Obtém o texto da ementa e do artefato
     * @param $dto
     */
    public function findVisualizaMinuta($dto)
    {
        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select('a.txEmenta', 'a.txTextoArtefato')
                ->from('app:Artefato', 'a')
                ->andWhere('a.sqArtefato = :sqArtefato')
                ->setParameter('sqArtefato', $dto->getSqArtefato());

        return $queryBuilder->getQuery()->getSingleResult();
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
        if (!empty($queryBuilder)) {
            $result = $queryBuilder[self::ZER];
        }

        return $result;
    }

    public function selectGridConsulta()
    {
        $qrBuilder = $this->_em->createQueryBuilder();
        $expr = $qrBuilder->expr()->andX(
                $qrBuilder->expr()->eq('ps.sqPessoaSgdoce', 'pa.sqPessoaFuncao'), $qrBuilder->expr()->eq('pa.sqPessoaFuncao', ':origem')
        );

        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('distinct a.sqArtefato,
                  a.nuDigital,
                  a.nuArtefato,
                  a.txAssuntoComplementar,
                  td.noTipoDocumento,
                  td.sqTipoDocumento,
                  ta.noTipoArtefato,
                  ta.sqTipoArtefato,
                  ps.sqPessoaSgdoce,
                  ps.noPessoa origem,
                  p2.noPessoa interessado,
                  ps.nuCpfCnpjPassaporte,
                  o.noOcorrencia,
                  a.dtPrazo,
                  ass.txAssunto,
                  ca.txComentario,
                  sa.noStatusArtefato,
                  am.txReferencia,
                  pr.noPrioridade,
                  ta.sqTipoArtefato')
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqTipoArtefatoAssunto', 'taa')
                ->innerJoin('taa.sqTipoArtefato', 'ta')
                ->leftJoin('a.sqTipoDocumento', 'td')
                ->leftJoin('taa.sqAssunto', 'ass')
                ->leftJoin('a.sqPessoaArtefato', 'pa')
                ->leftJoin('pa.sqPessoaSgdoce', 'ps')
                ->leftJoin(
                        'pa.sqPessoaFuncao'
                        , 'pf'
                        , \Doctrine\ORM\Query\Expr\Join::WITH, $expr
                )
                ->leftJoin('a.sqPessoaInteressadaArtefato', 'pia')
                ->leftJoin('pia.sqPessoaSgdoce', 'p2')
                ->setParameter('origem', \Core_Configuration::getSgdocePessoaFuncaoOrigem());

        return $queryBuilder;
    }

    public function verificaNuArtefatoDisponivel($params)
    {

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato', 'sqArtefato', 'integer');

        $query = $this->_em->createNativeQuery('
	            SELECT * FROM sgdoce.artefato a
                INNER JOIN sgdoce.pessoa_assinante_artefato paa ON paa.sq_artefato = a.sq_artefato
                INNER JOIN sgdoce.pessoa_unidade_org uo ON uo.sq_pessoa_unidade_org = paa.sq_pessoa_unidade_org
                WHERE a.nu_artefato = :nuArtefato
                AND a.sq_tipo_documento = :sqTipoDocumento
                AND uo.sq_pessoa_unidade_org_corp = :sqUnidadeOrg'
                , $rsm);

        $query->setParameter('nuArtefato', $params['nuArtefato']);
        $query->setParameter('sqTipoDocumento', $params['sqTipoDocumento']);
        $query->setParameter('sqUnidadeOrg', $params['sqUnidadeOrg']);
        $result = $query->getResult();
        if (count($result) > 0) {
            return FALSE;
        }
        return TRUE;
    }

    public function verificaDuplicidade($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('1')
                ->from('app:artefato', 'a')
                ->innerJoin('a.sqPessoaArtefato', 'pa')
                ->innerJoin('pa.sqPessoaSgdoce', 'ps')
                ->leftJoin('ps.sqVwPessoaUnidadeOrg', 'vuo')
                ->leftJoin('ps.sqPessoaCorporativo', 'vp')
                ->andWhere('vuo.sqUnidadeOrg = :unidadeOrg')
                ->orWhere('vp.sqPessoa = :unidadeOrg')
                ->andWhere('pa.sqPessoaFuncao = :pessoaFuncao')
                ->andWhere('a.sqTipoDocumento = :tipoDocumento')
                ->setParameter('pessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoOrigem())
                ->setParameter('unidadeOrg', $dto->getOrigem())
                ->setParameter('tipoDocumento', $dto->getTipo());

        if ($dto->getNumero()) {
            $queryBuilder->andWhere('a.nuArtefato = :nuArtefato')
                    ->setParameter('nuArtefato', $dto->getNumero());
        } else {
            $queryBuilder->andWhere('a.nuArtefato IS NULL');
        }

        //se for update o sqArtefato vem preenchido
        if ($dto->getSqArtefato()) {
            $queryBuilder->andWhere('a.sqArtefato <> :sqArtefato')
                    ->setParameter('sqArtefato', $dto->getSqArtefato());
        }

        return $queryBuilder->getQuery()->execute();
    }

    public function verificaDigital($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder
                ->select('a.nuDigital, a.sqArtefato')
                ->from('app:Artefato', 'a')
                ->where($queryBuilder->expr()->eq('a.nuDigital', ':nuDigital'))
                ->andWhere($queryBuilder->expr()->neq('a.sqTipoDocumento', ':tipoApolice'))
                ->setParameter('nuDigital', $dto->getNuDigital())
                ->setParameter('tipoApolice', \Core_Configuration::getSgdoceTipoArtefatoMinuta());

        return $queryBuilder->getQuery()->execute();
    }

    public function getArtefatoList (array $arrSqArtefato)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder
                ->select('a')
                ->from('app:Artefato', 'a')
                ->where('a.sqArtefato in(:sqArtefato)')
                ->setParameter('sqArtefato', $arrSqArtefato)
                ;
        return $queryBuilder->getQuery()->execute();

    }

    /**
     * @return
     */
    public function listPesquisaDocumento($dto)
    {
        $listCondition = array(
            'getNuArtefato' => array(
                "ilike" => array(
                    "AND" => 'art.nu_artefato'
                )
            ),
            'getSqAssunto' => array(
                "=" => array(
                    "AND" => 'ass.sq_assunto'
                )
            ),
            'getTxAssuntoComplementar' => array(
                "ilike" => array(
                    "AND" => 'art.tx_assunto_complementar'
                )
            ),
            'getInteressado' => array(
                "ilike" => array(
                    "OR" => array(
                        "trim(pse.no_pessoa)",
                        "trim(vue.sg_unidade_org || ' - ' || pse.no_pessoa)",
                    ),
                    'tlp' => array(
                        'trim(%s)',
                        'trim(%s)'
                    )
                )
            ),
            'getOrigem' => array(
                "ilike" => array(
                    "OR" => array(
                        "trim(pfo.no_pessoa)",
                        "trim(vuo.sg_unidade_org || ' - ' || pfo.no_pessoa)",
                    ),
                    'tlp' => array(
                        'trim(%s)',
                        'trim(%s)'
                    )
                )
            ),
            'getNuDigital' => array(
                "ilike" => array(
                    "AND" => 'formata_numero_digital(art.nu_digital)'
                )
            ),
            /*'getNuDigitalNumber' => array(
                "=" => array(
                    "OR" => 'art.nu_digital'
                )
            ),*/
            'getDestino' => array(
                "ilike" => array(
                    "OR" => array(
                        "trim(pfd.no_pessoa)",
                        "trim(vud.sg_unidade_org || ' - ' || pfd.no_pessoa)",
                    ),
                    'tlp' => array(
                        'trim(%s)',
                        'trim(%s)'
                    )
                )
            ),
            'getAssinatura' => array(
                "ilike" => array(
                    "OR" => array(
                        "trim(pfa.no_pessoa)",
                        "trim(vua.sg_unidade_org || ' - ' || pfa.no_pessoa)",
                    ),
                    'tlp' => array(
                        'trim(%s)',
                        'trim(%s)'
                    )
                )
            ),
            'getNoTipoDocumento' => array(
                "ilike" => array(
                    "AND" => 'tad.no_tipo_documento'
                )
            ),
        );

        // criteria
        $listPeriodo = array(
            'dtCadastro' => 'art.dt_cadastro',
            'dtAutuacao' => 'art.dt_artefato',
            'dtPrazo'    => 'art.dt_prazo'
        );
        $sqPeriodo = $dto->getSqPeriodo();

        $periodoColumn = null;

        if( isset($listPeriodo[$sqPeriodo]) ){
            $periodoColumn = $listPeriodo[$sqPeriodo];
        }

        $where = $this->getCriteriaText($listCondition, $dto, $periodoColumn);

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('total_record'     , 'totalRecord', 'integer');
        $rsm->addScalarResult('sq_artefato'      , 'sqArtefato', 'integer');
        $rsm->addScalarResult('nu_digital'       , 'nuDigital', 'string');
        $rsm->addScalarResult('tx_assunto'       , 'txAssunto', 'string');
        $rsm->addScalarResult('nu_artefato'      , 'nuArtefato', 'string');
        $rsm->addScalarResult('no_tipo_documento', 'noTipoDocumento', 'string');
        $rsm->addScalarResult('origem'           , 'noPessoaOrigem', 'string');
        $rsm->addScalarResult('dt_artefato'      , 'dtArtefato', 'string');
        $rsm->addScalarResult('interessados'     , 'noPessoaInteressados', 'string');
        $rsm->addScalarResult('tx_movimentacao'  , 'txMovimentacao', 'string');

        $sql = "SELECT  COUNT(art.sq_artefato) OVER() AS total_record,
                         art.sq_artefato,
                         formata_numero_digital(art.nu_digital) AS nu_digital,
                         ass.tx_assunto,
                         art.nu_artefato,
                         tad.no_tipo_documento,
                         pfo.no_pessoa as origem,
                         art.dt_artefato,
                         string_agg(pse.no_pessoa, ', ') AS interessados,
                         pfd.no_pessoa as destino,
                         pfa.no_pessoa as assinatura,
                         sgdoce.ultima_movimentacao_artefato(art.sq_artefato) as tx_movimentacao
                    FROM sgdoce.artefato art
                    JOIN sgdoce.tipo_artefato_assunto taa ON art.sq_tipo_artefato_assunto = taa.sq_tipo_artefato_assunto
                    JOIN sgdoce.assunto ass ON taa.sq_assunto = ass.sq_assunto
                    JOIN sgdoce.tipo_documento tad ON art.sq_tipo_documento = tad.sq_tipo_documento
                    JOIN sgdoce.pessoa_artefato pao ON art.sq_artefato = pao.sq_artefato AND pao.sq_pessoa_funcao  = " . \Core_Configuration::getSgdocePessoaFuncaoOrigem() . "
                    JOIN sgdoce.pessoa_sgdoce pfo ON pao.sq_pessoa_sgdoce = pfo.sq_pessoa_sgdoce
               LEFT JOIN corporativo.vw_unidade_org vuo ON pfo.sq_pessoa_corporativo = vuo.sq_pessoa
               LEFT JOIN sgdoce.pessoa_interessada_artefato pai ON art.sq_artefato = pai.sq_artefato
               LEFT JOIN sgdoce.pessoa_sgdoce pse ON pai.sq_pessoa_sgdoce = pse.sq_pessoa_sgdoce
               LEFT JOIN corporativo.vw_unidade_org vue ON pse.sq_pessoa_corporativo = vue.sq_pessoa
               LEFT JOIN sgdoce.pessoa_artefato pad ON art.sq_artefato = pad.sq_artefato AND pad.sq_pessoa_funcao = " . \Core_Configuration::getSgdocePessoaFuncaoDestinatario() . "
               LEFT JOIN sgdoce.pessoa_sgdoce pfd ON pad.sq_pessoa_sgdoce = pfd.sq_pessoa_sgdoce
               LEFT JOIN corporativo.vw_unidade_org vud ON pfd.sq_pessoa_corporativo = vud.sq_pessoa
               LEFT JOIN sgdoce.pessoa_artefato paa ON art.sq_artefato = paa.sq_artefato AND paa.sq_pessoa_funcao = " . \Core_Configuration::getSgdocePessoaFuncaoAssinatura() . "
               LEFT JOIN sgdoce.pessoa_sgdoce pfa ON paa.sq_pessoa_sgdoce = pfa.sq_pessoa_sgdoce
               LEFT JOIN corporativo.vw_unidade_org vua ON pfa.sq_pessoa_corporativo = vua.sq_pessoa
                    %s
                GROUP BY art.sq_artefato,
                         art.nu_digital,
                         ass.tx_assunto,
                         art.nu_artefato,
                         tad.no_tipo_documento,
                         pfo.no_pessoa,
                         art.dt_artefato,
                         pfd.no_pessoa,
                         pfa.no_pessoa,
                         tx_movimentacao";

        if( $where != "" ) {
            $where = "WHERE " . $where;
        } else {
            $where = "WHERE 1 <> 1";
        }

        $where .=  " AND taa.sq_tipo_artefato = " . \Core_Configuration::getSgdoceTipoArtefatoDocumento();

        $sql = sprintf($sql, $where);

        return $this->_em->createNativeQuery($sql, $rsm);
    }

    /**
     *
     * @param array $listCondition
     * @param \Core_Dto_Searchs $dto
     * @param string $periodoColumn
     *
     * @return string
     */
    public function getCriteriaText($listCondition, $dto, $periodoColumn = null)
    {
        $where = "";
        $initWhere = false;
        // FALSE = Fragmento, TRUE = Completo
        $stTipoPesquisa = (boolean)$dto->getStTipoPesquisa();

        foreach( $listCondition as $method => $condition ){
            $value      = $dto->$method();
            $oldValue   = $value;
            $operation  = strtolower(key($condition));

            if( $operation == 'ilike' && !$stTipoPesquisa ) {
                $value = preg_replace("/[%&\']/", "", $value);
                $values = explode(" ", $value);
                if( count($values) > 1 ) {
                    $value = "'%" . implode("%", $values) . "%'";
                } else if( !empty($value) ){
                    $value = "'%{$value}%'";
                }
            } else if($operation == 'ilike' && $stTipoPesquisa){
                $value = preg_replace("/[%&\']/", "", $value);
                $operation = 'ilike';
                $value     = "'%{$value}%'";
            } else if( $operation == 'regex' ) {
                // nu_artefato
                $valueOriginal = $value;
                $value = preg_replace( '/[^0-9]/', '', $value );
                $operation = '~';
                $value     = "'^({$valueOriginal}|{$value}).*'";
            }

            $condition      = current($condition);
            $groupColumns   = current($condition);

            if( $oldValue != '' && $value == '' ) {
                $value = "NULL";
            }

            /* add is_numeric para poder usar operação is null ou is not null e value postado for igual a zero (0) */
            if(is_numeric($value) || (!empty($value) && $value != "'%%'" )) {

                if( isset($condition['tlp']) ){
                    $values_tlp = $condition['tlp'];
                    unset($condition['tlp']);
                }
                $tmp_value = $value;
                foreach($condition as $sqlCondition => $column) {
                    if( is_array($column)) {

                        $columns = $column;

                        if( $initWhere ) {
                            $where .= " AND ";
                            $initWhere = false;
                        }

                        if( is_array($groupColumns) ) {
                            $where .= "( ";
                        }

                        foreach( $columns as $key => $column ) {

                            if( isset($values_tlp[$key]) ){
                                $value_tlp = $values_tlp[$key];
                                $value = sprintf($value_tlp, $tmp_value);
                            }

                            if( $initWhere == true ) {
                                $where .= " {$sqlCondition} ";
                            }

                            $this->_normalizeConditions($column, $operation, $value);

                            $where .= $column . " " . strtoupper($operation) . " " . $value;

                            $initWhere = true;
                        }

                        if( is_array($groupColumns) ) {
                            $where .= " )";
                        }
                    } else {

                        if( $initWhere == true ) {
                            $where .= " {$sqlCondition} ";
                        }

                        $this->_normalizeConditions($column, $operation, $value);

                        $where .= $column . " " . strtoupper($operation) . " " . $value;
                    }
                    $initWhere = true;
                }
                $value = null;
            }
        }

        if( !is_null($periodoColumn) ) {

            if( $initWhere == true ) {
                $where .= " AND ";
            }

            $column     = $periodoColumn;

            $dtInicial  = $dto->getDtInicial();
            $dtFinal    = $dto->getDtFinal();

            if( $dtInicial != ""
                && $dtFinal != "" ) {
                $obIncial   = new \Zend_Date($dtInicial);
                $obFinal    = new \Zend_Date($dtFinal);
                $where .= " " . $column . " between '" . $obIncial->get("yyyy-MM-dd") . " 00:00:00' AND '" . $obFinal->get("yyyy-MM-dd") . " 23:59:59'";
            } else if ( $dtInicial != "" ){
                $obIncial   = new \Zend_Date($dtInicial);
                $where .= " " . $column . " between '" . $obIncial->get("yyyy-MM-dd") . " 00:00:00' AND '" . $obIncial->get("yyyy-MM-dd") . " 23:59:59'";
            } else if( $dtFinal != "" ){
                $obFinal    = new \Zend_Date($dtFinal);
                $where .= " " . $column . " between '" . $obFinal->get("yyyy-MM-dd") . " 00:00:00' AND '" . $obFinal->get("yyyy-MM-dd") . " 23:59:59'";
            }
        }

        return $where;
    }

    private function _normalizeConditions(&$column,&$operation,&$value)
    {
        if ($operation == 'ilike' && !is_numeric(str_replace(array("%", "'"), '', $value))) {
            $value = $this->translate($value);
            $column = "TRANSLATE(TRIM({$column}), 'áàâãäéèêëíìîĩïóòôõöúùûũüÁÀÂÃÄÉÈÊËÍÌÎĨÏÓÒÔÕÖÚÙÛŨÜçÇ', 'aaaaaeeeeiiiiiooooouuuuuAAAAAEEEEIIIIIOOOOOUUUUUcC')";
        }

        if ($operation == 'is null' || $operation == 'is not null') {
            $value = '';
        }
    }


    /**
     * @return
     */
    public function findArtefatoResposta( $dto, $limit = 10 )
    {
        $sqTipoArtefato = $dto->getSqTipoArtefato();
        $nuArtefato     = $dto->getNuArtefato();
        $sqArtefatoPrazo = $dto->getSqArtefatoPrazo();

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato', 'sqArtefato', 'integer');
        $rsm->addScalarResult('nu_artefato', 'nuArtefato', 'string');
        $rsm->addScalarResult('nu_digital', 'nuDigital', 'string');
        $rsm->addScalarResult('sq_tipo_artefato', 'sqTipoArtefato', 'integer');

        $likeNuArtefatoNuDigital = "";

        if( $sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ){
            $likeNuArtefatoNuDigital = "art.nu_artefato ILIKE '%{$nuArtefato}%'";
        } else {
            $likeNuArtefatoNuDigital = "formata_numero_digital(art.nu_digital) ILIKE '%{$nuArtefato}%'";
        }

        $sql = "SELECT art.sq_artefato,
                       formata_numero_artefato(art.nu_artefato, atp.co_ambito_processo) AS nu_artefato,
                       formata_numero_digital(art.nu_digital) AS nu_digital,
                       ta.sq_tipo_artefato
                FROM sgdoce.artefato art
                INNER JOIN sgdoce.tipo_artefato_assunto tas ON art.sq_tipo_artefato_assunto = tas.sq_tipo_artefato_assunto
                INNER JOIN sgdoce.tipo_artefato ta ON tas.sq_tipo_artefato = ta.sq_tipo_artefato
                LEFT JOIN sgdoce.artefato_processo atp ON art.sq_artefato = atp.sq_artefato
                LEFT JOIN sgdoce.artefato_vinculo av ON art.sq_artefato = av.sq_artefato_filho
                WHERE ta.sq_tipo_artefato = {$sqTipoArtefato}
                AND ((av.sq_artefato_filho IS NULL OR av.sq_tipo_vinculo_artefato IN (
                " . \Core_Configuration::getSgdoceTipoVinculoArtefatoDespacho() . ",
                " . \Core_Configuration::getSgdoceTipoVinculoArtefatoApoio() . ",
                " . \Core_Configuration::getSgdoceTipoVinculoArtefatoReferencia() . "
                )))
                AND ({$likeNuArtefatoNuDigital})
                AND art.sq_artefato <> {$sqArtefatoPrazo} LIMIT {$limit}";

        $nativeQuery = $this->_em->createNativeQuery($sql, $rsm);

        return $nativeQuery->execute();
    }



    /**
     * @param mixed $search (\Core_Dto_Search , \Sgdoce\Model\Entity\Artefato)
     * @todo validação de artefato inconsistente.
     */
    public function isInconsistent( $search, $onlyImage = false, $onlyData = false )
    {

        if ($search instanceof \Sgdoce\Model\Entity\Artefato) {
            $entArtefato = $search;
        }else{
            $entArtefato = $this->find( $search->getSqArtefato() );
        }

        if( $entArtefato->getStMigracao() ) {

            $arInconsistencia = str_replace(array("{", "}"), "", $entArtefato->getArInconsistencia());
            $arInconsistencia = explode(",", $arInconsistencia);

            if( $onlyData ) {
                array_pop($arInconsistencia);
                if(in_array('f', $arInconsistencia)) {
                    return true;
                }
            }

            if( $onlyImage ) {
                if( end($arInconsistencia) == 'f' ) {
                    return true;
                }
            }

            if(!$onlyData && !$onlyImage) {
                if(in_array('f', $arInconsistencia) ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param \Core_Dto_Search $dtoSearch
     * @todo validação de artefato inconsistente.
     */
    public function isMigracao( $dtoSearch )
    {
        $entArtefato = $this->find( $dtoSearch->getSqArtefato() );
        return $entArtefato->getStMigracao();
    }


    /**
     * @return
     */
    public function findArtefatoDuplicado( $dto, $count = true )
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato', 'sqArtefato', 'integer');
        $rsm->addScalarResult('nu_digital', 'nuDigital', 'string');
        $rsm->addScalarResult('nu_artefato', 'nuArtefato', 'string');

        $sql = "SELECT art.sq_artefato,
                       sgdoce.formata_numero_digital(art.nu_digital) AS nu_digital,
                       art.nu_artefato
                  FROM sgdoce.artefato art
                  JOIN sgdoce.pessoa_artefato pes_art ON art.sq_artefato          = pes_art.sq_artefato
                  JOIN sgdoce.pessoa_sgdoce pes_sgd   ON pes_art.sq_pessoa_sgdoce = pes_sgd.sq_pessoa_sgdoce
                 WHERE art.sq_tipo_documento         = :sqTipoDocumento
                   AND art.nu_artefato               = :nuArtefato
                   AND pes_sgd.sq_pessoa_corporativo = :sqPessoaOrigem
                   AND pes_art.sq_pessoa_funcao      = :sqPessoaFuncao";

        if( $dto->getSqArtefato() ) {
            $sql .= " AND art.sq_artefato <> :sqArtefato";
        }

        $nativeQuery = $this->_em->createNativeQuery($sql, $rsm)
                                 ->setParameter('sqTipoDocumento', $dto->getSqTipoDocumento())
                                 ->setParameter('nuArtefato', $dto->getNuArtefato())
                                 ->setParameter('sqPessoaOrigem', $dto->getSqPessoaOrigem())
                                 ->setParameter('sqPessoaFuncao', \Core_Configuration::getSgdocePessoaFuncaoOrigem());

        if( $dto->getSqArtefato() ) {
            $nativeQuery->setParameter('sqArtefato', $dto->getSqArtefato() );
        }

        if( $count ) {
            return ( count($nativeQuery->getArrayResult()) > 0 );
        }

        return $nativeQuery->getArrayResult();
    }


}
