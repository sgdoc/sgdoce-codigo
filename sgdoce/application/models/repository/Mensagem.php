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
 * Classe para Repository de Mensagem
 *
 * @package      Model
 * @subpackage   Repository
 * @name         Mensagem
 * @version      1.0.0
 * @since        2012-11-20
 */
class Mensagem extends \Core_Model_Repository_Base
{

    /**
     * Procura mensagens de acordo com os parâmetros
     * @param  array $params Dados da requisição
     * @return mixed         Query Builder
     */
    public function pesquisaMensagem($params)
    {
        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select(array('m','td','a'))
                             ->from('app:Mensagem', 'm')
                             ->innerJoin('m.sqTipoDocumento','td')
                             ->innerJoin('m.sqAssunto','a');

        if ($params->getSqAssunto()) {
            $queryBuilder->where('m.sqAssunto = :assunto')
                         ->setParameter('assunto', (int) $params->getSqAssunto());
        }

        if ($params->getSqTipoDocumento()) {
            $queryBuilder->andWhere('m.sqTipoDocumento = :tipo')
                         ->setParameter('tipo', (int) $params->getSqTipoDocumento());
        }

        if ($params->getStMensagemAtiva()) {
            $queryBuilder->andWhere('m.stMensagemAtiva = :mensagemAtiva')
                         ->setParameter('mensagemAtiva', $params->getStMensagemAtiva());
        }

        return $queryBuilder;
    }

    /**
     * Obtém uma mensagem geral, se existir
     * @return mixed Mensagem
     */
    public function getMensagemGeral()
    {
        $qry = $this->_em
                   ->createQueryBuilder()
                   ->select('m')
                   ->from('app:Mensagem', 'm');

        $qry->where('m.sqAssunto IS NULL AND m.sqTipoDocumento  IS NULL');

        $res = $qry->getQuery()->execute();
        if( count($res ))
        {
            return $res[0];
        }
    }

    /**
     * Procura se já existe uma mensagem cadastrada com o mesmo tipo de documento e assunto
     * @param  array $params Parâmetros da requisição
     * @return array         Retorna um array com as mensagens encontradas
     */
    public function findMessage ($params)
    {
        $qry = $this->pesquisaMensagem($params);

        $res = $qry->getQuery()->execute();
        $out = array();
        foreach ($res as $item) {
            $out['Mensagem'] = \Core_Registry::getMessage()->translate('MN071');
            $out['idMensagem'] = $item->getSqMensagem();
        }
        return $out;
    }

    /**
     * Verifica se já existe uma mensagem com o mesmo tipo de documento, mesmo assunto e já ativa
     * @param  array $params [description]
     * @return array         [description]
     */
    public function findMessageAtiva ($params)
    {
        $qry = $this->_em
            ->createQueryBuilder()
            ->select('m')
            ->from('app:Mensagem', 'm')
            ->where('m.stMensagemAtiva = true')
            ->andWhere('m.sqAssunto = '.$params['assunto'])
            ->andWhere('m.sqTipoDocumento = '.$params['tipodoc'])
            ->andWhere('m.sqMensagem <> '.$params['id']);

        return $qry->getQuery()->getArrayResult();
    }

    /**
     * Desativa mensagens
     * @param  array $params Dados da requisição
     * @return mixed         Resultset
     */
    public function deactivateMessages($params)
    {
        $qry = $this->_em
                   ->createQueryBuilder();
        $qry->update('app:Mensagem','m')
           ->set('m.stMensagemAtiva',$qry->expr()->literal(FALSE));
        $qry->where('m.sqAssunto = :sqAssunto AND m.sqTipoDocumento = :sqTipoDocumento')
            ->setParameter('sqAssunto',$params['sqAssunto']->getSqAssunto())
            ->setParameter('sqTipoDocumento', $params['sqTipoDocumento']->getSqTipoDocumento());
        $res = $qry->getQuery()->execute();
    }

    /**
     * Verifica se já existe uma mensagem cadastrada
     * com mesmo tipo de documento e assunto
     * @param \Sgdoce\Model\Entity\Assunto $assunto Assunto
     * @param \Sgdoce\Model\Entity\TipoDocumento $tipoDocumento Tipo de Documento
     * @param integer|null $sqMensagem Id da mensagem sendo editada
     */
    public function hasMensagem(\Sgdoce\Model\Entity\Assunto $assunto,
                                \Sgdoce\Model\Entity\TipoDocumento $tipoDocumento,
                                $sqMensagem = NULL)
    {
        $queryBuilder = $this->_em->createQueryBuilder();
        $queryBuilder->select('m')
                     ->from('app:Mensagem', 'm')
                     ->where('m.sqTipoDocumento = :sqTipoDocumento')
                     ->andWhere('m.sqAssunto = :sqAssunto')
                     ->setParameter('sqTipoDocumento', $tipoDocumento->getSqTipoDocumento())
                     ->setParameter('sqAssunto', $assunto->getSqAssunto());

        if($sqMensagem != NULL){
            $queryBuilder->andwhere('m.sqMensagem <> :sqMensagem')
                         ->setParameter('sqMensagem', $sqMensagem);
        }

        $res = $queryBuilder->getQuery()->execute();
        return (count($res));
    }
}