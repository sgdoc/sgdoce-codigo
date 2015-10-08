<?php
namespace br\gov\mainapp\library\sisbio\mvcb\business;

use br\gov\mainapp\library\mvcb\business\BusinessAbstract as ParentBusiness;
use br\gov\sial\core\valueObject\ValueObjectAbstract;
use br\gov\sial\core\mvcb\business\exception\BusinessException;

/**
 * Sisbio
 * @package br.gov.mainapp.library.sisbio.mvcb
 * @subpackage business
 * @author Adriell Nascimento Barbosa <adriell.barbosa@icmbio.gov.br>
 **/
class SisbioBusinessAbstract extends ParentBusiness
{
    protected $_valueObject;

    public function __construct () 
    {
    ;
    }
    
    /**
     * @override
     */
    public function getModel ($dsName = NULL)
    {
        return parent::getModel($dsName ? $dsName : 'sisbio');
    }

    /**
     * Retorna o valueObject da business
     * @return mixed[]
     */
    public function getValueObject()
    {
        return $this->_valueObject;
    }

    /**
     * Carrega os dados de um array para o VO da business
     * @param mixed[] $data
     * @return SisbioBusinessAbstract
     */
    public function loadData($data) {
        $this->_valueObject->loadData($data);
        return $this;
    }
    
    /**
     * Seta o valueObject da business
     * @param ValueObjectAbstract $vo
     * @return SisbioBusinessAbstract
     */
    public function setValueObject(ValueObjectAbstract $vo)
    {
        $this->_valueObject = $vo;
        return $this;
    }

    /**
     * Retorna uma linha do banco de dados
     * @param string $return
     * @return mixed[]
     */
    public function getOne($return = 'dvo')
    {
        $fetch = $this->getModel()
                      ->findByParam($this->_valueObject, 1);

        if ($this->wantsVo($return))
            return $fetch->getValueObject();
        else
            return $fetch->getDataViewObject();
    }

    /**
     * Retorna multiplas linhas do banco de dados
     * @param string $return
     * @return mixed[]
     */
    public function getAll($return = 'dvo')
    {
        $fetch = $this->getModel()
                      ->findAll();

        if ($this->wantsVo($return))
            return $fetch->getAllValueObject();
        else
            return $fetch->getAllDataViewObject();
    }

    /**
     * Retorna multiplas linhas do banco de dados apartir de uma busca no valueObject
     * @param integer $limit
     * @param string $return
     * @return mixed[]
     */
    public function getAllByParams($limit = 0, $return = 'dvo')
    {
        $fetch = $this->getModel()
                      ->findByParam($this->_valueObject, $limit);
        
        if ($this->wantsVo($return))
            return $fetch->getAllValueObject();
        else
            return $fetch->getAllDataViewObject();
    }

    /**
     * Verifica se existe uma entrada no banco para os valores do ValueObject atual
     * @return boolean
     */
    public function exists()
    {
        return !$this->getModel()
                     ->findByParam($this->_valueObject)
                     ->getDataViewObject()
                     ->isEmpty();
    }

    /**
     * Remove do banco todas as linhas que são iguais as do ValueObject atual
     * @throws BusinessException
     */
    public function delete()
    {
        try {
            $this->getModel()
                 ->delete($this->_valueObject);

        } catch (BusinessException $exp) {
            throw new BusinessException($exp->getMessage(), $exp->getCode(), $exp);
        }
    }

    /**
     * Adiciona no banco uma linha com os valores do ValueObject atual
     * @return ValueObjectAbstract
     * @throws BusinessException
     */
    public function save()
    {
        try {
            $this->getModel()
                 ->save($this->_valueObject);

            return $this->_valueObject;
        } catch (BusinessException $exp) {
            throw new BusinessException($exp->getMessage(), $exp->getCode(), $exp);
        }
    }

    /**
     * Adiciona no banco uma linha com os valores do ValueObject atual se uma linha parecida não existir
     * @param boolean $noInterrupt
     * @return ValueObjectAbstract
     * @throws BusinessException
     */
    public function saveIfDontExist($noInterrupt = true)
    {
        if ($this->exists()) {
            if ($noInterrupt) return;
            else throw new BusinessException('Essa entrada já existe...');
        }

        try {
            return $this->save();
        } catch (BusinessException $exp) {
            throw new BusinessException($exp->getMessage(), $exp->getCode(), $exp);
        }
    }

    /**
     * Atualiza no banco uma entrada baseada nos valores do ValueObject atual
     * @return ValueObjectAbstract
     * @throws BusinessException
     */
    public function update()
    {
        try {
            $this->getModel()
                 ->update($this->_valueObject);

            return $this->_valueObject;
        } catch (BusinessException $e) {
            throw new BusinessException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Verifica se o tipo desejado é valueObject
     * @param string $type
     */
    private function wantsVo($type) {
        switch ($type) {
            case 'valueObject':
            case 'vo':
            case 'VO':
                return true;
            case 'dataViewObject':
            case 'dvo':
            case 'DVO':
            default:
                return false;
        }
    }
}
