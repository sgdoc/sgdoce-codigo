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

use Sica\Model\Entity;

/**
 * SISICMBio
 *
 * Classe para Repository de Sistemas
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Sistema
 * @version	 1.0.0
 */
class Sistema extends \Sica_Model_Repository
{

    public function getAll()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
                ->select('s.sqSistema', 's.noSistema', 's.sgSistema')
                ->from('app:Sistema', 's')
                ->orderBy('s.sgSistema, s.noSistema');

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function systemsActives($sqTipoPerfil = 1, array $arrTipoPerfil = array(1, 2))
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        $query = $queryBuilder->select('s.sqSistema', 's.noSistema', 's.sgSistema')
                ->from('app:Sistema', 's')
                ->andWhere($queryBuilder->expr()->eq('s.stRegistroAtivo', ':active'))
                ->setParameter('active', 'TRUE')
                ->orderBy('s.sgSistema, s.noSistema');

        if (!in_array($sqTipoPerfil, $arrTipoPerfil) && !\Core_Integration_Sica_User::getUserProfileExternal()) {
            $queryBuilder->andWhere(
                    $queryBuilder->expr()->in('s.sqSistema',
                            $this->_em->getRepository('app:Sistema')
                                      ->getSistemasPorTipoPerfil($sqTipoPerfil, $arrTipoPerfil))
            );
        }

        return $query->getQuery()->getArrayResult();
    }

    public function getSistemasPorTipoPerfil($sqTipoPerfil = 1, array $arrTipoPerfil = array(1, 2), $exc = TRUE)
    {
        $qBuilder = $this->_em->createQueryBuilder();
        $query = $qBuilder->select('s.sqSistema')
                ->from('app:UsuarioPerfil', 'up')
                ->innerJoin('up.sqUsuario', 'u')
                ->innerJoin('up.sqPerfil', 'p')
                ->innerJoin('p.sqTipoPerfil', 'tp')
                ->innerJoin('p.sqSistema', 's')
                ->where($qBuilder->expr()->in('tp.sqTipoPerfil', ':sqTipoPerfil'))
                ->andWhere($qBuilder->expr()->eq('u.sqUsuario', ':sqUsuario'))
                ->setParameters(
                array(
                    'sqTipoPerfil' => $arrTipoPerfil,
                    'sqUsuario' => \Core_Integration_Sica_User::getUserId()
                ));

        $result = $query->getQuery()->getResult();
        $arrSistemas = array();

        foreach ($result as $value) {
            $arrSistemas[] = $value['sqSistema'];
        }

        $arrSistemas = array_unique($arrSistemas);
        $sqSistema = \Core_Integration_Sica_User::getUserSystem();

        if ($sqTipoPerfil == 3) {
            $key = array_search($sqSistema, $arrSistemas);

            if ($key !== FALSE) {
                unset($arrSistemas[$key]);
            }
        }

        if (!$arrSistemas || (in_array($sqSistema, $arrSistemas) && count($arrSistemas) == 1)) {
            if ($exc) {
                \Core_Messaging_Manager::getGateway('Service')->addErrorMessage('MN148');
                throw new \Core_Exception_ServiceLayer_Verification();
            }
        }

        return $arrSistemas;
    }

    public function validateRnSixTwo(Entity\Sistema $entity)
    {
        $query = $this->createQueryBuilder('s');
        $query->select('s.sqSistema')
                ->where(
                        $query->expr()->eq(
                                $query->expr()->upper('s.noSistema'), $query->expr()->upper(':noSistema')
                        )
                )
                ->setParameter('noSistema', $entity->getNoSistema());

        $codigo = $this->_class->getIdentifierValues($entity);

        if ($codigo && $entity->getSqSistema()) {
            $query->andWhere($query->expr()->neq('s.sqSistema', ':sistema'))
                    ->setParameter('sistema', $entity->getSqSistema());
        }

        return $query->getQuery()->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR);
    }

    public function validateRnSixOne(Entity\Sistema $entity)
    {
        $query = $this->createQueryBuilder('s');
        $query->select('s.sqSistema')
                ->where(
                        $query->expr()->eq(
                                $query->expr()->upper('s.sgSistema'), $query->expr()->upper(':sgSistema')
                        )
                )
                ->setParameter('sgSistema', $entity->getSgSistema());

        $codigo = $this->_class->getIdentifierValues($entity);

        if ($codigo && $entity->getSqSistema()) {
            $query->andWhere($query->expr()->neq('s.sqSistema', ':sistema'))
                    ->setParameter('sistema', $entity->getSqSistema());
        }

        return $query->getQuery()->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_SCALAR);
    }

    public function listGrid(\Core_Dto_Search $dto)
    {
        $query = $this->_em->createQueryBuilder();

        $query->select('s.sqSistema, s.sgSistema, s.noSistema, s.stRegistroAtivo, a.noArquitetura, r.noPessoa')
                ->from($this->_entityName, 's')
                ->leftJoin('s.sqArquitetura', 'a')
                ->innerJoin('s.sqPessoaResponsavel', 'r')
                ->leftJoin('r.sqPessoaFisica', 'pf');

        if ($dto->hasNoSistema()) {
            $expre = $query->expr()->lower($query->expr()->trim('s.noSistema'));
            $value = "%" . mb_strtolower(trim($dto->getNoSistema()), 'UTF-8') . "%";
            $query->andWhere('(' . $query->expr()->like($expre, ':sistema') . ' OR ' .
                            $query->expr()->like($expre, ':orSistema') . ' OR ' .
                            $query->expr()->like('clear_accentuation(' . $expre . ')'
                                    , $query->expr()->literal($this->translate($value))) . ')')
                    ->setParameter('sistema', $value)
                    ->setParameter('orSistema', $this->translate($value));
        }

        if ($dto->hasSistema()) {
            $query->andWhere($query->expr()->eq('s.sqSistema', ':sqSistema'))
                    ->setParameter('sqSistema', $dto->getSistema(), 'integer');
        }

        if ($dto->getNoPessoa()) {
            $expre = $query->expr()->lower($query->expr()->trim('r.noPessoa'));
            $value = "%" . mb_strtolower(trim($dto->getNoPessoa()), 'UTF-8') . "%";
            $query->andWhere('(' . $query->expr()->like($expre, ':responsavel') . ' OR ' .
                            $query->expr()->like($expre, ':orResposavel') . ' OR ' .
                            $query->expr()->like('clear_accentuation(' . $expre . ')'
                                    , $query->expr()->literal($this->translate($value))) . ')')
                    ->setParameter('responsavel', $value)
                    ->setParameter('orResposavel', $this->translate($value));
        }

        if ($dto->hasSqArquitetura()) {
            $query->andWhere($query->expr()->eq('a.sqArquitetura', ':arquitetura'))
                    ->setParameter('arquitetura', $dto->getSqArquitetura(), 'integer');
        }

        if ($dto->hasNuCpf()) {
            $query->andWhere($query->expr()->eq('pf.nuCpf', ':cpf'))
                    ->setParameter('cpf', \Zend_Filter::filterStatic($dto->getNuCpf(), 'Digits'), 'integer');
        }

        if ($dto->hasStRegistroAtivo()) {
            $query->andWhere($query->expr()->eq('s.stRegistroAtivo', ':registro'))
                    ->setParameter(
                            'registro', $dto->getStRegistroAtivo()
            );
        }

        return $query;
    }

    public function findByNoSistema($noSistema)
    {
        $querybuilder = $this->createQueryBuilder('s');

        if ($noSistema) {
            $querybuilder->where($querybuilder->expr()->like('LOWER(s.noSistema)', ':noSistema'))
                    ->setParameter('noSistema', '%' . mb_strtolower($noSistema, 'utf-8') . '%');
        }

        $results = $querybuilder->getQuery()->getArrayResult();
        $data = array();

        foreach ($results as $result) {
            $data[$result['sqSistema']] = $result['sgSistema'] . ' - ' . $result['noSistema'];
        }

        return $data;
    }

    public function findById($identifier)
    {
        $dto = \Core_Dto::factoryFromData(array('sistema' => $identifier), 'search');
        $query = $this->listGrid($dto);
        $query->add(
                'select', 'pf.nuCpf, a.sqArquitetura', TRUE
        );
        $data = $query->getQuery()->getArrayResult();
        return $data ? $data[0] : array();
    }

    public function findSystemFull($identifier)
    {
        $dto = \Core_Dto::factoryFromData(array('sistema' => $identifier), 'search');
        $query = $this->listGrid($dto);

        $this->_queryFull($query);
        $data = $query->getQuery()->getArrayResult();
        return $data ? $data[0] : array();
    }

    public function findSystemsFull(\Core_Dto_Search $dto)
    {
        $query = $this->listGrid($dto);

        $this->_queryFull($query);
        return $query->getQuery()->getArrayResult();
    }

    protected function _queryFull($query)
    {
        $tipoTelefoneInstituicional = \Core_Configuration::getCorpTipoTelefoneInstitucional();
        $tipoEmailInstituicional = \Core_Configuration::getCorpTipoEmailInstitucional();

        $query->add(
                'select', 'pf.nuCpf, t.nuDdd, t.nuTelefone, e.txEmail'
                . ', s.txUrl, s.txDescricao, s.txUrlHelp, s.txEnderecoImagem, l.noLeiaute', TRUE
        );

        $query->innerJoin('s.sqLeiaute', 'l')
                ->leftJoin('r.telefone', 't', 'WITH', $query->expr()->eq('t.sqTipoTelefone', ':tipoTelefone'))
                ->leftJoin('r.email', 'e', 'WITH', $query->expr()->eq('e.sqTipoEmail', ':tipoEmail'))
                ->setParameter('tipoTelefone', $tipoTelefoneInstituicional, 'integer')
                ->setParameter('tipoEmail', $tipoEmailInstituicional, 'integer');

        return $query;
    }

    public function getSistemasPerfilExterno()
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();
        $queryBuilder->select('pep.sqPerfil', 's.noSistema', 's.sgSistema')
                ->from('app:Sistema', 's')
                ->innerJoin('s.sqPerfilExternoPadrao', 'pep')
                ->andWhere($queryBuilder->expr()->eq('pep.stRegistroAtivo', ':active'))
                ->setParameter('active', 'TRUE')
                ->andWhere($queryBuilder->expr()->eq('pep.inPerfilExterno', ':inPerfilExterno'))
                ->andWhere($queryBuilder->expr()->eq('s.stRegistroAtivo', ':inPerfilExterno'))
                ->setParameter('inPerfilExterno', 'TRUE')
                ->orderBy('s.sgSistema, s.noSistema');

        $result = $queryBuilder->getQuery()->getArrayResult();
        $sistema = array();

        foreach ($result as $val) {
            $sistema[$val['sqPerfil']] = $val['sgSistema'] . ' - ' . $val['noSistema'];
        }

        return $sistema;
    }

}
