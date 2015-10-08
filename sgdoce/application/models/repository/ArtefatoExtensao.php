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
use Artefato\Service\Processo;

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
class ArtefatoExtensao extends \Core_Model_Repository_Base {

	/**
	 * Constante para receber o valor de tipo de vinculo material
	 * @var    integer
	 * @name   DOSSIE
	 */
	const TIPO_VINCULO_MATERIAL = 6;

    /**
     * Consulta amaterial documento
     * @param \Core_Dto_Entity $dto
     */
    public function listGridMaterialDocumento($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                ->select(
                        'a.nuArtefato', 'ta.noTipoArtefato', 'a.txAssuntoComplementar', 'a.nuDigital', 'ps.noPessoa', 'ad.noTitulo', 'av.sqArtefatoVinculo')
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqArtefatoPai', 'av')
                ->leftJoin('a.sqArtefatoDossie', 'ad')
                ->leftJoin('a.sqTipoArtefatoAssunto', 'taa')
                ->leftJoin('taa.sqTipoArtefato', 'ta')
                ->leftJoin('a.sqPessoaArtefato', 'pa')
                ->leftJoin('pa.sqPessoaSgdoce', 'ps')
                ->andWhere('av.dtRemocaoVinculo IS NULL')
                ->andWhere('av.sqTipoVinculoArtefato = :material')
                ->setParameter('material', self::TIPO_VINCULO_MATERIAL)
                ->andWhere('av.sqArtefatoPai = :id')
                ->setParameter('id', $dto->getSqArtefato());
        return $queryBuilder;
    }

    /**
     * Consulta documento eletronico
     * @param \Core_Dto_Entity $dto
     * @return query DQL
     */
    public function listGridDocumentoEletronico($dto) {
        $inValue = $this->getArtefatoFilhoVinculoArtefato($dto);

        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('a.nuArtefato', 'a.nuDigital', 'ta.noTipoArtefato', 'ps.noPessoa', 'vtp.sqTipoPessoa', 'av.sqArtefatoVinculo')
                ->from('app:ArtefatoVinculo', 'av')
                ->innerJoin('av.sqArtefatoFilho', 'a')
                ->innerJoin('a.sqTipoArtefatoAssunto', 'tas')
                ->innerJoin('tas.sqTipoArtefato', 'ta')
                ->leftJoin('a.sqPessoaInteressadaArtefato', 'pia')
                ->leftJoin('pia.sqPessoaSgdoce', 'ps')
                ->leftJoin('ps.sqTipoPessoa', 'vtp')
                ->andWhere('av.dtRemocaoVinculo IS NULL');

        if ($inValue) {
            $queryBuilder->andWhere("a.sqArtefato in ({$inValue})");
        } else {
            $queryBuilder->andWhere("1 != 1");
        }

        return $queryBuilder;
    }

    /**
     * Consulta documento eletronico
     * @param \Core_Dto_Entity $dto
     * @return query DQL
     */
    public function listGridDocumentoVinculo($dto)
    {
        $query = $this->_em->createQueryBuilder();
        // lista de artefatos vinculados ao artefato principal
        $inValue = $this->getArtefatoFilhoVinculoArtefato($dto);
        // subquery que retornara o nome das pessoas ligadas ao documento

        $subSql1 = $this->_em->createQueryBuilder()->addSelect("ps1.sqPessoaSgdoce")->from('app:PessoaArtefato', 'p1')
            ->innerJoin('p1.sqPessoaFuncao','pf', 'WITH',$query->expr()->eq('pf.sqPessoaFuncao',\Core_Configuration::getSgdocePessoaFuncaoOrigem()))
            ->leftJoin('p1.sqPessoaSgdoce', 'ps1')->andWhere('p1.sqArtefato = av.sqArtefatoPai');

        $subSql2 = $this->_em->createQueryBuilder()->addSelect("p2.noPessoa")->from('app:PessoaSgdoce', 'p2')
                ->where("p2.sqPessoaSgdoce = ({$subSql1->getDQL()})");
        // query principal
        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('CASE WHEN ta.sqTipoArtefato = 2 THEN a.nuArtefato ELSE a.nuDigital as nuDigital',
                         'a.nuArtefato', 'a.txAssuntoComplementar', 'ta.noTipoArtefato', 'av.sqArtefatoVinculo',
                         'ad.noTitulo', "({$subSql2->getDQL()}) noPessoa")
                ->from('app:Artefato', 'a')
                ->leftJoin('a.sqArtefatoFilho', 'av')
                ->leftJoin('a.sqArtefatoDossie', 'ad')
                ->leftJoin('a.sqTipoArtefatoAssunto', 'tas')
                ->leftJoin('tas.sqTipoArtefato', 'ta')
                ->andWhere('av.dtRemocaoVinculo IS NULL');

        if ($inValue) {
            $queryBuilder->andWhere("a.sqArtefato in ({$inValue})")
                    ->andWhere('av.sqArtefatoPai = :sqArtefatoPai')
                    ->setParameter('sqArtefatoPai', $dto->getSqArtefato());
        } else {
            $queryBuilder->andWhere('1!=1');
        }
        $queryBuilder->getQuery()->execute();

        return $queryBuilder;
    }

    /**
     * Consulta material apoio documento
     * @param \Core_Dto_Entity $dto
     * @return query DQL
     */
    public function listGridMaterialApoioDocumento($dto) {
        $queryBuilder = $this->_em->createQueryBuilder()
                ->select(
                        'a.nuArtefato', 'ta.noTipoArtefato', 'a.txAssuntoComplementar', 'a.nuDigital', 'av.sqArtefatoVinculo')
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqArtefatoFilho', 'av')
                ->leftJoin('a.sqArtefatoDossie', 'ad')
                ->leftJoin('a.sqTipoArtefatoAssunto', 'taa')
                ->leftJoin('taa.sqTipoArtefato', 'ta')
                ->andWhere('av.dtRemocaoVinculo IS NULL')
                ->andWhere("a.sqArtefato in ({$inValue})");

        return $queryBuilder;
    }

    /**
     * Consulta artefato filho vinculo artefato
     * @param \Core_Dto_Entity $dto
     * @param boolean $tipoConsulta
     */
    public function getArtefatoFilhoVinculoArtefato($dto)
    {
        $arrResult = array();
        if ($dto->getSqArtefato()) {
            $queryBuilder = $this->_em->createQueryBuilder()
                    ->select('a.sqArtefato')
                    ->from('app:Artefato', 'a')
                    ->innerJoin('a.sqArtefatoFilho', 'av')
                    ->andWhere('av.dtRemocaoVinculo IS NULL')
                    ->andWhere('av.sqArtefatoPai = :id')
                    ->setParameter('id', $dto->getSqArtefato())
                    ->andWhere('av.sqTipoVinculoArtefato = :material')
                    ->setParameter('material', $dto->getSqTipoVinculo())
                    ->andWhere('av.dtRemocaoVinculo IS NULL');

            $resResult = $queryBuilder->getQuery()->getArrayResult();

            foreach ($resResult as $value) {
                $arrResult[] = $value['sqArtefato'];
            }
            return implode(',', $arrResult);
        }
        return NULL;
    }

    /**
     * Obtém os dados da pessoa
     * @return array
     */
    public function findNumeroArtefato(\Core_Dto_Abstract $dto, $maxResults = 10) {
        $query = mb_strtolower($dto->getQuery(), 'UTF-8');
        $queryBuilder = $this->getEntityManager()
                ->createQueryBuilder()
                ->distinct()
                ->select('a', 'afp')
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqPessoa', 'pa')
                ->innerJoin('a.sqTipoArtefatoAssunto', 'taa')
                ->innerJoin('taa.sqTipoArtefato', 'ta')
                ->leftJoin('a.sqArtefatoProcesso', 'afp')
                ->where("LOWER(a.nuArtefato) like '%$query%'")
                ->orderBy('a.nuArtefato', 'DESC');

        if ($dto->getExtraParam()) {//caso tenha sido selecionado tipo de artefato
            $queryBuilder->andWhere('ta.sqTipoArtefato = :sqTipoArtefato')
                         ->setParameter('sqTipoArtefato', $dto->getExtraParam());
        }else{
            $queryBuilder->orWhere('ta.sqTipoArtefato = :sqTipoArtefato')
            ->setParameter('sqTipoArtefato', \Core_Configuration::getSgdoceTipoArtefatoDocumento());
            $queryBuilder->orWhere('ta.sqTipoArtefato = :sqTipoArtefato')
            ->setParameter('sqTipoArtefato', \Core_Configuration::getSgdoceTipoArtefatoProcesso());
//            $queryBuilder->orWhere('ta.sqTipoArtefato = :sqTipoArtefato')
//            ->setParameter('sqTipoArtefato', 3);
        }

        if ($dto->hasSqTipoDocumento()) {//caso tenha sido selecionado tipo de documento
            $queryBuilder->andWhere('a.sqTipoDocumento = :sqTipoDocumento')
            ->setParameter('sqTipoDocumento', $dto->getSqTipoDocumento());
        }
        
        $queryBuilder->setMaxResults($maxResults);

        $res = $queryBuilder->getQuery()->getArrayResult();
        $out = array();
        
        $maskNumberFilter = new \Core_Filter_MaskNumber();

        foreach ($res as $item) {
            $nuArtefato = $item['nuArtefato'];

            if ($item['sqArtefatoProcesso']) {
                if ($item['sqArtefatoProcesso']['coAmbitoProcesso'] == Processo::T_TIPO_AMBITO_PROCESSO_FEDERAL) {
                    
                    $mask = null;
                    switch( strlen($nuArtefato) ){
                        case 21:
                            $mask = Processo::T_MASK_21_DIGITS;
                            break;
                        case 17:
                            $mask = Processo::T_MASK_17_DIGITS;
                            break;
                        case 15:
                            $mask = Processo::T_MASK_15_DIGITS;
                            break;
                    }
                    
                    if( !is_null($mask) ) {
                        $maskNumberFilter->setMask($mask);
                        $nuArtefato = $maskNumberFilter->filter($nuArtefato);
                    }
                }
            }

            $out[$item['nuArtefato']] = $nuArtefato;
        }
        return $out;
    }

    /**
     * método que pesquisa numero do artefato para preencher autocomplete
     * @param string $term
     * @return multitype:NULL
     */
    public function findNumeroDigital($term, $dto = NULL, $maxResults = 10)
    {
        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select('a,taa')
                ->distinct()
                ->from('app:Artefato', 'a')
                ->innerJoin('a.sqPessoa', 'pa')
                ->innerJoin('a.sqTipoArtefatoAssunto', 'taa')
                ->innerJoin('taa.sqTipoArtefato', 'ta')
                ->orderBy('a.nuDigital');

        if (isset($term)) {
            $term = mb_strtolower($term, 'UTF-8');
            $queryBuilder->where("LOWER(a.nuDigital) like '%$term%'");
        }

        if ($dto->getExtraParam()) {//caso tenha sido selecionado tipo de artefato
            $queryBuilder->andWhere('ta.sqTipoArtefato = :sqTipoArtefato')
                    ->setParameter('sqTipoArtefato', $dto->getExtraParam());
        }else{
            $queryBuilder->orWhere('ta.sqTipoArtefato = :sqTipoArtefato')
            ->setParameter('sqTipoArtefato', \Core_Configuration::getSgdoceTipoArtefatoDocumento());
            $queryBuilder->orWhere('ta.sqTipoArtefato = :sqTipoArtefato')
            ->setParameter('sqTipoArtefato', \Core_Configuration::getSgdoceTipoArtefatoProcesso());
//            $queryBuilder->orWhere('ta.sqTipoArtefato = :sqTipoArtefato')
//            ->setParameter('sqTipoArtefato', \Core_Configuration::getSgdoceTipoArtefatoDossie());
        }

        if ($dto->hasSqTipoDocumento()) {//caso tenha sido selecionado tipo de documento
            $queryBuilder->andWhere('a.sqTipoDocumento = :sqTipoDocumento')
            ->setParameter('sqTipoDocumento', $dto->getSqTipoDocumento());
        }

        if ($dto->getUsuario()) {
            $pessoaSgdoce = $this->_em->getRepository('app:PessoaSgdoce')
                ->findBySqPessoaCorporativo($dto->getUsuario());
            $queryBuilder->innerJoin('a.sqPessoaArtefato','pa')
                ->innerJoin('pa.sqPessoaSgdoce','ps')
                ->andWhere('pa.sqPessoaSgdoce = :pessoaSgdoce')
                ->andWhere('pa.sqPessoaFuncao in(:pessoaFuncao)')
                ->setParameter('pessoaSgdoce',$pessoaSgdoce)
                ->setParameters(array('pessoaFuncao' => array(\Core_Configuration::getSgdocePessoaFuncaoAutor(),
                       \Core_Configuration::getSgdocePessoaFuncaoOrigem())
                    )
                );
        }

        $queryBuilder->setMaxResults($maxResults);
        
        $res = $queryBuilder->getQuery()->execute();

        $out = array();
        if ($res) {
            if ($dto->getRetornaIdArtefato()) {
                foreach ($res as $item) {
                    $out[$item->getSqArtefato()] = $item->getNuDigital() ? : $item->getNuArtefato();
                }
            } else {
                foreach ($res as $item) {
                    $out[$item->getNuDigital()] = $item->getNuDigital();
                }
            }
        }

        return $out;
    }

    /**
     * Analisa duplicidade
     * @param Core_Dto_Entity $dto
     * @return array
     */

    public function analisarDuplicidade($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
            ->select(
                'a.sqArtefato', 'a.nuArtefato'/*, 'puo.noUnidadeOrg'*/
            )
            ->from('app:Artefato', 'a')
            ->leftJoin('a.sqPessoaArtefato', 'pa')
//             ->leftJoin('pa.sqPessoaUnidadeOrg', 'puo')
            ->leftJoin('a.sqTipoDocumento', 'td')
            ->andWhere('a.nuArtefato = :nuArtefato')
            ->setParameter('nuArtefato', $dto->getNuArtefato())
//             ->andWhere('puo.noUnidadeOrg = :noUnidadeOrg')
            ->setParameter('noUnidadeOrg', $dto->getNoUnidadeOrg())
            ->andWhere('a.sqTipoDocumento = :sqTipoDocumento')
            ->setParameter('sqTipoDocumento', $dto->getSqTipoDocumento());
        $res = $queryBuilder->getQuery()->execute();
        return $res;
    }

    /**
     * Metodo responsavel por recuperar o Numero da Ultima Digital inserida (valida).
     * @return Array String
     */
    public function lastDigitalNumber()
    {
        $today = new \DateTime('now');
        $sql = "SELECT MAX(nu_digital)nu_digital
        FROM sgdoce.artefato
        WHERE {$today->format('Y')} = (SELECT DISTINCT date_part('year', max(dt_artefato)) FROM sgdoce.artefato);";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Metodo responsavel por recuperar o Numero da Ultima Digital inserida (valida).
     * @return Array String
     */
    public function recuperaUltimoNumeroArtefato()
    {
        $today = new \DateTime('now');
        $sql = "SELECT MAX(nu_artefato) nu_artefato
                FROM sgdoce.artefato
                WHERE {$today->format('Y')} = (SELECT DISTINCT date_part('year', max(dt_artefato)) FROM sgdoce.artefato);";

        $stmt = $this->getEntityManager()->getConnection()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Metodo responsavel por recuperar o Numero da Ultima Digital inserida (valida).
     * @return Array String
     */
    public function findTipoArtefato($dto) {
        $term = mb_strtolower($dto->getQuery(), 'UTF-8');
        $queryTipoArtefato = $this->_em
                ->createQueryBuilder()
                ->select("ta.sqTipoArtefato,ta.noTipoArtefato")
                ->from('app:TipoArtefato', 'ta')
                ->where("LOWER(ta.noTipoArtefato) like '%$term%'")
                ->andWhere('ta.sqTipoArtefato in('.
                    \Core_Configuration::getSgdoceTipoArtefatoDocumento().','.
                    \Core_Configuration::getSgdoceTipoArtefatoDossie().','.
                    \Core_Configuration::getSgdoceTipoArtefatoProcesso().')'
                );

        $queryTipoDocumento = $this->_em
                ->createQueryBuilder()
                ->select("td.sqTipoDocumento,td.noTipoDocumento")
                ->from('app:TipoDocumento', 'td')
                ->where("LOWER(td.noTipoDocumento) like '%$term%'");

        $queryTipoAssunto = $this->_em
                ->createQueryBuilder()
                ->select("a.sqAssunto, a.txAssunto")
                ->from('app:Assunto', 'a')
                ->where("LOWER(a.txAssunto) like '%$term%'");

        $queryUnion = "{$queryTipoArtefato->getQuery()->getSQL()} UNION
                       {$queryTipoDocumento->getQuery()->getSQL()} UNION
                       {$queryTipoAssunto->getQuery()->getSQL()}";

        $res = $this->_em->getConnection()->fetchAll($queryUnion);
        $out = array();

        if ($res) {
            if ($dto->getBuscaPeca()){
                foreach ($res as $item) {
                    $out[$item['no_tipo_artefato1']] = $item['no_tipo_artefato1'];
                }
            } else{
                foreach ($res as $item) {
                    $out[$item['sq_tipo_artefato0']] = $item['no_tipo_artefato1'];
                }
            }
        }
        return $out;
    }

    /**
     * método que pesquisa Titulo do Dossie para preencher autocomplete
     */
    public function searchTituloDossie($dto)
    {
        $search       = mb_strtolower($dto->getQuery(),'UTF-8');
        $queryBuilder = $this->_em->createQueryBuilder();

        $field = $queryBuilder->expr()
            ->lower($queryBuilder->expr()->trim('ad.noTitulo'));

        $query = $queryBuilder->select(
                'ad.noTitulo', 'a.sqArtefato'
            )
            ->from('app:Artefato', 'a')
            ->innerJoin('a.sqArtefatoDossie', 'ad')
            ->andWhere(
                $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $field .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            )
            ->orderBy('ad.noTitulo');

        $res = $queryBuilder->getQuery()->execute();
        $out = array();
        foreach ($res as $item) {
            $out[$item['sqArtefato']] = $item['noTitulo'];
        }
        return $out;
    }

    /**
     * método que retorna dados para Vizualizar o Artefato
     * @param \Core_Dto_Abstract $dto
     * @return array
     */
    public function findVisualizarArtefato(\Core_Dto_Search $dto)
    {
    	$query = mb_strtolower($dto->getQuery(), 'UTF-8');
    	$queryBuilder = $this->getEntityManager()
    	->createQueryBuilder()
    	->select('a.sqArtefato,
                IDENTITY(a.nuDigital) as nuDigital,
                a.nuArtefato,
                a.dtArtefato,
                a.dtPrazo,
                a.nuDiasPrazo,
                a.inDiasCorridos,
                a.dtArtefato,
                a.txAssuntoComplementar,
                a.inAssinaturaDigital,
    		ad.noTitulo,
                ta.sqTipoArtefato,
                td.noTipoDocumento,
                ass.txAssunto,
                p.noPrioridade,
                tp.txTipoPrioridade,
                ga.noGrauAcesso,
                ha.dtOcorrencia,
                eu.nuNupSiorg
                ')
                    ->distinct()
                    ->from('app:Artefato', 'a')
                    ->leftJoin('a.sqTipoDocumento', 'td')
                    ->leftJoin('a.nuDigital', 'eu')
                    ->leftJoin('a.sqTipoArtefatoAssunto', 'taa')
                    ->leftJoin('taa.sqTipoArtefato', 'ta')
                    ->leftJoin('taa.sqAssunto', 'ass')
                    ->leftJoin('a.sqTipoPrioridade', 'tp')
                    ->leftJoin('tp.sqPrioridade', 'p')
                    ->leftJoin('a.sqGrauAcessoArtefato', 'gaa')
                    ->leftJoin('gaa.sqGrauAcesso', 'ga')
                    ->leftJoin('a.sqHistoricoArtefato', 'ha')
                    ->leftJoin('a.sqArtefatoDossie', 'ad')
                    ->andWhere('a.sqArtefato = :sqArtefato')
                    ->setParameter('sqArtefato', $dto->getSqArtefato());

        $result = $queryBuilder->getQuery()->execute();
        if ($dto->getInOriginal() == 'TRUE') {
            $ultimoHistorico = $this->_em->createQueryBuilder()
            ->select('max(ha2.sqHistoricoArtefato)')
            ->from('app:HistoricoArtefato','ha2')
            ->where('ha2.sqArtefato = a.sqArtefato')
            ->getDQL();

            $queryBuilder
            ->leftJoin('a.sqHistoricoArtefato', 'ha')
            ->leftJoin('a.sqArtefatoFilho','av')
            ->andWhere('ha.sqPessoa = :sqPessoa')
            ->andWhere('av.inOriginal = :inOriginal')
            ->andWhere('av.dtRemocaoVinculo IS NULL')
            ->setParameter('sqPessoa', $dto->getUsuario())
            ->setParameter('inOriginal', 'false')
            ->expr()->in('ha.sqHistoricoArtefato', $ultimoHistorico);
        }
        return $result[0];
    }

    public function findArtefatoPecaProcesso($dto, $limit = null)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_artefato', 'sqArtefato', 'integer');
        $rsm->addScalarResult('in_original', 'inOriginal', 'integer');
        $rsm->addScalarResult('sq_pessoa', 'sqPessoa', 'integer');
        $rsm->addScalarResult('nu_digital', 'nuDigital', 'string');
        $rsm->addScalarResult('nu_artefato', 'nuArtefato', 'string');

        $sql = "
              select distinct a.sq_artefato,
                     CASE 
                        WHEN length(cast(a.nu_digital as text)) > 7 THEN
                            cast(a.nu_digital as text)
                        ELSE
                            lpad(cast(a.nu_digital as varchar), 7, '0')                             
                       END AS nu_digital,
                     a.nu_artefato,
                     av.in_original,
                     historico.sq_pessoa
              from sgdoce.artefato a
              inner join sgdoce.tipo_artefato_assunto taa on taa.sq_tipo_artefato_assunto = a.sq_tipo_artefato_assunto
              inner join sgdoce.assunto ass on ass.sq_assunto = taa.sq_assunto
              inner join sgdoce.tipo_artefato ta on taa.sq_tipo_artefato = ta.sq_tipo_artefato
              inner join sgdoce.pessoa_artefato pa on a.sq_artefato = pa.sq_artefato
              left join sgdoce.tipo_documento td on td.sq_tipo_documento = a.sq_tipo_documento
              left join sgdoce.artefato_vinculo av on av.sq_artefato_filho = a.sq_artefato
                 and av.in_original = true and dt_remocao_vinculo is null
              left join (select * from sgdoce.historico_artefato h
                 where sq_historico_artefato = (
                      select  max(sq_historico_artefato)
                      from sgdoce.historico_artefato hh
                      where hh.sq_artefato = h.sq_artefato)) as historico on historico.sq_artefato = a.sq_artefato";
        $wherePadrao = "where ta.sq_tipo_artefato = :sqTipoArtefato";
        $where = "and a.sq_tipo_documento = :sqTipoDocumento";
        $wherePeca = "where :identificador in(td.no_tipo_documento,ta.no_tipo_artefato,ass.tx_assunto)";
        $whereDigital = "and (CASE 
                        WHEN length(cast(a.nu_digital as text)) > 7 THEN
                            cast(a.nu_digital as text)
                        ELSE
                            lpad(cast(a.nu_digital as varchar), 7, '0')                             
                       END like '%{$dto->getQuery()}%')";

        //usando replace devido ao cast em nu_digital
        if ($dto->getSqTipoArtefato()){
            $sql = str_replace(':sqTipoArtefato', $dto->getSqTipoArtefato(), "$sql $wherePadrao") ;
        }

        if ($dto->getSqTipoDocumento()){
            $sql = str_replace(':sqTipoDocumento', $dto->getSqTipoDocumento(), "$sql $where") ;
        }

        if ($dto->getIdentificador()){
            $sql = str_replace(':identificador', $dto->getIdentificador(), "$sql $wherePeca") ;
        }

        if ($dto->getQuery()){
            $sql = "$sql $whereDigital";
        }

        $sql .= " ORDER BY nu_digital ";

        if (!is_null($limit)) {
           $sql .= " LIMIT {$limit} OFFSET 0";
        }
        
        $query = $this->_em->createNativeQuery($sql, $rsm);

        return $query->getResult();
    }

    public function findNumeroDigitalDossie($term, $dto, $limit = null)
    {
        $res = $this->findArtefatoPecaProcesso($dto, $limit);
        $out = array();
        foreach ($res as $item) {
            if ($dto->getInOriginal() == 'TRUE') {
                //vinculados como original
                if ($item['sqPessoa'] == $dto->getUsuario()){
                    if ($item['inOriginal'] == FALSE) {
                        $out[$item['nuDigital']] = "{$item['nuDigital']}";
                    }
                }
            } else {
                //vinculados como copia
                $out[$item['nuDigital']] = "{$item['nuDigital']}";
            }
        }

        return $out;
    }
}