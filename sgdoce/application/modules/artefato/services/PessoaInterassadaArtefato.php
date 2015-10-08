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

use Sgdoce\Model\Entity\PessoaInteressadaArtefato;
/**
 * Classe para Service de Pessoa
 *
 * @package  Minuta
 * @category Service
 * @name     Pessoa
 * @version  1.0.0
 */
class PessoaInterassadaArtefato extends \Core_ServiceLayer_Service_CrudDto
{
    /**
     * Variável para receber a entidade VwPessoa
     * @var    string
     * @access protected
     * @name   $_entityNameCorp
     */
    protected $_entityName = 'app:PessoaInteressadaArtefato';

    public function searchPessoaInteressada(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->searchPessoaInteressada($dto);
    }

    /**
     * Metódo que realiza o save Pessoa Interessada
     */
    public function savePessoaInteressada($pessoaEntity)
    {
        $entityPessoaArtefato = $this->_newEntity('app:PessoaInteressadaArtefato');

        $entityArtefato = $this->getEntityManager()
        ->getPartialReference('app:Artefato',$pessoaEntity->getSqArtefato()->getSqArtefato());

        $entityPessoaSgdoce = $this->getEntityManager()
        ->getPartialReference('app:PessoaSgdoce',$pessoaEntity->getSqPessoaSgdoce()->getSqPessoaSgdoce());

        $entityPessoaArtefato->setSqArtefato($entityArtefato);
        $entityPessoaArtefato->setSqPessoaSgdoce($entityPessoaSgdoce);

        $this->getEntityManager()->persist($entityPessoaArtefato);
        $this->getEntityManager()->flush($entityPessoaArtefato);
        return $entityPessoaArtefato;
    }

    /**
     * Realiza consulta pessoa existente
     * @param \Core_Dto_Search $dtoSearch
     */
     public function findPessoaInteressada(\Core_Dto_Search $entityPessoaSgdoce)
     {
         return $this->_getRepository()->findPessoaInteressada($entityPessoaSgdoce);
     }

    /**
     * método para pesquisa de grid de Interessado no Artefato
     * @param \Core_Dto_Search $dto
     */
    public function listGridInteressadosArtefato(\Core_Dto_Search $dto)
    {
        $result = $this->_getRepository()->searchPageDto('listGridInteressadosArtefato', $dto);
        return $result;
    }

    /**
     * método para pesquisa de grid de Interessado no Artefato
     * @param \Core_Dto_Search $dto
     */
    public function getPessoaInteressadaArtefato(\Core_Dto_Search $dto)
    {
    	return $this->_getRepository()->getPessoaInteressadaArtefato($dto);
    }

    /**
     * método que retorna a quantidade de Interessados no Artefato
     * @param \Core_Dto_Search $dto
     */
    public function countInteressadosArtefato(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->countInteressadosArtefato($dto);
    }

    /**
     * método que retorna a quantidade de Interessados no Artefato
     * @param \Core_Dto_Search $dto
     */
    public function countInteressadosArtefatoValido(\Core_Dto_Search $dto)
    {
        return $this->_getRepository()->countInteressadosArtefatoValido($dto);
    }

    /**
     * método para pesquisa de grid de Interessado no Artefato
     * @param $array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
    	$return = $this->_getRepository()->findBy($criteria);
    	if(count($return) > 0){
			return $return[0];
    	}
    	return NULL;
    }

    /**
     * método para pesquisa de grid de Interessado no Artefato
     * @param $array
     */
    public function findByAll(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
    	$return = $this->_getRepository()->findBy($criteria);
    	if(count($return) > 0){
			return $return;
    	}
    	return NULL;
    }

    /**
     * @param \Core_Dto_Abstract $dto
     * @return BooleanType
     */
    public function deleteTodosInteressado($sqArtefato)
    {
    	return $this->_getRepository('app:PessoaInteressadaArtefato')->deleteTodosInteressado($sqArtefato);
    }
}
