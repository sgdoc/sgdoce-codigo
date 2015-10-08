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
 * Classe para Repository de Usuario Externo
 *
 * @package	 ModelsRepository
 * @category Repository
 * @name	 Usuario
 * @version	 1.0.0
 */
class UsuarioExterno extends \Sica_Model_Repository
{

    const ATIVO = '1';
    const INATIVO = '0';

    public function authenticate($login, $pass)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $result = $this->_em
                ->createQueryBuilder()
                ->select('u.sqUsuarioExterno sqUsuario, u.stRegistroAtivo, u.stRegistroAtivo stAtivo, '
                        . 'per.inPerfilExterno, u.txEmail, u.txSenha, u.noUsuarioExterno noPessoa, u.sqUsuarioExterno '
                        . 'sqPessoa'
                )->from('app:UsuarioExterno', 'u')
                ->leftJoin('u.sqUsuarioPerfil', 'uep')
                ->leftJoin('uep.sqPerfil', 'per')
                ->where($queryBuilder->expr()->eq('u.txEmail', ':txEmail'))
                ->setParameter('txEmail', $login)
                ->andWhere($queryBuilder->expr()->eq('u.txSenha', ':txSenha'))
                ->setParameter('txSenha', md5($pass))
                ->andWhere($queryBuilder->expr()->in('u.stRegistroAtivo', array(self::ATIVO, self::INATIVO)))
                ->getQuery()
                ->getResult();

        if ($result) {
            $result[0]['inPerfilExterno'] = TRUE;

            return $result[0];
        } else {
            return $result;
        }
    }

    public function recoverPass(\Core_Dto_Abstract $dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $this->_em
                ->createQueryBuilder()
                ->select('u.sqUsuarioExterno, u.stRegistroAtivo' .
                        ',u.txEmail, u.txSenha, u.noUsuarioExterno, u.stRegistroAtivo'
                )->from('app:UsuarioExternoPerfil', 'up')
                ->innerJoin('up.sqPerfil', 'per')
                ->innerJoin('up.sqUsuarioExterno', 'u')
                ->where($queryBuilder->expr()->eq('u.txEmail', ':txEmail'))
                ->setParameter('txEmail', $dto->getTxEmail())
                ->andWhere($queryBuilder->expr()->eq('per.inPerfilExterno', ':inPerfilExterno'))
                ->setParameter(':inPerfilExterno', 'TRUE');

        $param = \Zend_Filter::filterStatic($dto->getTxLogin(), 'digits');
        $pessoa = 'sqUsuarioPessoaFisica';

        switch ($dto->getTpValidacao()) {
            case 'passaporte':
                $field = 'nuPassaporte';
                break;
            case 'cpf':
                $field = 'nuCpf';
                break;
            default:
                $pessoa = 'sqUsuarioPessoaJuridica';
                $field = 'nuCnpj';
                break;
        }

        $query->innerJoin("u.$pessoa", 'upfj')
                ->andWhere($queryBuilder->expr()->eq("upfj.$field", ':param'))
                ->setParameter('param', $param);

        $result = $query->getQuery()->getResult();

        return $result ? $result[0] : $result;
    }

    /**
     * @param integer $identifier
     * @return array
     */
    public function findDataViewUserExternal($identifier)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('u.noUsuarioExterno,
                               u.txEmail,
                               pf.nuCpf,
                               pf.inNacionalidadeBrasileira,
                               pj.nuCnpj,
                               pj.noFantasia,
                               pf.nuPassaporte,
                               dc.nuDddTelefoneFixo,
                               dc.nuTelefoneFixo,
                               dc.nuDddTelefoneCelular,
                               dc.nuTelefoneCelular,
                               u.stRegistroAtivo')
                ->distinct()
                ->from('app:UsuarioExternoPerfil', 'up')
                ->innerJoin('up.sqPerfil', 'per', 'WITH', $queryBuilder->expr()
                        ->eq('per.inPerfilExterno', ':perfilExterno'))
                ->innerJoin('per.sqSistema', 's')
                ->innerJoin('up.sqUsuarioExterno', 'u')
                ->innerJoin('u.dadoComplementar', 'dc')
                ->leftJoin('u.sqUsuarioPessoaFisica', 'pf')
                ->leftJoin('u.sqUsuarioPessoaJuridica', 'pj')
                ->where($queryBuilder->expr()->eq('u.sqUsuarioExterno', ':usuario'))
                ->setParameter('usuario', $identifier)
                ->setParameter('perfilExterno', 'TRUE')
                ->groupBy('u.noUsuarioExterno,
                                u.txEmail,
                                pf.nuCpf,
                                pf.inNacionalidadeBrasileira,
                                pj.nuCnpj,
                                pj.noFantasia,
                                pf.nuPassaporte,
                                dc.nuDddTelefoneFixo,
                                dc.nuTelefoneFixo,
                                dc.nuDddTelefoneCelular,
                                dc.nuTelefoneCelular,
                                u.stRegistroAtivo', 'up.sqUsuarioPerfil');

        $data = $queryBuilder->getQuery()->getArrayResult(array(), \Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

        if ($data) {
            return $data[0];
        }

        return array(
            'noUsuarioExterno' => NULL,
            'nuCpf' => NULL,
            'nuCnpj' => NULL,
            'noFantasia' => NULL,
            'nuPassaporte' => NULL,
            'nuDddTelefoneFixo' => NULL,
            'nuTelefoneFixo' => NULL,
            'nuDddTelefoneCelular' => NULL,
            'nuTelefoneCelular' => NULL,
            'txEmail' => NULL,
            'stRegistroAtivo' => NULL
        );
    }

    public function checkCredencials($dto)
    {
        $queryBuilder = $this->_em->createQueryBuilder();

        $query = $this->_em
                ->createQueryBuilder()
                ->select('u.sqUsuarioExterno')
                ->from('app:UsuarioExterno', 'u');

        if ($dto->getNuCpf()) {
            $query->innerJoin('u.sqUsuarioPessoaFisica', 'pf')
                    ->andWhere($queryBuilder->expr()->eq('pf.nuCpf', ':nuCpf'))
                    ->setParameter('nuCpf', \Zend_Filter::filterStatic($dto->getNuCpf(), 'Digits'));
        }

        $noUsuarioExterno = \Zend_Filter::filterStatic($dto->getNoUsuarioExterno(), 'StringTrim');
        if ($dto->getNuPassaporte() || $noUsuarioExterno) {
            if ($dto->getInNacionalidadeBrasileira()) {
                return FALSE;
            }

            $query->innerJoin('u.sqUsuarioPessoaFisica', 'pf')
                    ->andWhere($queryBuilder->expr()->eq('pf.nuPassaporte', ':nuPassaporte'))
                    ->setParameter('nuPassaporte', $dto->getNuPassaporte())
                    ->andWhere($queryBuilder->expr()->eq('u.noUsuarioExterno', ':noUsuarioExterno'))
                    ->setParameter('noUsuarioExterno', $noUsuarioExterno);
        }

        if ($dto->getNuCnpj()) {
            $query->innerJoin('u.sqUsuarioPessoaJuridica', 'pj')
                    ->andWhere($queryBuilder->expr()->eq('pj.nuCnpj', ':nuCnpj'))
                    ->setParameter('nuCnpj', \Zend_Filter::filterStatic($dto->getNuCnpj(), 'Digits'));
        }

        if ($dto->getTxEmail()) {
            $query->andWhere($queryBuilder->expr()->eq('u.txEmail', ':txEmail'))
                    ->setParameter('txEmail', $dto->getTxEmail());
        }

        if ($dto->getSqUsuarioExterno()) {
            $query->andWhere('u.sqUsuarioExterno != :sqUsuarioExterno')
                    ->setParameter('sqUsuarioExterno', $dto->getSqUsuarioExterno());
        }

        return $query->getQuery()->getResult() ? TRUE : FALSE;
    }

    public function deletePerfilPadraoUsuario($sqUsuarioExterno)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $subQuery = $queryBuilder = $this->_em->createQueryBuilder();

        $subQuery->select('pep.sqPerfil')
                ->from('app:Sistema', 's')
                ->innerJoin('s.sqPerfilExternoPadrao', 'pep');

        $arrPerfilExterno = array();
        foreach ($subQuery->getQuery()->getResult() as $sqPerfil) {
            $arrPerfilExterno[] = $sqPerfil['sqPerfil'];
        }

        if ($arrPerfilExterno) {

            $criteria = array('sqUsuarioExterno'=> $sqUsuarioExterno,'sqPerfil'=>$arrPerfilExterno);
            $usuarioExtPerfil = $this->getEntityManager()->getRepository('app:UsuarioExternoPerfil')->findBy($criteria);
            foreach($usuarioExtPerfil as $usuarioPerfil){
                $this->getEntityManager()->remove($usuarioPerfil);
                $this->getEntityManager()->flush();
            }
            return true;
        }

        return $arrPerfilExterno;
    }

    public function listGridUsersExternals(\Core_Dto_Search $search = NULL)
    {
        $query = $this->_em
                ->createQueryBuilder()
                ->select('u.sqUsuarioExterno, u.noUsuarioExterno, u.txEmail, s.sgSistema, s.noSistema, pf.nuCpf, '
                        . 'pj.nuCnpj, pf.nuPassaporte, u.stRegistroAtivo, per.noPerfil,'
                        . 'pf.inNacionalidadeBrasileira')
                ->from('Sica\Model\Entity\UsuarioExterno', 'u')
                ->leftJoin('u.sqUsuarioPerfil', 'up')
                ->leftJoin('up.sqPerfil', 'per', 'WITH', $this->_em->createQueryBuilder()
                        ->expr()->eq('per.inPerfilExterno', ':perfilExterno'))
                ->leftJoin('per.sqSistema', 's')
                ->leftJoin('u.sqUsuarioPessoaFisica', 'pf')
                ->leftJoin('u.sqUsuarioPessoaJuridica', 'pj')
                ->setParameter('perfilExterno', 'TRUE')
                ->groupBy('u.sqUsuarioExterno, u.noUsuarioExterno, u.txEmail, s.sgSistema, s.noSistema, pf.nuCpf, '
                . 'pj.nuCnpj, pf.nuPassaporte, u.stRegistroAtivo, up.sqUsuarioPerfil, per.noPerfil, '
                . 'pf.inNacionalidadeBrasileira');

        if (!is_null($search)) {
            $params = $search->getApi();
            $this->addWhereUsuarioExterno($query, $search, $params);
        }

        return $query;
    }

    public function listGridUsersExternalsCount(\Core_Dto_Search $search = NULL)
    {
        $query = $this->_em
                ->createQueryBuilder()
                ->select('DISTINCT u.sqUsuarioExterno')
                ->from('Sica\Model\Entity\UsuarioExterno', 'u')
                ->leftJoin('u.sqUsuarioPerfil', 'up')
                ->leftJoin('up.sqPerfil', 'per', 'WITH', $this->_em->createQueryBuilder()
                        ->expr()->eq('per.inPerfilExterno', ':perfilExterno'))
                ->leftJoin('per.sqSistema', 's')
                ->leftJoin('u.sqUsuarioPessoaFisica', 'pf')
                ->leftJoin('u.sqUsuarioPessoaJuridica', 'pj')
                ->setParameter('perfilExterno', 'TRUE');

        if (!is_null($search)) {
            $params = $search->getApi();
            $this->addWhereUsuarioExterno($query, $search, $params);
        }

        return count($query->getQuery()->getArrayResult());
    }

    /**
     * Trata filtros para compor o método listGridMarco
     * @param \Doctrine\ORM\QueryBuilder $query
     * @param array $params
     */
    public function addWhereUsuarioExterno(\Doctrine\ORM\QueryBuilder &$query, $search, $params)
    {
        foreach ($params as $field => $value) {

            if ('' !== $search->{$value}()) {
                switch ($field) {

                    case 'noUsuarioExterno':
                        $expre = $query->expr()->lower($query->expr()->trim('u.noUsuarioExterno'));
                        $val = "%" . mb_strtolower(trim($search->{$value}()), 'UTF-8') . "%";

                        $query->andWhere('(' . $query->expr()->like($expre, ':noUsuarioExterno') . ' OR ' .
                                        $query->expr()->like($expre, ':orNoUsuarioExterno') . ' OR ' .
                                        $query->expr()->like('clear_accentuation(' . $expre . ')'
                                                , $query->expr()->literal($this->translate($val))) . ')')
                                ->setParameter('noUsuarioExterno', $val)
                                ->setParameter('orNoUsuarioExterno', $this->translate($val));
                        break;

                    case 'noFantasia':
                        $expre1 = $query->expr()->lower($query->expr()->trim('pj.noFantasia'));
                        $expre2 = $query->expr()->lower($query->expr()->trim('u.noUsuarioExterno'));
                        $value = "%" . mb_strtolower(trim($search->{$value}()), 'UTF-8') . "%";

                        $query->andWhere($query->expr()->orX(
                                        '(' . $query->expr()->like($expre1, ':noFantasia') . ' OR ' .
                                        $query->expr()->like($expre1, ':orNoFantasia') . ' OR ' .
                                        $query->expr()->like('clear_accentuation(' . $expre1 . ')'
                                                , $query->expr()->literal($this->translate($value))) . ')'
                                        , '(' . $query->expr()->like($expre2, ':noFantasia') . ' OR ' .
                                        $query->expr()->like($expre2, ':orNoFantasia') . ' OR ' .
                                        $query->expr()->like('clear_accentuation(' . $expre2 . ')'
                                                , $query->expr()->literal($this->translate($value))) . ')'
                        ));
                        $query->setParameter('noFantasia', $value)
                                ->setParameter('orNoFantasia', $this->translate($value));
                        break;

                    case 'cpfCnpjUsuario':
                        $cpfCnpj = \Zend_Filter::filterStatic($search->{$value}(), 'Digits');

                        if (strlen($cpfCnpj) == 11) {
                            $query->andWhere('pf.nuCpf = :sqCpf')
                                    ->setParameter('sqCpf', $cpfCnpj);
                        } else {
                            $query->andWhere('pj.nuCnpj = :sqCnpj')
                                    ->setParameter('sqCnpj', $cpfCnpj);
                        }
                        break;

                    case 'sqSistema':
                        $query->andWhere('s.sqSistema = :sqSistema')
                                ->setParameter('sqSistema', $search->{$value}());
                        break;

                    case 'sqPerfil':
                        $query->andWhere('per.sqPerfil = :sqPerfil')
                                ->setParameter('sqPerfil', $search->{$value}());
                        break;

                    case 'stRegistroAtivo':
                        $query->andWhere('u.stRegistroAtivo = :stRegistroAtivo')
                                ->setParameter('stRegistroAtivo', $search->{$value}());
                        break;
                }
            }
        }
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

        $criteria = array('sqUsuarioExterno'=> $mapping->getUsuario());
        if ($inPerfil) {
            $criteria['sqPerfil'] = $inPerfil;
        }
        $usuarioExtPerfil = $this->getEntityManager()->getRepository('app:UsuarioExternoPerfil')->findBy($criteria);
        foreach($usuarioExtPerfil as $usuarioPerfil){
            $this->getEntityManager()->remove($usuarioPerfil);
            $this->getEntityManager()->flush();
        }

        $usuario = $this->_em->find('app:UsuarioExterno', $mapping->getUsuario());

        foreach ($perfis as $perfil) {
            $entityPerfil = $perfil->getEntity();
            $this->_em->getUnitOfWork()->registerManaged(
                    $entityPerfil, array('sqPerfil' => $entityPerfil->getSqPerfil()), array()
            );

            $usuarioPerfil = new \Sica\Model\Entity\UsuarioExternoPerfil();
            $usuarioPerfil->setSqPerfil($entityPerfil);
            $usuarioPerfil->setSqUsuarioExterno($usuario);
            $this->_em->persist($usuarioPerfil);
        }

        $this->_em->flush();
    }

    public function getDataMail(\Core_Dto_Mapping $mapping, $perfis)
    {
        $tipoEmailInstituicional = \Core_Configuration::getCorpTipoEmailInstitucional();
        $queryBuilder = $this->_em->createQueryBuilder();

        $queryBuilder->select('u.noUsuarioExterno, u.txEmail')
                ->from($this->_entityName, 'u')
                ->where($queryBuilder->expr()->eq('u.sqUsuarioExterno', ':usuario'))
                ->setParameter('usuario', $mapping->getUsuario());

        $data['usuario'] = $queryBuilder->getQuery()->getSingleResult();

        $queryBuilder = $this->_em->createQueryBuilder();

        $arrPerfil = array();
        foreach ($perfis as $value) {
            $arrPerfil[] = $value->getSqPerfil();
        }

        $queryBuilder->select('p.noPerfil, s.sgSistema, s.noSistema')
                ->from('app:UsuarioExternoPerfil', 'up')
                ->innerJoin('up.sqPerfil', 'p')
                ->innerJoin('p.sqSistema', 's')
                ->where($queryBuilder->expr()->eq('up.sqUsuarioExterno', ':usuario'))
                ->setParameter('usuario', $mapping->getUsuario())
                ->andWhere($queryBuilder->expr()->in('p.sqPerfil', $arrPerfil));

        $data['perfis'] = $queryBuilder->getQuery()->getArrayResult();

        return $data;
    }

}
