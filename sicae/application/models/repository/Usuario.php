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

/**
 * SISICMBio
 *
 * Classe para Repository de Usuario
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Usuario
 * @version	 1.0.0
 */
class Usuario extends \Sica_Model_Repository
{

    public function authenticate($login, $pass=NULL)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('u.sqUsuario, p.sqPessoa, p.stRegistroAtivo, pf.dtNascimento, ec.sqEstadoCivil, '
                            . 'pf.nuCurriculoLates, pf.noPai, pf.noMae, tp.sqTipoPessoa, pf.noProfissao, pf.nuCpf, '
                            . 'pf.sgSexo, u.stAtivo, u.txSenha, p.noPessoa, per.inPerfilExterno')
                     ->from('app:Usuario', 'u')
                     ->innerJoin('u.sqPessoa', 'p')
                     ->innerJoin('p.sqPessoaFisica', 'pf')
                     ->innerJoin('pf.sqTipoPessoa', 'tp')
                     ->leftJoin('pf.sqEstadoCivil', 'ec')
                     ->leftJoin('u.sqUsuarioPerfil', 'up')
                     ->leftJoin('up.sqPerfil', 'per')
                     ->where($queryBuilder->expr()->eq('pf.nuCpf', ':nuCpf'))
                     ->setParameter('nuCpf', $login);

        if (!is_null($pass)) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('u.txSenha', ':txSenha'))
                         ->setParameter('txSenha', md5($pass));
        }

        $queryBuilder->andWhere('per.inPerfilExterno = :inPerfilExterno OR per.inPerfilExterno IS NULL')
                     ->setParameter(':inPerfilExterno', 'FALSE', 'boolean')
                     ->groupBy('u.sqUsuario, p.sqPessoa, p.stRegistroAtivo, p.noPessoa, per.inPerfilExterno, '
                             . 'pf.dtNascimento, ec.sqEstadoCivil, pf.nuCurriculoLates, pf.noPai, pf.noMae, '
                             . 'tp.sqTipoPessoa, pf.noProfissao, pf.nuCpf', 'pf.sgSexo');

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    /**
     * @deprecated
     * DEAD CODE
     */
    public function findUser(\Core_Dto_Entity $dtoUser)
    {
        trigger_error('Method deprecated', E_USER_DEPRECATED);
        $queryBuilder = $this->_em
                ->createQueryBuilder()
                ->select('p.sqPessoa', 'u.sqUsuario', 'u.txSenha')
                ->from('app:Usuario', 'u')
                ->innerJoin('u.sqPessoa', 'p')
                ->where('u.sqUsuario = :sqUsuario')
                ->setParameter('sqUsuario', $dtoUser->getSqUsuario())
                ->andWhere('u.sqPessoa = :sqPessoa')
                ->setParameter('sqPessoa', $dtoUser->getSqPessoa()->getSqPessoa())
                ->andWhere('u.stAtivo = :stAtivo')
                ->setParameter('stAtivo', TRUE, 'boolean');

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function findUserByCpfMail(\Core_Dto_Entity $dtoPerson, \Core_Dto_Entity $dtoMail)
    {
        $queryBuilder = $this->_em->createQueryBuilder()
                ->select('pf.nuCpf', 'e.txEmail', 'u.stAtivo', 'p.noPessoa', 'p.sqPessoa', 'u.sqUsuario', 'u.txSenha')
                ->from('app:Usuario', 'u')
                ->innerJoin('u.sqPessoa', 'p')
                ->innerJoin('p.sqPessoaFisica', 'pf')
                ->innerJoin('pf.sqEmail', 'e')
                ->innerJoin('e.sqTipoEmail', 'te')
                ->where('pf.nuCpf = :nuCpf')
                ->setParameter('nuCpf', $dtoPerson->getNuCpf())
                ->andWhere('e.txEmail = :txEmail')
                ->setParameter('txEmail', $dtoMail->getTxEmail())
                ->andWhere('te.sqTipoEmail = :sqTipoEmail')
                ->setParameter('sqTipoEmail', \Core_Configuration::getCorpTipoEmailInstitucional());

        return $queryBuilder->getQuery()->getOneOrNullResult();
    }

    public function listGridUsersInternals(\Core_Dto_Search $search = NULL)
    {
        $tipoTelefoneInstituicional = \Core_Configuration::getCorpTipoTelefoneInstitucional();
        $tipoEmailInstituicional = \Core_Configuration::getCorpTipoEmailInstitucional();

        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder
                ->select(
                        'u.sqUsuario', 'p.noPessoa', 'pf.nuCpf', 't.nuDdd', 't.nuTelefone', 'e.txEmail', 'u.stAtivo'
                )
                ->from($this->_entityName, 'u')
                ->leftJoin('u.sqUsuarioPerfil', 'up')
                ->leftJoin('up.sqPerfil', 'pl', 'WITH'
                        , $queryBuilder->expr()->eq('pl.inPerfilExterno', ':perfilExterno')
                )
                ->leftJoin('pl.sqSistema', 's')
                ->leftJoin('up.sqUnidadeOrgPessoa', 'un')
                ->innerJoin('u.sqPessoa', 'p')
                ->leftJoin('p.telefone', 't', 'WITH', $queryBuilder->expr()->eq('t.sqTipoTelefone', ':tipoTelefone')
                )
                ->leftJoin('p.email', 'e', 'WITH', $queryBuilder->expr()->eq('e.sqTipoEmail', ':tipoEmail')
                )
                ->innerJoin('p.sqPessoaFisica', 'pf')
                ->groupBy('u.sqUsuario', 'pf.nuCpf', 'p.noPessoa', 't.nuTelefone', 't.nuDdd', 'e.txEmail'
                )
                ->setParameter('perfilExterno', 'FALSE')
                ->setParameter('tipoTelefone', $tipoTelefoneInstituicional, 'integer')
                ->setParameter('tipoEmail', $tipoEmailInstituicional, 'integer');

        if ($search->getNuCpf()) {
            $cpf = \Zend_Filter::filterStatic($search->getNuCpf(), 'Digits');
            $queryBuilder->andWhere(
                    $queryBuilder->expr()->eq('pf.nuCpf', ':cpf')
            )->setParameter('cpf', $cpf);
        }
        if ($search->getNoPessoa()) {
            $expre = $queryBuilder->expr()->lower($queryBuilder->expr()->trim('p.noPessoa'));
            $value = "%" . mb_strtolower(trim($search->getNoPessoa()), 'UTF-8') . "%";

            $queryBuilder->andWhere('(' . $queryBuilder->expr()->like($expre, ':pessoa') . ' OR ' .
                            $queryBuilder->expr()->like($expre, ':orPessoa') . ' OR ' .
                            $queryBuilder->expr()->like('clear_accentuation(' . $expre . ')'
                                    , $queryBuilder->expr()->literal($this->translate($value))) . ')')
                    ->setParameter('pessoa', $value)
                    ->setParameter('orPessoa', $this->translate($value));
        }

        if ($search->hasSqUnidadeOrg() && $search->getSqUnidadeOrg()) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('un.sqUnidadeOrgPessoa', ':unidade'))
                    ->setParameter('unidade', $search->getSqUnidadeOrg());
        }

        if ($search->getSqSistema()) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('s.sqSistema', ':sistema'))
                    ->setParameter('sistema', $search->getSqSistema());
        }

        if ($search->hasSqPerfil()) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('pl.sqPerfil', ':perfil'))
                    ->setParameter('perfil', $search->getSqPerfil());
        }

        if ($search->getStAtivo() !== "") {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('u.stAtivo', ':ativo'))
                    ->setParameter('ativo', $search->getStAtivo());
        }

        return $queryBuilder;
    }

    public function findProfilesBind($usuario, $externo = FALSE)
    {
        $entity = $this->_entityName;
        $nameIdentifier = 'u.sqUsuario';
        $columns = array(
            'u.sqUsuario',
            's.noSistema',
            's.sgSistema',
            's.sqSistema',
            'un.noPessoa',
            'un.sqPessoa',
            'pl.noPerfil',
            'pl.sqPerfil'
        );
        $getUnidade = TRUE;
        $perfilExterno = 'FALSE';

        if ($externo) {
            $entity = 'app:UsuarioExterno';
            $perfilExterno = 'TRUE';
            $nameIdentifier = 'u.sqUsuarioExterno';
            $getUnidade = FALSE;
            $columns = array(
                'u.sqUsuarioExterno',
                'u.noUsuarioExterno',
                's.noSistema',
                's.sgSistema',
                's.sqSistema',
                'pl.noPerfil',
                'pl.sqPerfil'
            );
        }

        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder
                ->select($columns)
                ->from($entity, 'u')
                ->innerJoin('u.sqUsuarioPerfil', 'up')
                ->innerJoin('up.sqPerfil', 'pl', 'WITH'
                        , $queryBuilder->expr()->eq('pl.inPerfilExterno', ':perfilExterno')
                )
                ->innerJoin('pl.sqSistema', 's');

        if ($getUnidade) {
            $queryBuilder->innerJoin('up.sqUnidadeOrgPessoa', 'un');
        }

        $queryBuilder->where($queryBuilder->expr()->eq($nameIdentifier, ':usuario'))
                ->setParameter('usuario', $usuario)
                ->setParameter('perfilExterno', $perfilExterno)
                ->orderBy('s.sgSistema, s.noSistema, pl.noPerfil');

        if ($getUnidade) {
            $queryBuilder->addOrderBy('un.noPessoa');
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function findDataViewUserInternal($identifier)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $tipoTelefoneInstituicional = \Core_Configuration::getCorpTipoTelefoneInstitucional();
        $tipoEmailInstituicional = \Core_Configuration::getCorpTipoEmailInstitucional();

        $queryBuilder->select(
                        'p.noPessoa', 'pf.nuCpf', 't.nuDdd, t.nuTelefone', 'e.txEmail', 'u.stAtivo'
                )
                ->from($this->_entityName, 'u')
                ->innerJoin('u.sqPessoa', 'p')
                ->innerJoin('p.sqPessoaFisica', 'pf')
                ->leftJoin(
                        'p.telefone', 't', 'WITH', $queryBuilder->expr()->eq('t.sqTipoTelefone', ':tipoTelefone')
                )
                ->leftJoin(
                        'p.email', 'e', 'WITH', $queryBuilder->expr()->eq('e.sqTipoEmail', ':tipoEmail')
                )
                ->where($queryBuilder->expr()->eq('u.sqUsuario', ':usuario'))
                ->setParameter('usuario', $identifier)
                ->setParameter('tipoTelefone', $tipoTelefoneInstituicional, 'integer')
                ->setParameter('tipoEmail', $tipoEmailInstituicional, 'integer');

        $data = $queryBuilder
                        ->getQuery()->getArrayResult(
                array(), \Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY
        );

        if ($data) {
            return $data[0];
        }

        return array(
            'noPessoa' => NULL,
            'nuCpf' => NULL,
            'nuDdd' => NULL,
            'nuTelefone' => NULL,
            'txEmail' => NULL,
            'stAtivo' => NULL
        );
    }

    public function saveBindProfile(\Core_Dto_Mapping $mapping, array $perfis)
    {
        $queryPerfis = $this->_em->createQueryBuilder();
        $queryPerfis->select('p.sqPerfil')
                ->from('app:Perfil', 'p')
                ->where($queryPerfis->expr()->eq('p.sqSistema', ':sistema'))
                ->setParameter('sistema', $mapping->getSqSistema());

        $perfisFound = $queryPerfis->getQuery()->getArrayResult();
        $inPerfil = array();
        foreach ($perfisFound as $perfil) {
            $inPerfil[] = $perfil['sqPerfil'];
        }

        $criteria = array('sqUsuario' => $mapping->getUsuario(), 'sqUnidadeOrgPessoa' => $mapping->getUnidade());
        if ($inPerfil) {
            $criteria['sqPerfil'] = $inPerfil;
        }
        $usuarioPerfil = $this->getEntityManager()->getRepository('app:UsuarioPerfil')->findBy($criteria);

        foreach ($usuarioPerfil as $usuarioPerf){
            $this->getEntityManager()->remove($usuarioPerf);
            $this->getEntityManager()->flush();
        }


        $unidade = $this->_em->getUnitOfWork()->createEntity(
                'app:UnidadeOrg', array('sqPessoa' => $mapping->getUnidade())
        );

        $usuario = $this->_em->find('app:Usuario', $mapping->getUsuario());

        foreach ($perfis as $perfil) {
            $entityPerfil = $perfil->getEntity();
            $this->_em->getUnitOfWork()->registerManaged(
                    $entityPerfil, array('sqPerfil' => $entityPerfil->getSqPerfil()), array()
            );
            $usuarioPerfil = new \Sica\Model\Entity\UsuarioPerfil();
            $usuarioPerfil->setSqPerfil($entityPerfil);
            $usuarioPerfil->setSqUnidadeOrgPessoa($unidade);
            $usuarioPerfil->setSqUsuario($usuario);
            $this->_em->persist($usuarioPerfil);
        }

        $this->_em->flush();
    }

    public function findUsers(\Core_Dto_Search $dto)
    {
        $tipoTelefoneInstituicional = \Core_Configuration::getCorpTipoTelefoneInstitucional();
        $tipoEmailInstituicional = \Core_Configuration::getCorpTipoEmailInstitucional();

        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('pf.nuCpf, p.noPessoa, e.txEmail, t.nuTelefone, t.nuDdd, u.stAtivo, s.sqSistema, '
                        . 's.noSistema, s.sgSistema, pl.noPerfil, un.noPessoa noUnidade')
                ->from($this->_entityName, 'u')
                ->innerJoin('u.sqPessoa', 'p')
                ->innerJoin('p.sqPessoaFisica', 'pf')
                ->leftJoin(
                        'p.telefone', 't', 'WITH', $queryBuilder->expr()->eq('t.sqTipoTelefone', ':tipoTelefone')
                )
                ->leftJoin(
                        'p.email', 'e', 'WITH', $queryBuilder->expr()->eq('e.sqTipoEmail', ':tipoEmail')
                )
                ->leftJoin('u.sqUsuarioPerfil', 'up')
                ->leftJoin('up.sqPerfil', 'pl')
                ->leftJoin('pl.sqSistema', 's')
                ->leftJoin('up.sqUnidadeOrgPessoa', 'un')
                ->setParameter('tipoTelefone', $tipoTelefoneInstituicional, 'integer')
                ->setParameter('tipoEmail', $tipoEmailInstituicional, 'integer')
                ->orderBy('p.noPessoa, s.sgSistema, s.noSistema')
                ->addOrderBy('pl.noPerfil, un.noPessoa');

        if ($dto->hasNuCpf()) {
            $cpf = \Zend_Filter::filterStatic($dto->getNuCpf(), 'Digits');
            $queryBuilder->andWhere(
                    $queryBuilder->expr()->eq('pf.nuCpf', ':cpf')
            )->setParameter('cpf', $cpf);
        }
        if ($dto->hasNoPessoa()) {
            $queryBuilder->andWhere(
                            $queryBuilder->expr()->like(
                                    $queryBuilder->expr()->lower('p.noPessoa'), ':pessoa'
                            )
                    )
                    ->setParameter('pessoa', '%' . mb_strtolower($dto->getNoPessoa(), 'utf-8') . '%');
        }

        if ($dto->hasSqUnidadeOrg() && $dto->getSqUnidadeOrg()) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('un.sqUnidadeOrgPessoa', ':unidade'))
                    ->setParameter('unidade', $dto->getSqUnidadeOrg());
        }

        if ($dto->hasSqSistema()) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('s.sqSistema', ':sistema'))
                    ->setParameter('sistema', $dto->getSqSistema());
        }

        if ($dto->hasSqPerfil()) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('pl.sqPerfil', ':perfil'))
                    ->setParameter('perfil', $dto->getSqPerfil());
        }

        if ($dto->hasStAtivo()) {
            $queryBuilder->andWhere($queryBuilder->expr()->eq('u.stAtivo', ':ativo'))
                    ->setParameter('ativo', $dto->getStAtivo());
        }

        return $queryBuilder->getQuery()->getArrayResult();
    }

    public function getDataMail(\Core_Dto_Mapping $mapping, $perfis)
    {
        $tipoEmailInstituicional = \Core_Configuration::getCorpTipoEmailInstitucional();
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('p.noPessoa,  e.txEmail')
                ->from($this->_entityName, 'u')
                ->innerJoin('u.sqPessoa', 'p')
                ->innerJoin(
                        'p.email', 'e', 'WITH', $queryBuilder->expr()->eq('e.sqTipoEmail', ':tipoEmail')
                )
                ->where($queryBuilder->expr()->eq('u.sqUsuario', ':usuario'))
                ->setParameter('usuario', $mapping->getUsuario())
                ->setParameter('tipoEmail', $tipoEmailInstituicional, 'integer');

        $data['usuario'] = $queryBuilder->getQuery()->getSingleResult();

        $queryBuilder = $this->_em->createQueryBuilder();

        $arrPerfil = array();
        foreach ($perfis as $value) {
            $arrPerfil[] = $value->getSqPerfil();
        }

        $queryBuilder->select('p.noPerfil, s.sgSistema, s.noSistema')
                ->from('app:UsuarioPerfil', 'up')
                ->innerJoin('up.sqPerfil', 'p')
                ->innerJoin('p.sqSistema', 's')
                ->innerJoin('up.sqUnidadeOrgPessoa', 'u')
                ->where($queryBuilder->expr()->eq('up.sqUsuario', ':usuario'))
                ->setParameter('usuario', $mapping->getUsuario())
                ->andWhere('u.sqUnidadeOrgPessoa = :sqUnidadeOrgPessoa')
                ->setParameter('sqUnidadeOrgPessoa', $mapping->getUnidade())
                ->andWhere($queryBuilder->expr()->in('p.sqPerfil', $arrPerfil));

        $data['perfis'] = $queryBuilder->getQuery()->getArrayResult();

        return $data;
    }

}
