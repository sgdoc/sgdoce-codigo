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
 * Classe para Repository Endereco
 *
 * @package      Model
 * @subpackage   Repository
 * @name          VwEndereco
 * @version      1.0.0
 * @since        2012-11-20
 */
class VwEndereco extends \Core_Model_Repository_Base
{
    /**
     * Constante para receber o valor "do padrao modelo documento atos 0 (zero)
     * @var  integer
     * @name ZER
     */
    const ZER  = 0;

    /**
     * Variável para receber a entidade VwEndereco
     * @var    string
     * @access protected
     * @name   $_enName
     */
    private $_enName = 'app:VwEndereco';

    /**
     * Obtém endereço de um usuário
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function buscarEnderecoPorIdUsuario(\Core_Dto_Search $dto)
    {

        $query = $this->_em->createQueryBuilder();
        $query->select('e')
                ->from($this->_enName, 'e')
                ->where('e.sqPessoa = :idUsuario')
                ->setParameter('idUsuario', $dto->getSqUsuario())
        ;
        $result = $query->getQuery()->getResult();

        if(!$result){
            $result[] = new \Sgdoce\Model\Entity\VwEndereco();
        }
        return $result[0];
    }

    /**
     * Obtém endereço
     * @param type $sqUsuario
     * @return array
     */
    public function findEndereco($sqUsuario)
    {
        $query = $this->_em->createQueryBuilder();
        $query->select('e,m,es')
         ->from($this->_enName, 'e')
        ->innerJoin('e.sqMunicipio', 'm')
        ->innerJoin('m.sqEstado', 'es')
        ->andwhere('e.sqPessoa = :idUsuario')
        ->setParameter('idUsuario', $sqUsuario);
        $result = $query->getQuery()->execute();
        if(count($result) > self::ZER){
            return $result[self::ZER];
        }
        return NULL;
    }


    /**
     * Realiza a pesquisa da grid
     * @param \Core_Dto_Abstract $dto
     */
    public function listGrid(\Core_Dto_Abstract $dto)
    {
        $sql = $this->_em->createQueryBuilder()
            ->select('
                p.sqPessoa,
                e.sqEndereco,
                0,
                e.sqCep,
                te.noTipoEndereco,
                e.txEndereco,
                e.nuEndereco,
                e.noBairro,
                m.noMunicipio,
                es.noEstado,
                substring(ac.deCaminhoArquivo, 0, 15) deCaminhoArquivo,
                ac.sqAnexoComprovante,
                esg.sqEnderecoSgdoce,
                \'corporativo\' as tabela,
                e.txComplemento
            ')
            ->from('app:VwEndereco', 'e')
            ->innerJoin('e.sqPessoa', 'p')
            ->innerJoin('e.sqTipoEndereco', 'te')
            ->innerJoin('e.sqMunicipio', 'm')
            ->innerJoin('m.sqEstado', 'es')
            //->leftJoin('p.sqPessoaSgdoce','ps')
            ->leftJoin('p.sqPessoaCorporativo', 'ps', 'WITH','ps.sqPessoaCorporativo = p.sqPessoa')
            ->leftJoin('ps.sqPessoaEndereco','esg','WITH','esg.sqTipoEndereco = e.sqTipoEndereco')
            ->leftJoin('esg.sqAnexoComprovante','ac')
            ->where('p.sqPessoa = ' . $dto->getSqPessoa())
            ->orderBy('e.sqTipoEndereco, esg.sqEnderecoSgdoce');


        $query    = $this->_em->getConnection()->fetchAll($sql->getQuery()->getSQL());
        $result = array();
        foreach ($query as $endereco) {
            $result[$endereco['sq_endereco1']] = $endereco;
        }
        return $result;
    }

    protected function queryEnderecoSgdoce($dto)
    {
        $query = $this->_em->createQueryBuilder()
            ->select('
                ac.deCaminhoArquivo
            ')
            ->from('app:EnderecoSgdoce', 'ess')
            ->innerJoin('ess.sqAnexoComprovante', 'ac')
            ->innerJoin('ess.sqTipoEndereco', 'esste')
            ->where('ess.txEndereco = e.txEndereco')
            ->andWhere('ess.nuEndereco = e.nuEndereco')
            ->andWhere('ess.txComplemento = e.txComplemento')
            ->andWhere('ess.noBairro = e.noBairro')
            ->andWhere('ess.coCep = e.sqCep')
            ->andWhere('te.sqTipoEndereco = esste.sqTipoEndereco')
            ->getDQL();

        return $query;
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
            'c.noLogradouro',
            'c.txComplemento'
        );

        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select($fields)
                ->from('app:VwEnderecoCep', 'c')
                ->innerJoin('c.sqMunicipio', 'm')
                ->innerJoin('m.sqEstado', 'e')
                ->innerJoin('e.sqPais', 'p')
                ->where('c.coCep = :coCep')
                ->setParameter('coCep', $cep)
                ->setMaxResults(1);

        $result = $queryBuilder->getQuery()->getResult();

        return $result ? $result[0] : array();
    }


    public function listaEnderecoTramite(\Core_Dto_Abstract $dto)
    {
        $sql = $this->_em->createQueryBuilder()
            ->select(
                    'p.sqPessoa',
                    'e.sqEndereco',
                    'e.sqCep',
                    'te.noTipoEndereco',
                    'TRIM(e.txEndereco) AS txEndereco',
                    'e.nuEndereco',
                    'TRIM(e.noBairro) AS noBairro',
                    'm.noMunicipio',
                    'es.noEstado',
                    'TRIM(e.txComplemento) AS txComplemento'
                )
            ->from('app:VwEndereco', 'e')
            ->innerJoin('e.sqPessoa', 'p')
            ->innerJoin('e.sqTipoEndereco', 'te')
            ->innerJoin('e.sqMunicipio', 'm')
            ->innerJoin('m.sqEstado', 'es')
            ->where('p.sqPessoa = ' . $dto->getSqPessoa())
            ->orderBy('e.sqTipoEndereco');

        return $sql->getQuery()->getResult();
    }



}