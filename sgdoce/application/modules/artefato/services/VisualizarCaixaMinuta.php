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
 * Classe para Service de Visualizar Caixa de Minuta
 *
 * @package  Minuta
 * @category Service
 * @name      VisualizarCaixaMinuta
 * @version  1.0.0
 */
class VisualizarCaixaMinuta extends \Core_ServiceLayer_Service_CrudDto
{
    /**
    * Constante para receber o valor do padrao modelo documento atos
    * @var integer
    * @name   SQ_PADRAO_MODELO_DOCUMENTO_ATOS
    */
    const SQ_PADRAO_MODELO_DOCUMENTO_ATOS   = 1;

    /**
     * Constante para receber o valor do padrao modelo documento geral
     * @var integer
     * @name   SQ_PADRAO_MODELO_DOCUMENTO_GERAL
     */
    const SQ_PADRAO_MODELO_DOCUMENTO_GERAL  = 2;

    /**
     * Constante para receber o valor do padrao modelo documento oficio
     * @var integer
     * @name   SQ_PADRAO_MODELO_DOCUMENTO_OFICIO
     */
    const SQ_PADRAO_MODELO_DOCUMENTO_OFICIO = 3;

    /**
    * Método que obtém dados para a grid
    * @param \Core_Dto_Search $dto
    * @return array
    */
    public function getGrid(\Core_Dto_Search $dto)
    {
        $repository = $this->getEntityManager ()->getRepository ('Sgdoce\Model\Entity\VwCaixaMinuta');
        $result     = $repository->searchPageDto('listGridMinutas', $dto);
        return $result;
    }

    /**
    * Método que consulta dados da minuta referente a um sqArtefato
    * @params \Core_Dto_Entity $dto
    * return  array
    */
    public function findCaixaMinuta(\Core_Dto_Entity $dto)
    {
        $repository = $this->getEntityManager()->getRepository('app:VwCaixaMinuta');
        $res = $repository->findCaixaMinuta($dto);
        return $res;
    }

    /**
    * Método que salva o historico do artefato
    * @param \Core_Dto_Entity $dto
    * @return boolean
    */
    public function saveHistorico(\Core_Dto_Entity $dto)
    {
        $entity = $dto->getEntity();

        $objStatus = $this->getEntityManager()
            ->find('\Sgdoce\Model\Entity\StatusArtefato', $entity->getSqStatusArtefato()->getSqStatusArtefato());
        $entity->setSqStatusArtefato($objStatus);

        $objUnidade = $this->getEntityManager()
            ->find('\Sgdoce\Model\Entity\VwUnidadeOrg', $entity->getSqUnidadeOrg()->getSqUnidadeOrg());
        $entity->setSqUnidadeOrg($objUnidade);

        $objArtefato = $this->getEntityManager()
            ->find('\Sgdoce\Model\Entity\Artefato', $entity->getSqArtefato()->getSqArtefato());
        $entity->setSqArtefato($objArtefato);

        $objPessoa = $this->getEntityManager()
            ->find('\Sgdoce\Model\Entity\VwPessoa', $entity->getSqPessoa()->getSqPessoa());
        $entity->setSqPessoa($objPessoa);

        $objOcorrencia = $this->getEntityManager()
            ->find('\Sgdoce\Model\Entity\Ocorrencia', $entity->getSqOcorrencia()->getSqOcorrencia());
        $entity->setSqOcorrencia($objOcorrencia);

        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush($entity);

        return TRUE;
    }

    /**
    * Método que obtém dados do envio anterior no histórico do artefato
    * @param  \Core_Dto_Entity $dto
    * @return array
    */
    public function getDataHistoricoEnvioAnterior(\Core_Dto_Entity $dto)
    {
        $repository = $this->getEntityManager()->getRepository('app:HistoricoArtefato');
        $res = $repository->getDataHistoricoEnvioAnterior($dto);
        return $res;
    }

    /**
    * Método que obtém dados do penúltimo histórico do artefato
    * @param  \Core_Dto_Entity $dto
    * @return array
    */
    public function getPenultimateHistArt(\Core_Dto_Entity $dto)
    {
        $repository = $this->getEntityManager()->getRepository('app:HistoricoArtefato');
        $res = $repository->getPenultimateHistArt($dto);
        return $res;
    }

    /**
    * Método que pesquisa as unidades orgs
    * @param \Core_Dto_Search $dto
    * @return array
    */
    public function searchUnidadeOrgs($dto)
    {
        if ($dto['query']){
            return $repository = $this->getEntityManager()
                ->getRepository('app:VwUnidadeOrg')
                ->searchUnidadesOrganizacionais($dto);
        }

        return NULL;
    }

    /**
     * Método que pesquisa as pessoas vinculadas as funcionalidades de minuta
     * @param \Core_Dto_Search $dto
     * @return array
     */
    public function searchPessoas(\Core_Dto_Search $dto)
    {
        if ($dto->getQuery()){
            return $repository = $this->getEntityManager()
            ->getRepository('app:VwProfissional')
            ->searchPessoas($dto);
        }

        return NULL;
    }

    /**
    * Método que cria Documento para View HTML
    * @param \Core_Dto_Entity $dtoEntityPessoa
    * @param \Core_Dto_Entity $dtoEntityModelo
    * @return $this->doc()
    */
    public function createDocView($dtoEntityArtefato, $dtoEntityModelo)
    {
        $view = TRUE;
        return $this->doc($dtoEntityArtefato, $dtoEntityModelo, $view);
    }

    /**
    * Método que cria Documento para PDF
    * @param \Core_Dto_Entity $dtoEntityPessoa
    * @param \Core_Dto_Entity $dtoEntityModelo
    * @return $this->doc()
    */
    public function createDocPdf($dtoEntityArtefato, $dtoEntityModelo)
    {
        $view = FALSE;
        return $this->doc($dtoEntityArtefato, $dtoEntityModelo, $view);
    }

    /**
    * Método que prepara o documento com os dados
    * @param \Core_Dto_Entity $dtoEntityPessoa
    * @param \Core_Dto_Entity $dtoEntityModelo
    * @param boolean $view
    * @return string $file
    */
    public function doc($dtoEntityArtefato, $dtoEntityModelo, $view)
    {
        $registry = \Zend_Registry::get('configs');
        $options  = array('path' => $registry['folder']['visualizaMinuta']);

        // inicio consultas dos dados de todos 'pessoa_funcao' envolvidas na minuta
        $arrOrigem      = $this->getEntityManager()->getRepository('app:ArtefatoMinuta')
                                                                          ->getPessoaOrigemArtefato($dtoEntityArtefato);

        $arrDestino     = $this->getEntityManager()->getRepository('app:ArtefatoMinuta')
                                                                    ->getPessoaDestinatarioArtefato($dtoEntityArtefato);

        $arrInteressado = $this->getEntityManager()->getRepository('app:PessoaInteressadaArtefato')
                                                                     ->getPessoaInteressadaArtefato($dtoEntityArtefato);

        $arrAssinatura  = $this->getEntityManager()->getRepository('app:ArtefatoMinuta')
                                                                      ->getPessoaAssinaturaArtefato($dtoEntityArtefato);

        $arrRodape      = $this->getEntityManager()->getRepository('app:PessoaArtefato')
                                                                          ->getPessoaArtefatoRodape($dtoEntityArtefato);
        // fim consultas dos dados de todos 'pessoa_funcao' envolvidas na minuta

        $objModeloMinuta   = $this->getServiceLocator()->getService('modeloMinuta')->findModelo($dtoEntityModelo);
        $arrCampo = $this->getCamposModelo($objModeloMinuta['sqModeloDocumento']);
        $entityArtefato = $this->getEntityManager()->getRepository('app:Artefato')->find($dtoEntityArtefato->getSqArtefato());
        // barcode do numero da digital
        $config = new \Zend_Config(
                array(
                        'barcode' => 'Code25interleaved',
                        'barcodeParams' => array(
                                'text' => str_replace(array('E', '-'), '', $arrOrigem['nuDigital']),
                                'drawText' => FALSE,
                                'barHeight' => 50,
                                'barWidth' => 60
                        ),
                        'renderer' => 'image',
                        'rendererParams' => array(
                                'imageType' => 'gif'
                        )
                )
        );

        $path = current(explode ('application', __DiR__))
            . 'data'    . DIRECTORY_SEPARATOR
            . 'upload'  . DIRECTORY_SEPARATOR
            . 'documento' . DIRECTORY_SEPARATOR;

        $imgBarcode = $path . 'barcode.jpg';

        if(file_exists($imgBarcode)) {
            unlink($imgBarcode);
        }

        $renderer = \Zend_Barcode::factory($config)->draw();
        imagejpeg($renderer, $imgBarcode);
        imagedestroy($renderer);

        $arrOrigem['bar_code'] = $imgBarcode;

        // path para o arquivo utilizado como referencia para criacao do pdf
        \Core_Doc_Factory::setFilePath(APPLICATION_PATH . '/modules/artefato/views/scripts/visualizar-caixa-minuta');

        $file = "{$objModeloMinuta['noPadraoModeloDocumento']}.pdf";

        $data = array(
            'arrOrigem'      => $arrOrigem,
            'arrDestino'     => $arrDestino,
            'arrInteressado' => $arrInteressado,
            'arrAssinatura'  => $arrAssinatura,
            'arrRodape'      => $arrRodape,
            'arrCampo'       => $arrCampo,
            'viewFor'        => $view,
            'entity'         => $entityArtefato
        );

        // retorna os dados para a controller para renderizar no phtml e não sno pdf..
        if ($view) {
            return $data;
        }

        return $this->gerarDoc($objModeloMinuta['sqPadraoModeloDocumento'], $data, $options, $file);
    }

    /**
     *
     * Método que gera documento
     * @param integer $sqObjModeloMinuta
     * @param array $data
     * @param array $options
     * @param string $file
     * @return file
     */
    public function gerarDoc($sqObjModeloMinuta, $data, $options, $file)
    {
        //caso precise tratar as tags html, inserir o seguinte código $data = $this->trataTags($data);

        switch ($sqObjModeloMinuta){
            case self::SQ_PADRAO_MODELO_DOCUMENTO_ATOS:
                \Core_Doc_Factory::write('visualizarMinutaAtosPdf', $data, $options['path'], $file);
                break;
            case self::SQ_PADRAO_MODELO_DOCUMENTO_GERAL:
                \Core_Doc_Factory::write('visualizarMinutaGeralPdf', $data, $options['path'], $file);
                break;
            case self::SQ_PADRAO_MODELO_DOCUMENTO_OFICIO:
                \Core_Doc_Factory::write('visualizarMinutaOficioPdf', $data, $options['path'], $file);
                break;
        }

        return $file;
    }

    /**
     * Método que chama o método que trata as tags html
     * @param array $data
     * @return array
     */
    public function trataTags($data)
    {
        $data = $this->trataDataTagsSimp($data, 'arrOrigem');
        $data = $this->trataDataTagsComp($data, 'arrDestino');
        $data = $this->trataDataTagsComp($data, 'arrInteressado');
        $data = $this->trataDataTagsComp($data, 'arrAssinatura');
        $data = $this->trataDataTagsSimp($data, 'arrRodape');

        return $data;
    }

    /**
     * Método que retorna as tags de formatação html
     * @return array
     */
    public function tagHtmlFormat()
    {
        return array('<b>', '<big>', '<em>', '<i>', '<small>', '<strong>', '<sub>', '<sup>', '<ins>',
                     '<del>', '<s>', '<strike>', '<u>',
                     '</b>', '</big>', '</em>', '</i>', '</small>', '</strong>', '</sub>', '</sup>', '</ins>',
                     '</del>', '</s>', '</strike>', '</u>',
                     '<br>', '<br />', '<hr>', '<hr />');
    }

    /**
     * Método que trata os dados das tags html
     * @param array $data
     * @param string $pessoaFuncao
     * @return type
     */
    public function trataDataTagsSimp($data, $pessoaFuncao)
    {
        if (!empty($data[$pessoaFuncao])) {
            foreach ($data[$pessoaFuncao] as $key => $data1) {

                    if ($key !== 'dtArtefato') {
                        $data[$pessoaFuncao][$key] = strip_tags($data[$pessoaFuncao][$key], implode('', $this->tagHtmlFormat()));
                    }
            }
        }

        return $data;
    }

    /**
     * Método que trata os dados das tags html
     * @param array $data
     * @param string $pessoaFuncao
     * @return array
     */
    public function trataDataTagsComp($data, $pessoaFuncao)
    {
        if (!empty($data[$pessoaFuncao])) {
            foreach ($data[$pessoaFuncao] as $key => $data1) {
                if ($key !== 'qtdDestinatario') {
                    foreach ($data1 as $key2 => $data2) {
                        $data[$pessoaFuncao][$key][$key2] = strip_tags($data[$pessoaFuncao][$key][$key2], implode('',
                                                                                                $this->tagHtmlFormat()));
                    }
                }
            }
        }

        return $data;
    }

    /**
    * Método que obtém os campos relacionados ao modelo do documento
    * @params integer $sqModeloDocumento
    * return  array
    */
    public function getCamposModelo($sqModeloDocumento)
    {
        $repository = $this->getEntityManager()->getRepository('app:ModeloDocumentoCampo');
        $result = $repository->getCamposModelo($sqModeloDocumento);
        return $result;
    }

    /**
    * Obtém o modelo minuta
    * @param \Core_Dto_Entity $dtoEntityModelo
    * @return array $objModeloMinuta
    */
    public function getModeloMinuta($dtoEntityModelo)
    {
        $objModeloMinuta   = $this->getServiceLocator()->getService('modeloMinuta')->findModelo($dtoEntityModelo);

        return $objModeloMinuta;
    }

    /**
    * Método que obtém dados para a grid em acompanhamento
    * @param \Core_Dto_Search $dto
    * @return array
    */
    public function getFeriados()
    {
        $repository = $this->getEntityManager();
        $result = $repository->getRepository('app:VwFeriado');
        return $result->getFeriados();
    }
}