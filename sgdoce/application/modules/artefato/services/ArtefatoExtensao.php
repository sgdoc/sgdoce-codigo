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

/**
 * Classe para Service de Artefato
 *
 * @package  Artefato
 * @category Service
 * @name     Artefato
 * @version  1.0.0
 */
class ArtefatoExtensao extends \Core_ServiceLayer_Service_CrudDto {

	//Método que Retorna dados para o AutoComplete do Vinculação
	public function returnAutoCompleteVinculacao($entity) {
		$dados = array();

		$pessoaArtefatoOrigem = $this->findPessoaOrigem($entity[0]);
		$dados['sqPessoa'] = NULL;
		$dados['Pessoa'] = NULL;
		$dados['sqTipoDocumento'] = NULL;
		$dados['noTipoDocumento'] = NULL;
		$dados['tipoArtefato'] = NULL;

		if ($pessoaArtefatoOrigem) {
			$dados['sqPessoa'] = $pessoaArtefatoOrigem->getSqPessoaSgdoce()->getNoPessoa();
			$dados['Pessoa'] = $pessoaArtefatoOrigem->getSqPessoaSgdoce()->getSqPessoaSgdoce();
		}

		if ($entity[0]->getSqTipoDocumento()) {
			$dados['sqTipoDocumento'] = $entity[0]->getSqTipoDocumento()->getSqTipoDocumento();
			$dados['noTipoDocumento'] = $entity[0]->getSqTipoDocumento()->getNoTipoDocumento();
		}
		if ($entity[0]->getSqTipoArtefatoAssunto()) {
			$dados['tipoArtefato'] = $entity[0]->getSqTipoArtefatoAssunto()->getSqTipoArtefato()->getSqTipoArtefato();
		}
		return $dados;
	}

	/**
	 * método para salvar vinculo entre artefatos
	 * @param \Core_Dto_Search $dto
	 */
	public function addDocumentoEletronico(\Core_Dto_Search $dto) {
		$entity = NULL;
		if ($dto->getSqTipoArtefato() == 2) {
//Processo
			$artefato = $this->_getRepository('app:Artefato')->findBy(array('nuArtefato' => $dto->getNuArtefatoVinculacao()));
		} else {
//Dossie Referencia e vinculo entre documentos
			$artefato = $this->_getRepository('app:Artefato')->findBy(array('nuDigital' => $dto->getNuDigital()));
		}

		if ($artefato) {
			$sqArtefatoPai = $this->_getRepository('app:Artefato')->find($dto->getSqArtefato());
			$sqArtefatoFilho = $this->_getRepository()->find($artefato[0]->getSqArtefato());
			$sqTipoVinculo = $this->_getRepository('app:TipoVinculoArtefato')->find($dto->getTipoVinculo());

			$entity = new \Sgdoce\Model\Entity\ArtefatoVinculo();
			$entity->setSqArtefatoPai($sqArtefatoPai);
			$entity->setSqArtefatoFilho($sqArtefatoFilho);
			$entity->setSqTipoVinculoArtefato($sqTipoVinculo);
			$entity->setDtVinculo(\Zend_Date::now());

			if ($dto->getInOriginal()) {
				$entity->setInOriginal($dto->getInOriginal());
			} else {
				$entity->setInOriginal(FALSE);
			}
			$this->getEntityManager()->persist($entity);
			$this->getEntityManager()->flush($entity);
		}
		return $entity;
	}

	public function addArtefatoVinculo(\Core_Dto_Search $dto) {
		$entity = NULL;

		$artefato = $this->_getRepository('app:Artefato')->findBy(array('nuDigital' => $dto->getNuDigital()));
		if ($artefato) {
			$sqArtefatoPai = $this->_getRepository('app:Artefato')->find($dto->getSqArtefato());
			$sqArtefatoFilho = $this->_getRepository()->find($artefato[0]->getSqArtefato());
			$sqTipoVinculo = $this->_getRepository('app:TipoVinculoArtefato')->find($dto->getTipoVinculo());

			$entity = new \Sgdoce\Model\Entity\ArtefatoVinculo();
			$entity->setSqArtefatoPai($sqArtefatoPai);
			$entity->setSqArtefatoFilho($sqArtefatoFilho);
			$entity->setSqTipoVinculoArtefato($sqTipoVinculo);
			$entity->setDtVinculo(\Zend_Date::now());
			if ($dto->getInOriginal()) {
				$entity->setInOriginal($dto->getInOriginal());
			} else {
				$entity->setInOriginal(FALSE);
			}
			$this->getEntityManager()->persist($entity);
			$this->getEntityManager()->flush($entity);
		}
		return $entity;
	}

	public function addAnexoArtefatoVinculo(\Core_Dto_Search $dto) {
		// upload das imagens pelo Zend
		$upload = $this->_upload($dto);
		// verificando a existencia de erros
		if (isset($upload['errors'])) {
			return $upload;
		}
		$files = $upload;
		foreach ($files as $value) {
			$endereco = array_reverse(explode("../", $value['name']));

			// setando imagem e iformacoes
			$entity = new \Sgdoce\Model\Entity\AnexoArtefatoVinculo();
			$entity->setSqArtefatoVinculo($dto->getSqArtefatoVinculo());
			$entity->setDeCaminhoAnexo('../' . $endereco[0]);
			$entity->setTxOutroTipo($dto->getTxOutroTipo());
			$tipoAnexo = $this->getEntityManager()->getPartialReference('app:TipoAnexo', $dto->getSqTipoAnexo());
			$entity->setSqTipoAnexo($tipoAnexo);
			$entity->setNoTituloAnexo($dto->getNoTituloAnexo());

			$this->getEntityManager()->persist($entity);
			$this->getEntityManager()->flush($entity);
		}

		return array('success' => TRUE);
	}

	public function findPessoaOrigem($dto) {
		$criteria = array('sqArtefato' => $dto->getSqArtefato(), 'sqPessoaFuncao' => 1);
		return $this->_getRepository('app:PessoaArtefato')->findOneBy($criteria);
	}

	public function delete($sqArtefato) {
		parent::delete($sqArtefato);
	}
}