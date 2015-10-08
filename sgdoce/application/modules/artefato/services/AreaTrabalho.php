<?php
/**
 * Copyright 2012 do ICMBio
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
namespace Artefato\Service;
/**
 * Classe para Service de Area de Trabalho
 *
 * @package  Minuta
 * @category Service
 * @name      AreaTrabalho
 * @version  1.0.0
 */
class AreaTrabalho extends \Core_ServiceLayer_Service_CrudDto
{

    protected $_entityName = 'app:VwAreaTrabalho';

    /**
     * Retorna a quantidade dos itens que estão na minha
     * caixa do tipo de $sqTipoArtefato informado
     *
     * @param  int $sqPessoa
     * @param  int $sqUnidadeOrg
     * @param  int $sqTipoArtefato
     * @return int
     */
    public function getQtdItensMinhaCaixa ($sqPessoa, $sqUnidadeOrg, $sqTipoArtefato)
    {
        return (int) $this->_getRepository()->getQtdItensMinhaCaixa($sqPessoa, $sqUnidadeOrg, $sqTipoArtefato);
    }

    /**
    * Método que obtém dados para a grid
    * @param \Core_Dto_Search $dto
    * @return array
    */
    public function getGrid(\Core_Dto_Search $dto, $withTotalRecord = TRUE)
    {
        $result = $this->_getRepository()->searchPageDto('listGridAreaTrabalho', $dto, $withTotalRecord);
        return $result;
    }

    /**
    * Método que obtém dados para a grid
    * @param \Core_Dto_Search $dto
    * @return array
    */
    public function getGridArquivo(\Core_Dto_Search $dto, $withTotalRecord = TRUE)
    {
        $result = $this->_getRepository()->searchPageDto('listGridAreaTrabalhoArquivo', $dto, $withTotalRecord);
        return $result;
    }

    /**
    * Método que obtém dados para a grid
    * @param \Core_Dto_Search $dto
    * @return array
    */
    public function getGridArquivoSetorial(\Core_Dto_Search $dto, $withTotalRecord = TRUE)
    {
        return $this->_getRepository('app:ArtefatoArquivoSetorial')->searchPageDto('listGrid', $dto, $withTotalRecord);
    }

    public function canEditArtefact(\Core_Dto_Search $dto, $isProcesso = FALSE)
    {

        $repoUTA            = $this->_getRepository('app:VwUltimoTramiteArtefato');

        $isChild            = $this->_getRepository('app:ArtefatoVinculo')->isChild($dto);
        $isAllowedAlter     = in_array(\Core_Integration_Sica_User::getUserProfile(), $this->getUsersAllowedAlterArtefact());

        $listTipoAssuntoSolicitacao = array(
            \Core_Configuration::getSgdoceTipoAssuntoSolicitacaoAlterarCadastro()
        );

        $dtoSolicitacao     = \Core_Dto::factoryFromData(array(
            'sqArtefato' => $dto->getSqArtefato()
        ), 'search');

        $hasDemandaAlteracaoDados = $this->getServiceLocator()
                                         ->getService('Solicitacao')
                                         ->hasDemandaAbertaByAssuntoPessoaResponsavel($dtoSolicitacao, $listTipoAssuntoSolicitacao);
        // Se processo, verifica se existe vinculo além do tipo de autuação
        $hasProcessoVinculo = false;
        if( $isProcesso ) {
            $rsVinculosProcesso = $this->_getRepository('app:ArtefatoVinculo')
                                       ->findByNot(array(
                                           'sqArtefatoPai' => $dto->getSqArtefato()
                                       ), array(
                                           'sqTipoVinculoArtefato' => \Core_Configuration::getSgdoceTipoVinculoArtefatoAutuacao()
                                       ));
            $hasProcessoVinculo = (count($rsVinculosProcesso));
        }

        //se $areaTrabalho, logo, esta na area da pessoa logada
        //caso contrario retorna NULL
        $areaTrabalho = $this->_getRepository()->findArtefato($dto);

        //SGI pode alterar o documento se houver demanda de suporte do tipo alterar dados ou alterar grau ou se estiver na minha da area de trabalho dele.
        if (($isAllowedAlter && $hasDemandaAlteracaoDados) || ($isAllowedAlter && $areaTrabalho)) {
            return TRUE;
        }

        if ($areaTrabalho &&
            $repoUTA->isFirstTramite($dto) &&                             //deve ser o 1º tramite
            (!$areaTrabalho->getHasVinculo() || !$hasProcessoVinculo) &&  //não pode ter vinculo
            !$areaTrabalho->getArquivado() &&                             //não pode estar arquivado
            !$areaTrabalho->getHasSolicitacaoAberta() &&                  //não pode ter solicitação em aberto
            !$isChild                                                     //não pode ser filho de ninguem exceto(material apoio, despacho e referencia)
            ) {
            return TRUE;
        }

        return FALSE;
    }

    public function getNotification()
    {
        return $this->_getRepository('app:VwAreaTrabalho')->getNotification();
    }

    /**
     * Usuários autorizados para alteração de documentos sem validação nenhuma.
     *
     * @return array
     */
    public function getUsersAllowedAlterArtefact()
    {
        return array(
            \Core_Configuration::getSgdocePerfilSedoc(),
            \Core_Configuration::getSgdocePerfilSgi(),
        );
    }

    public function findArtefato(\Core_Dto_Search $dto, $returnArray = FALSE)
    {
        return $this->_getRepository()->findArtefato($dto, $returnArray);
    }

}
