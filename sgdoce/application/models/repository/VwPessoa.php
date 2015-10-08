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
 * Classe para Repository Pessoa
 *
 * @package      Model
 * @subpackage   Repository
 * @name         VwPessoa
 * @version      1.0.0
 * @since        2012-11-20
 */
class VwPessoa extends VwPessoaExtensao
{

    /**
     * Váriavel Pessoa
     * @var string
     * @name app:VwPessoa
     * @access private
     */
    private $_enName = 'app:VwPessoa';

    /**
     * método que lista pessoa
     * @param array $params
     * @return Query
     */
    public function listPessoa($params, $limit = 10)
    {
        if (empty($params['tp'])) {
            $params['tp'] = NULL;
        }

        $select = 'p, prf';
        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $queryBuilder->select($select)
                ->distinct()
                ->from($this->_enName, 'p')
                ->leftJoin('p.sqProfissional', 'prf')
                ->orderBy('p.noPessoa');

        if (isset($params['extraParam'])) {
            switch ($params['extraParam']) {
                case \Core_Configuration::getSgdoceTipoPessoaEstrangeiro():
                case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica():
                    $query->innerJoin('p.sqPessoaFisica', 'pf');
                    if ($params['extraParam'] == \Core_Configuration::getSgdoceTipoPessoaPessoaFisica()) {
                        $query->andWhere('pf.sqNacionalidade = :sqNacionalidade')
                                ->setParameter('sqNacionalidade', \Core_Configuration::getSgdoceTipoPessoaPessoaFisica())
                                ->orWhere('pf.sqNacionalidade IS NULL');
                    } else {
                        $query->andWhere('pf.sqNacionalidade <> :sqNacionalidade')
                                ->setParameter('sqNacionalidade', \Core_Configuration::getSgdoceTipoPessoaPessoaFisica());
                    }
                    break;
                case \Core_Configuration::getSgdoceTipoPessoaMinisterioPublico():
                    $query->leftJoin('p.sqUnidadeOrgInterna', 'ui')
                            ->leftJoin('p.sqRppn', 'rpn');
                    break;
                case \Core_Configuration::getSgdoceTipoPessoaOutrosOrgaos():
                    $query->innerJoin('p.sqUnidadeOrgExterna', 'ue');

                    break;
                default:
                    $query->andWhere('p.sqTipoPessoa = :sqTipoPessoa')
                            ->setParameter('sqTipoPessoa', $params['extraParam']);
                    break;
            }
        }

        if (!is_null($params['query'])) {
            $search = mb_strtolower($params['query'], 'UTF-8');
            $field = $queryBuilder->expr()
                    ->lower($queryBuilder->expr()->trim('p.noPessoa'));

            $query->andWhere(
                    $queryBuilder->expr()
                            ->like(
                                    'clear_accentuation(' . $field . ')', $queryBuilder->expr()
                                    ->literal($this->removeAccent('%' . $search . '%'))
                            )
            );
        }
        $query->setMaxResults($limit);

        return $query->getQuery()->execute();
    }

    /**
     * método que retorna os dados da pessoa
     * @param dto Search
     * @return array
     */
    public function getPessoaDados(\Core_Dto_Search $search)
    {
        $filter = new \Zend_Filter_Digits();

        $select = 'p';
        $nuCpfCnpjPassaporte = '';
        if ($search->getNuCpfCnpjPassaporte()) {
            $nuCpfCnpjPassaporte = $filter->filter($search->getNuCpfCnpjPassaporte());
        }

        switch ($search->getSqTipoPessoa()) {
            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica() :
                if ($search->getSqNacionalidade() == \Sgdoce_Constants::NACIONALIDADE_BRASILEIRA) {
                    $select = 'p,pf';
                } else {
                    if ($nuCpfCnpjPassaporte != '') {
                        $select = 'p,pf,d';
                    } else {
                        $select = 'p,pf';
                    }
                }
                break;
            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica() :
                $select = 'p,pj';
                break;
            case \Core_Configuration::getSgdoceTipoPessoaMinisterioPublico() :
            case \Core_Configuration::getSgdoceTipoPessoaOutrosOrgaos() :
                $select = 'p';
                break;
        }

        $query = $this->_em
                ->createQueryBuilder()
                ->select($select)
                ->from($this->_enName, 'p');

        $this->addWhere($query, $search, $nuCpfCnpjPassaporte);

        $result = $query->getQuery()->execute();

        return $result ? $result[0] : $result;
    }


    public function getDadosPessoa(\Core_Dto_Search $search)
    {
        $sqTipoPessoa = $search->getSqTipoPessoa();

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_pessoa', 'sqPessoa', 'integer');
        $rsm->addScalarResult('no_pessoa', 'noPessoa', 'string');

        $query = $this->_em->createNativeQuery(NULL, $rsm);

        $filter = new \Zend_Filter_Digits();

        if ($sqTipoPessoa == \Core_Configuration::getSgdoceTipoPessoaPessoaFisica()) {
            $rsm->addScalarResult('nu_cpf', 'nuCpf', 'string');

            $sql = "SELECT
                        p.sq_pessoa,
                        p.no_pessoa,
                        pf.nu_cpf
                    FROM  corporativo.vw_pessoa p
                    JOIN  corporativo.vw_pessoa_fisica pf on pf.sq_pessoa = p.sq_pessoa";


            if ($search->getNuCpfCnpjPassaporte()) {
                $sql .= " WHERE pf.nu_cpf = :nuCpf";
                $query->setParameter('nuCpf', $filter->filter($search->getNuCpfCnpjPassaporte()), 'string');
            } elseif  ($search->getSqPessoaCorporativo()) {
                $sql .= " WHERE p.sq_pessoa = :sqPessoa";
                $query->setParameter('sqPessoa', $search->getSqPessoaCorporativo(), 'integer');
            }

            $query->setSQL($sql);
        }

       if ($sqTipoPessoa == \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica()) {
           $rsm->addScalarResult('nu_cnpj', 'nuCnpj', 'string');

           $sql = 'SELECT
                       p.sq_pessoa,
                       p.no_pessoa,
                       pj.nu_cnpj
                   FROM corporativo.vw_pessoa p
                   JOIN corporativo.vw_pessoa_juridica pj on pj.sq_pessoa = p.sq_pessoa';

           if  ($search->getNuCpfCnpjPassaporte()) {
               $sql .= ' WHERE pj.nu_cnpj = :nuCnpj';
               $query->setParameter('nuCnpj', $filter->filter($search->getNuCpfCnpjPassaporte()), 'string');
           } elseif ($search->getSqPessoaCorporativo()) {
                $sql .= " WHERE p.sq_pessoa = :sqPessoa";
                $query->setParameter('sqPessoa', $search->getSqPessoaCorporativo(), 'integer');
           }

           $query->setSQL($sql);
        }

        $result = $query->getResult();


        return !empty($result) ? current($result) : array();
    }

    /**
     * método que retorna os dados da assinatura
     * @param query
     * @return query
     */
    public function getPessoaAssinatura(\Core_Dto_Search $search)
    {
        switch ($search->getSqTipoPessoa()) {
            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica():
                $query = $this->_em->createQueryBuilder()
                        ->select('ps.sqPessoa,
                              ps.noPessoa,
                              c.sqCargo,
                              c.noCargo,
                              un.sqUnidadeOrg,
                              un.noUnidadeOrg')
                        ->from('app:VwProfissional', 'p')
                        ->leftJoin('p.sqPessoa', 'ps')
                        ->leftJoin('p.sqCargo', 'c')
                        ->leftJoin('p.sqUnidadeExercicio', 'un');

                $query->andWhere('ps.sqPessoa = :sqPessoa')
                        ->setParameter('sqPessoa', $search->getSqPessoa());
                return $query->getQuery()->execute();
                break;
            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica():
                $select = 'pti.sqPessoa,pti.noPessoa,ct.sqCargo,ct.noCargo,unt.noUnidadeOrg';
                ;
                break;
            case \Core_Configuration::getSgdoceTipoPessoaEstrangeiro():
                $select = 'psu.sqPessoa,psu.noPessoa,cs.sqCargo,cs.noCargo,uns.noUnidadeOrg';
                break;
        }

        $query = $this->_em->createQueryBuilder()
                ->select($select)
                ->from('app:VwChefia', 'c')
                ->innerJoin('c.sqDestinacaoFgDas', 'df')
                ->leftJoin('c.sqProfissionalTitular', 'pt')
                ->leftJoin('c.sqProfissionalSubstituto', 'ps')
                ->leftJoin('pt.sqProfissional', 'pti')
                ->leftJoin('ps.sqProfissional', 'psu')
                ->leftJoin('pt.sqCargo', 'ct')
                ->leftJoin('ps.sqCargo', 'cs')
                ->leftJoin('pt.sqUnidadeExercicio', 'unt')
                ->leftJoin('ps.sqUnidadeExercicio', 'uns');

        $query->andWhere('df.sqUnidadeOrgDestinada = :sqUnidade')
                ->setParameter('sqUnidade', $search->getSqUnidadeOrg());

        return $query->getQuery()->execute();
    }

    /**
     * Realiza a busca de informacoes da pessoa pelo codigo do documento CPF/CNPJ/RegistroEstrangeiro
     * @param \Core_Dto_Search $search
     * @return Object
     */
    public function buscaPessoaPorDocumento(\Core_Dto_Search $search)
    {
        $filter = new \Zend_Filter_Digits();
        $nuCpfCnpjPassaporte = $filter->filter($search->getNuCpfCnpjPassaporte());

        switch ($search->getSqTipoPessoa()) {
            case \Core_Configuration::getSgdoceTipoPessoaPessoaFisica() :
                if ($search->getSqTipoPessoa() == \Core_Configuration::getSgdoceTipoPessoaPessoaFisica()) {
                    $select = 'p,pf';
                } else {
                    $select = 'p,pf,d';
                }
                break;
            case \Core_Configuration::getSgdoceTipoPessoaPessoaJuridica() :
                $select = 'p,pj';
                break;
            case \Core_Configuration::getSgdoceTipoPessoaMinisterioPublico() :
            case \Core_Configuration::getSgdoceTipoPessoaOutrosOrgaos() :
                $select = 'p';
                break;
        }

        $query = $this->_em->createQueryBuilder()
                ->select($select)
                ->from($this->_enName, 'p');

        $this->addWhere($query, $search, $nuCpfCnpjPassaporte);

        $result = $query->getQuery()->execute();
        return $result ? $result[0] : $result;
    }

    /**
     * Verifica se o usuário tem acesso a uma determinada rota
     * @param \Core_Dto_Search $dtoSearch
     * @return Object
     */
    public function verificaUsuarioRota(\Core_Dto_Search $dtoSearch)
    {

        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_pessoa', 'sqPessoa', 'integer');

        $query = $this->_em->createNativeQuery('
                SELECT * FROM sicae.vw_usuario u
                INNER JOIN sicae.vw_usuario_perfil up on up.sq_usuario = u.sq_usuario
                INNER JOIN sicae.vw_perfil p on p.sq_perfil = up.sq_perfil
                INNER JOIN sicae.vw_perfil_funcionalidade pf on pf.sq_perfil = p.sq_perfil
                INNER JOIN sicae.vw_funcionalidade f on f.sq_funcionalidade = pf.sq_funcionalidade
                INNER JOIN sicae.vw_rota r on f.sq_funcionalidade = r.sq_funcionalidade
                WHERE u.sq_pessoa = :sqPessoa
                AND   r.tx_rota = :txRota', $rsm);

        $query->setParameter('sqPessoa', $dtoSearch->getSqPessoa());
        $query->setParameter('txRota', $dtoSearch->getTxRota());
        $result = $query->getResult();

        if (count($result) > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Verifica se o usuário tem acesso a uma determinada rota
     * @param \Core_Dto_Search $dtoSearch
     *
     * @return Array[Object]
     */
    public function searchPessoaUnidade(\Core_Dto_Search $objDtoSearch, $limit = 10)
    {
        $strNoPessoa = mb_strtolower($objDtoSearch->getQuery(), 'UTF-8');

        $objQBuilder = $this->_em->createQueryBuilder();
        $objField = $objQBuilder->expr()
                ->lower($objQBuilder->expr()->trim('p.noPessoa'));

        $objQBuilder->select("p.noPessoa, p.sqPessoa, IDENTITY(up.sqUnidadeOrgPessoa) AS sqUnidadeOrgPessoa")
                ->distinct('p.noPessoa')
                ->distinct('p.sqPessoa')
                ->from('app:VwUsuarioPerfil', 'up')
                ->join("up.sqUsuario", 'u')
                ->join("u.sqPessoa", "p")
                ->where(
                        $objQBuilder->expr()
                        ->like("clear_accentuation({$objField})",
                               $objQBuilder->expr()
                                    ->literal($this->removeAccent('%' . $strNoPessoa . '%'))
                ));

        if( $objDtoSearch->getSqUnidadeOrg() != '' ) {
                $objQBuilder->andWhere("up.sqUnidadeOrgPessoa = :sqUnidadeOrgPessoa")
                            ->setParameter("sqUnidadeOrgPessoa", $objDtoSearch->getSqUnidadeOrg());
        }
        $objQBuilder->setMaxResults($limit);

        return $objQBuilder->getQuery()->execute();
    }

    /**
     * Método responsável por realizar a busca dos dados para o autocomplete de pessoa
     *
     * @param array $params
     * @return array $out
     */
    public function autocomplete ($dtoSearch, $limit)
    {
        $rsm = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
        $rsm->addScalarResult('sq_pessoa', 'sqPessoa', 'integer');
        $rsm->addScalarResult('no_pessoa', 'noPessoa', 'string');

        $sqTipoPessoa = $dtoSearch->getExtraParam();
        switch ($sqTipoPessoa) {
            case \Core_Configuration::getCorpTipoPessoaFisica():
                $sql = " SELECT
                         p.sq_pessoa,
                         coalesce(p.no_pessoa) as no_pessoa
                     FROM corporativo.vw_pessoa p
                     JOIN corporativo.vw_pessoa_fisica pf on pf.sq_pessoa = p.sq_pessoa
                     WHERE (corporativo.fn_normaliza_string(p.no_pessoa) like '%' || corporativo.fn_normaliza_string(:noPessoa) || '%')
                     ORDER BY no_pessoa asc
                     LIMIT :limit";
                break;
            case \Core_Configuration::getCorporativoTipoPessoaUnidadeOrganizacional():
                $sql = " SELECT sq_pessoa, no_pessoa
                     FROM
                     (
                         SELECT
                             p.sq_pessoa,
                             coalesce(uo.sg_unidade_org || ' - ', '') || coalesce(p.no_pessoa, '') as no_pessoa,
                             corporativo.fn_normaliza_string(p.no_pessoa) AS column_aux
                         FROM corporativo.vw_pessoa p
                         JOIN corporativo.vw_unidade_org uo on uo.sq_pessoa = p.sq_pessoa
                         WHERE (corporativo.fn_normaliza_string(p.no_pessoa) like '%' || corporativo.fn_normaliza_string(:noPessoa) || '%')

                         UNION

                         SELECT
                             p.sq_pessoa,
                             coalesce(uo.sg_unidade_org || ' - ', '') || coalesce(p.no_pessoa, '') as no_pessoa,
                             corporativo.fn_normaliza_string(uo.sg_unidade_org) AS column_aux
                         FROM corporativo.vw_pessoa p
                         JOIN corporativo.vw_unidade_org uo on uo.sq_pessoa = p.sq_pessoa
                         WHERE (corporativo.fn_normaliza_string(uo.sg_unidade_org) like '%' || corporativo.fn_normaliza_string(:noPessoa) || '%')
                     ) tb_auxiliar
                     ORDER BY column_aux asc
                     LIMIT :limit";
                break;
            case \Core_Configuration::getCorpTipoPessoaJuridica():
                $sql = " SELECT
                         p.sq_pessoa,
                         coalesce(p.no_pessoa) as no_pessoa
                     FROM corporativo.vw_pessoa p
                     JOIN corporativo.vw_pessoa_juridica pj on pj.sq_pessoa = p.sq_pessoa
                     WHERE (corporativo.fn_normaliza_string(p.no_pessoa) like '%' || corporativo.fn_normaliza_string(:noPessoa) || '%')
                     ORDER BY no_pessoa asc
                     LIMIT :limit";
                break;
            case \Core_Configuration::getCorporativoTipoPessoaUnidadeOrgExterna() :
                $sql = " SELECT
                                uoe.sq_pessoa,
                                coalesce(uoe.sg_pai  || ' - ', '') || coalesce(uoe.no_pessoa, '') as no_pessoa
                           FROM corporativo.vw_unidade_org_externa uoe
                          WHERE (corporativo.fn_normaliza_string(uoe.no_pessoa) like '%' || corporativo.fn_normaliza_string(:noPessoa) || '%') OR
                                (corporativo.fn_normaliza_string(uoe.sg_unidade_org) like '%' || corporativo.fn_normaliza_string(:noPessoa) || '%')

                        UNION

                         SELECT rppn.sq_pessoa,
                                rppn.sg_rppn as no_pessoa
                           FROM corporativo.vw_rppn rppn
                          WHERE (corporativo.fn_normaliza_string(rppn.sg_rppn) like '%' || corporativo.fn_normaliza_string(:noPessoa) || '%')

                         ORDER BY no_pessoa asc
                     LIMIT :limit";

                break;
        }

        $result = $this->_em->createNativeQuery($sql, $rsm)
                           ->setParameter('noPessoa', str_replace(' ', '%', $dtoSearch->getQuery()), 'string')
                           ->setParameter('limit', $limit, 'integer')
                           ->getResult();

        $out = array();
        foreach ($result as $item) {
            $out[$item['sqPessoa']] = $item['noPessoa'];
        }
        return $out;
    }
}
