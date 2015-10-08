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

namespace Artefato\Service;
use Doctrine\Common\Util\Debug;

/**
 * Classe para Service de Artefato
 *
 * @package  Artefato
 * @category Service
 * @name     Artefato
 * @version  1.0.0
 */
class AnexoArtefato extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * @var string
     */
    protected $_entityName = 'app:AnexoArtefato';

    /**
     * inclusao da imagem no banco de dados para um determinado artefato
     * @param type $dto
     * @return string
     */
    public function insertAnexos($dto)
    {
        // upload das imagens pelo Zend
        $upload = $this->_upload($dto);
        // verificando a existencia de erros
        if (isset($upload['errors'])) {
            return $upload;
        }
        // iniciando a entidade (lere-lere)
        $criteria = array('sqArtefato' => $dto->getSqArtefato());
        $artefato = $this->_getRepository('app:Artefato')->findOneBy($criteria);
        // verificando se a entidade existe (ou responde)
        if (!$artefato) {
            return 'ArtefatoInexistente';
        }
        // iniciando variaveis
        $nuPagina = count($this->listGridAnexos($dto)) + 1;
        $files = $upload;
        $entity = $this->getEntityManager();

        $inFrente = $this->verificaFrenteVerso($dto->getInFrente());

        $cont = 1;
        // percorrendo registros para inclusao e tratamento da mesma
        foreach ($files as $value) {
            $size = substr_replace($value['size'], '', -2);
            $endereco = array_reverse(explode("../", $value['name']));
            $criteria = array('deCaminhoArquivo'=> '../'.$endereco[0], 'sqArtefato' => $artefato);
            $arquivoExiste = $this->_getRepository('app:AnexoArtefato')->findBy($criteria);
            if (!empty($arquivoExiste)) {
                return array('errors' => array('Item já incluído na lista'));
            }
            // setando imagem e iformacoes
            $anexo = new \Sgdoce\Model\Entity\AnexoArtefato();
            $anexo->setSqArtefato($artefato);
            $anexo->setNuPagina($nuPagina);
            $anexo->setDeCaminhoArquivo('../' . $endereco[0]);
            $anexo->setDeExtensaoArquivo('png');
            $anexo->setNuTamanhoArquivo($size);

            $this->setaInFrente($anexo, $inFrente, $cont);

//            $anexo->setInFrente($dto->getInFrente() == 'F');
            //persistindo entidade
            $entity->persist($anexo);
            $nuPagina++;
            $cont++;
        }
        // 'comitando'  e retornando o sucesso
        $entity->flush();
        return array('success' => true);
    }

    /**
     * Verifica se é frente ou verso
     * @param type $inFrente
     * @return boolean
     */
    public function verificaFrenteVerso($inFrente)
    {
        if ($inFrente == 'F') {
            return TRUE;
        }

        return FALSE;
    }

    /**
     *
     * @param type $anexo
     * @param type $inFrente
     * @param type $cont
     * @return boolean
     */
    public function setaInFrente(&$anexo, $inFrente, $cont)
    {
        if ($inFrente) {
            $inFrente = TRUE;
        }
        else {
            if (!$inFrente && ($cont % 2 != 0)) {
                $inFrente = TRUE;
            }
            else {
                $inFrente = FALSE;
            }
        }

        $anexo->setInFrente($inFrente);
        return TRUE;
    }

    /**
     * carrega a lista de imagens
     * @param type $dto
     * @return type
     */
    public function listGridAnexos($dto)
    {
        // retornando registro
        return $this->_getRepository()->listGridAnexos($dto);
    }

    /**
     * carrega a lista de imagens
     ** @param \Core_Dto_Search $dto
     * @return type
     */
    public function listGridImagem(\Core_Dto_Search $dto)
    {
        $result = $this->_getRepository()->searchPageDto('listGridImagem', $dto);
        return $result;
    }

    /**
     * Expande a imagem no tamanho real
     * @param type $dto
     * @return type
     */
    public function showImage($dto)
    {
        // buscando e retornando imagem em seu tamanho real
        $anexo = $this->find($dto->getSqAnexoArtefato());
        $endereco = $anexo->getDeCaminhoArquivo();
        return Imagem::showImage($dto, $endereco);
    }

    /**
     * Metod que deleta anexo de um artefato
     * @param type $sqAnexoArtefatos
     * @return type
     */
    public function deleteAnexos($sqAnexoArtefatos)
    {
        // percorrendo registro para deletar o anexo.
        foreach ($sqAnexoArtefatos as $sqAnexoArtefato) {
            $anexo = $this->find($sqAnexoArtefato);
            $fileinfo = pathinfo($anexo->getDeCaminhoArquivo());
            $nome = $fileinfo['filename'];
            $extensao = strtolower($fileinfo['extension']);
            $sqArtefato = $anexo->getSqArtefato()->getSqArtefato();
            try {
                $this->getEntityManager()->remove($anexo);
            } catch (\Exception $exc) {
                return array("error" => "Imagem " . $nome . "." . $extensao . " está sendo usada.");
            }
        }
        // persistindo e retornando resultado
        $this->getEntityManager()->flush();

        // reordenado contagem
        $this->reordenandoImagens($sqArtefato);

        return array('success' => true);
    }

    /**
     * Metodo responsavel por deletar todas as imagens de uma artefato
     * @param type $sqArtefatos
     * @return type
     */
    public function deleteAll($sqArtefatos)
    {
        // iniciando entidade artefato
        $criteria = array('sqArtefato' => $sqArtefatos->getSqArtefato());
        $sqAnexoArtefatos = $this->_getRepository()->findBy($criteria);
        // deletando imagens
        foreach ($sqAnexoArtefatos as $sqAnexoArtefato) {
            $anexo = $this->find($sqAnexoArtefato->getSqAnexoArtefato());
            $fileinfo = pathinfo($anexo->getDeCaminhoArquivo());
            $nome = $fileinfo['filename'];
            $extensao = strtolower($fileinfo['extension']);
            try {
                $this->getEntityManager()->remove($anexo);
            } catch (\Exception $exc) {
                return array("error" => "Imagem " . $nome . "." . $extensao . " está sendo usada.");
            }
        }
        // confirmando a remocao pra aplicacao
        $this->getEntityManager()->flush();
        return array('success' => true);
    }

    /**
     * Metodo responsavel por reordenar a lista de imagens de um artefato
     * @param type $dto
     * @param type $arrAnexoArtefato
     * @return array
     */
    public function ordenalista($dto, $arrAnexoArtefato)
    {
        // iniciando contador da pagina
        $posicao          = 1;
        $arrFrente        = array();
        $anexoAnterior    = NULL;
        $inFrenteAnterior = TRUE;

        // carregando entidade artefato
        $criteria = array('sqArtefato' => $dto->getArtefato());
        $sqArtefato = $this->_getRepository()->findBy($criteria);
        // reordenando os anexos por sqAnexoArtefato
        foreach ($arrAnexoArtefato as $anexoArtefato) {
            $criteria = array('sqAnexoArtefato' => $anexoArtefato);
            $sqAnexoArtefato = $this->_getRepository()->findOneBy($criteria);

            $sqAnexoArtefato->setNuPagina($posicao++);
            $this->getEntityManager()->persist($sqAnexoArtefato);

            if($sqAnexoArtefato->getInVersoBranco()) { $posicao++; }
            // para ajudar na ordenacao, salvo o objeto anterior
            $anexoAnterior = $sqAnexoArtefato;
        }
        // persisitndo e retornando o sucesso
        $this->getEntityManager()->flush();
        return array('success' => true);
    }

    /**
     * Metodo responsavel por modificar setar o verso
     * @param type $dto
     * @return type
     */
    public function setaVersoBranco($dto)
    {
        //recuperando anexo e setando novo valor
        $criteria = array('sqAnexoArtefato' => $dto->getSqAnexoArtefato());
        $sqAnexoArtefato = $this->_getRepository()->findOneBy($criteria);
        $sqAnexoArtefato->setInVersoBranco($dto->getInValueBranco());
        $this->getEntityManager()->persist($sqAnexoArtefato);
        $this->getEntityManager()->flush();

        // corrigindo a ordenacao das imagens na base
        $this->reordenandoImagens($dto->getSqArtefato());

        return array('success' => true);
    }

    /**
     * executa a REordenacao dos anexos
     * @param type $sqArtefato
     * @return type
     */
    public function reordenandoImagens($sqArtefato)
    {
        $posicao = 0;

        $criteria = array('sqArtefato' => $sqArtefato);
        $listAnexoArtefato = $this->_getRepository()->findBy($criteria);

        // reordenando os anexos por sqAnexoArtefato
        foreach ($listAnexoArtefato as $sqAnexoArtefato) {
            $posicao++;

            $sqAnexoArtefato->setNuPagina($posicao);
            if($sqAnexoArtefato->getInVersoBranco()){
                $posicao++;
            }
            $this->getEntityManager()->persist($sqAnexoArtefato);
        }

        // persisitndo e retornando o sucesso
        $this->getEntityManager()->flush();
        return array('success' => true);
    }

    /**
     * metod que realiza o upload e a persistencia da imagem
     */
    protected function _upload($dto)
    {
        if ($dto->getInInserir() == 'imagem') {
            $validFile      = TRUE;
            $thumb          = TRUE;
            $invalidFile    = FALSE;
            $validImageSize = TRUE;
        }
        else if ($dto->getInInserir() == 'materialApoio'){
            $validFile      = FALSE;
            $thumb          = FALSE;
            $invalidFile    = TRUE;
            $validImageSize = FALSE;
        }

        return Imagem::upload('anexoArtefato', $thumb, $validFile, $invalidFile, $validImageSize);
    }

    /**
     * Altera o campo frente/verso de uma imagem
     * return array
     */
    public function alteraFrenteVerso($dto)
    {
        $anexoArtefato = $this->_getRepository()->find($dto->getSqAnexoArtefato());
        $anexoArtefato->setInFrente($dto->getInFrente());
        $this->getEntityManager()->persist($anexoArtefato);
        $this->getEntityManager()->flush($anexoArtefato);
        return TRUE;
    }
}