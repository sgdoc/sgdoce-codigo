<?php

/**
 * Copyright 2012 do ICMBio
 *
 * Este arquivo é parte do programa SISICMBio
 * O SISICMBio é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro
 * dos termos da Licença Pública Geral GNU como publicada pela Fundação do Software Livre
 * (FSF); na versão 2 da Licença.
 *
 * Este programa é distribuído na esperança que possa ser  útil, mas SEM NENHUMA GARANTIA;
 * sem uma garantia implícita de ADEQUAÇÃO a qualquer  MERCADO ou APLICAÇÃO EM PARTICULAR.
 * Veja a Licença Pública Geral GNU/GPL em português para maiores detalhes.
 * Você deve ter recebido uma cópia da Licença Pública Geral GNU, sob o título "LICENCA.txt",
 * junto com este programa, se não, acesse o Portal do Software Público Brasileiro no
 * endereço www.softwarepublico.gov.br ou escreva para a Fundação do Software Livre(FSF) Inc.,
 * 51 Franklin St, Fifth Floor, Boston, MA  02110-1301, USA
 */

/**
 * @package     Core
 * @subpackage  Model
 * @subpackage  Repository
 * @name        Abstract
 * @category    Abstract Repository
 */
abstract class Core_Model_Repository_Abstract extends Doctrine\ORM\EntityRepository
{
    const T_ORDER_BY = 'ORDER BY';
    const T_LIMIT    = 'LIMIT';
    const T_OFFSET   = 'OFFSET';

    protected function _fetchPagination ($params, $queryBuilder, $withoutNativeCount=FALSE)
    {
        $method = '_fetchPaginationQueryBuilder';

        if ($queryBuilder instanceof \Doctrine\ORM\NativeQuery) {
            $method = '_fetchPaginationNativeQuery';
        }

        return $this->$method($params, $queryBuilder, $withoutNativeCount);
    }

    private function _fetchPaginationNativeQuery ($params, \Doctrine\ORM\NativeQuery $nQuery, $withoutNativeCount=FALSE)
    {
        $strQuery  = $nQuery->getSQL();

        //caso a query já possua um limit e offset
        if (strpos(strtolower($strQuery), ':limit') !== FALSE) {

            $paramLimit = $nQuery->getParameter('limit');
            if (! $paramLimit) {
                $nQuery->setParameter('limit', $params['iDisplayLength']);
            }
            
            if (strpos(strtolower($strQuery), ':offset') !== FALSE) {
                $paramOffset = $nQuery->getParameter('offset');
                if (! $paramOffset) {
                    $nQuery->setParameter('offset', $params['iDisplayStart']);
                }
            }
            $improvedQuery = $strQuery;
        } else {
            # encapsula a consulta enviada numa subconsulta
            # para permitir a paginacao e limitacao do resultado
            # sem necessitar alterar a query enviada
            $improvedQuery      = sprintf('SELECT * FROM (%s) AS foo ', $strQuery);
            $improvedQueryCount = sprintf('SELECT count(1) total FROM (%s) C', $improvedQuery);

            # se necesario, aplica ordenacao
            if (isset($params['order'])) {
                foreach ($params['order'] as $order) {
                    $orderBy[] = sprintf('%s %s', $order['sort'], $order['order']);
                }

                if (!empty($orderBy)) {
                    $improvedQuery .= sprintf(' %s %s', self::T_ORDER_BY, implode(', ', $orderBy));
                }
            }

            $improvedQuery .= sprintf(
                ' %1$s %4$d %2$s %3$d'
                , self::T_LIMIT
                , self::T_OFFSET
                , $params['iDisplayStart']
                , $params['iDisplayLength']);
        }

        if( !$withoutNativeCount ){
            /**
             * Cria uma query para recupera o total geral de registros
             */
            $rsmCount = new \Doctrine\ORM\Query\ResultSetMapping($this->_em);
            $rsmCount->addScalarResult('total','total','integer');

            $nativeQueryCount = $this->_em->createNativeQuery($improvedQueryCount, $rsmCount);
            foreach ($nQuery->getParameters() as $key => $value) {
                $nativeQueryCount->setParameter(":{$key}", $value);
            }
        }

        $nQuery->setSQL($improvedQuery);

        $result['data']  = $nQuery->getResult();

        if(!$withoutNativeCount){
            $total = $nativeQueryCount->getSingleScalarResult();
        }else{
            $total = 0;
            if ($result['data']) {
                //recupera o total_record da primeria posição do resultado
                if(!isset($result['data'][0]['totalRecord'])){
                    trigger_error('Não foi setada coluna "total_record no nativeQuery"', E_USER_ERROR);
                }
                $total = $result['data'][0]['totalRecord'];
            }
        }
        $result['total'] = $total;

        return $result;
    }

    private function _fetchPaginationQueryBuilder ($params, $queryBuilder, $withoutNativeCount=FALSE)
    {
        $queryBuilder->setFirstResult($params['iDisplayStart'])
                     ->setMaxResults($params['iDisplayLength']);

        if (isset($params['order'])) {
            foreach ($params['order'] as $order) {
                $queryBuilder->addOrderBy($order['sort'], $order['order']);
            }
        }

        $query = $queryBuilder->getQuery()->setHydrationMode(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        $paginator = new Doctrine\ORM\Tools\Pagination\Paginator($query);

        $result['data'] = $paginator->getIterator();
        $result['total'] = $paginator->count();

        return $result;
    }

    public function searchPage($method, $params, $withoutNativeCount=FALSE)
    {
        $queryBuilder = $this->{$method}($params);

        $result = $this->_fetchPagination($params, $queryBuilder, $withoutNativeCount);

        return $result;
    }

    public function dtoSearchToSearchParams($dto)
    {
        $getters = $dto->getApi();

        $params = array();

        foreach ($getters as $key => $getter) {
            $params[$key] = $dto->$getter();
            if ($params[$key] instanceof Core_Dto_Search) {
                $params[$key] = $this->dtoSearchToSearchParams($params[$key]);
            }
        }

        return $params;
    }

    public function searchPageDto($method, \Core_Dto_Search $dto, $withoutNativeCount=FALSE)
    {
        $queryBuilder = $this->{$method}($dto);
        $params = $this->dtoSearchToSearchParams($dto);
        $result = $this->_fetchPagination($params, $queryBuilder, $withoutNativeCount);

        return $result;
    }

    /**
     * @return Core_Messaging_Gateway
     */
    public function getMessaging()
    {
        return Core_Messaging_Manager::getGateway('Model');
    }

    public function translate($text)
    {
        $from = 'áàâãäéèêëíìîĩïóòôõöúùûũüÁÀÂÃÄÉÈÊËÍÌÎĨÏÓÒÔÕÖÚÙÛŨÜçÇ';
        $to = 'aaaaaeeeeiiiiiooooouuuuuAAAAAEEEEIIIIIOOOOOUUUUUcC';

        return str_replace($this->mb_str_split($from), $this->mb_str_split($to), $text);
    }

    private function mb_str_split($text)
    {
        return preg_split('~~u', $text, null, PREG_SPLIT_NO_EMPTY);
    }

    public function removeAccent($text)
    {
        $from = 'áàâãäéèêëíìîĩïóòôõöúùûũüÁÀÂÃÄÉÈÊËÍÌÎĨÏÓÒÔÕÖÚÙÛŨÜçÇ';
        $to = "aaaaaeeeeiiiiiooooouuuuuAAAAAEEEEIIIIIOOOOOUUUUUcC";
        $text = str_replace($this->mb_str_split($from), $this->mb_str_split($to), $text);
        $text = str_replace('%','',$text);

        return !empty($text) ? '%'.$text .'%' : $text;
    }

    /**
     * Monta combo com campos do Tipo Pessoa
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array The objects.
     * @return array
     */
    public function getComboDefault(array $criteria = array(), array $orderBy = NULL, $limit = NULL, $offset = NULL)
    {
        $itens = array();
        $result = $this->_em->getRepository($this->getClassName())->findBy($criteria, $orderBy, $limit, $offset);

        $className = $this->getClassName();
        $metadata = $this->_em->getClassMetadata(get_class(new $className()));
        $fieldNames = $metadata->getFieldNames();

        $getSqCodigo = 'get' . ucfirst($fieldNames[0]);
        $getNoDescricao = 'get' . ucfirst($fieldNames[1]);

        foreach ($result as $item) {
            $itens[$item->{$getSqCodigo}()] = $item->{$getNoDescricao}();
        }

        return $itens;
    }

}