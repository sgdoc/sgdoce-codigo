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
 * Classe para Repository de TipoAssuntoSolicitacao
 *
 * @package      Model
 * @subpackage   Repository
 * @name         TipoAssuntoSolicitacao
 * @version      1.0.0
 * @since        2015-03-09
 */
class TipoAssuntoSolicitacao extends \Core_Model_Repository_Base
{
    /**
     * LISTA DE TIPO DE ASSUNTO DE SOLICITAÇÃO.
     *
     * @param \Core_Dto_Search $dto
     * @param string $sort
     * @param string $order
     * @return array
     */
    public function listTipoAssuntoSolicitacao ($dto, $sort = 'a.noTipoAssuntoSolicitacao', $order = 'ASC')
    {
        $queryBuilder = $this->_em
                             ->createQueryBuilder()
                             ->select('a.sqTipoAssuntoSolicitacao, a.noTipoAssuntoSolicitacao, a.inTipoParaArtefato')
                             ->from('app:TipoAssuntoSolicitacao', 'a')
                             ->orderBy($sort, $order);

        if( $dto->getQuery() != "" ) {
            $search = mb_strtolower($dto->getQuery(),'UTF-8');

            $noTipoAssuntoSolicitacao = $queryBuilder->expr()
                    ->lower($queryBuilder->expr()->trim('a.noTipoAssuntoSolicitacao'));

            $queryBuilder->where(
                    $queryBuilder->expr()
                    ->like(
                        'clear_accentuation(' . $noTipoAssuntoSolicitacao .')',
                        $queryBuilder->expr()
                            ->literal($this->removeAccent('%' . $search . '%'))
                    )
            );
        }

        $inTipoParaArtefato = $dto->getInTipoParaArtefato();

        if ( !is_null($inTipoParaArtefato) ) 
        {
            $paraArtefato = ($dto->getInTipoParaArtefato() > 0) ? '1' : '0';
            
            $queryBuilder->andWhere("a.inTipoParaArtefato = :inTipoParaArtefato")
                         ->setParameter(":inTipoParaArtefato", $paraArtefato);
            
            if ( $paraArtefato ) 
            {
                $arrAssuntoProcesso = array(
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoComentario(),
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoDespacho(),
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoAlterarCadastro(),
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoVolumeDeProcesso(),
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoDesanexaProcesso(),
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoDesmembraPecas(),
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoRemoverPeca()
                );
                
                $arrAssuntoDocumento = array(
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoComentario(),
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoAlterarCadastro(),
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoDespacho(),
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoDesanexaDigital(),
                        \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoExcluirImagem(),
                        //\Core_Configuration::getSgdoceTipoAssuntoSolicitacaoAlterarNumero()
                );
                
                $arrAssunto = ($inTipoParaArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) ? $arrAssuntoProcesso : $arrAssuntoDocumento;
                
                $queryBuilder->andWhere("a.sqTipoAssuntoSolicitacao IN(:sqTipoAssuntoSolicitacaoiIn)")
                             ->setParameter(":sqTipoAssuntoSolicitacaoiIn", $arrAssunto);
            }
        }
        
        $queryBuilder->andWhere("a.stRegistroAtivo = :stRegistroAtivo")
                     ->setParameter(":stRegistroAtivo", TRUE);
        
        return $queryBuilder->getQuery()
                            ->useResultCache(TRUE, NULL, __METHOD__)
                            ->execute();
    }
}
