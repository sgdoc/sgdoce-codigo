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

namespace Arquivo\Service;

/**
 * Classe para Service de Emprestimo
 *
 * @package  Arquivo
 * @category Service
 * @name     Emprestimo
 * @version  1.0.0
 */
class Emprestimo extends \Core_ServiceLayer_Service_CrudDto
{

    /**
     * Variavel para receber o nome da entidade
     *
     * @var string
     * @access protected
     * @name $_entityName
     */
    protected $_entityName = 'app:Emprestimo';

    /**
     *
     * @param array $data
     * @return array array de objetos Sgdoce\Model\Entity\Artefato
     */
    public function getArtefatoToEmprestimo (array $data)
    {
        return $this->getEntityManager()->getRepository('app:Artefato')->getArtefatoList($data);
    }

    public function saveEmprestimo (\Core_Dto_Search $dto)
    {
        try {

            $filter = new \Zend_Filter();
            $filter->addFilter(new \Zend_Filter_StringTrim)
                    ->addFilter(new \Zend_Filter_StripTags);

            $validate = new \Zend_Validate_StringLength(array('max'=>250,'encoding' => 'UTF-8'));

            $tipo            = $dto->getDestinoInterno();
            $txMotivo        = $filter->filter($dto->getTxMotivo());
            $noPessoaEntregue= $filter->filter($dto->getNoPessoaEntregue());
            if (!$validate->isValid($txMotivo)) {
                throw new \Core_Exception_ServiceLayer_Verification('Texto muito logo para o motivo');
            }
            if (!$validate->isValid($noPessoaEntregue)) {
                throw new \Core_Exception_ServiceLayer_Verification('Texto muito logo para o nome da pessoa a quem será entregue o artefato');
            }
            $dtOperacao      = \Zend_Date::now();
            $sqTipoHistorico = \Core_Configuration::getSgdoceTipoHistoricoArquivoEmprestado();

            if ($tipo == 'externo') {
                $sqPessoa = $dto->getSqPessoa();
            }else{
                $sqPessoa = $dto->getSqPessoaIcmbioDestino();
            }

            foreach($dto->getSqArtefato()->getApi() as $method) {
                $entityArtefato       = $this->_getRepository('app:Artefato')->find($dto->getSqArtefato()->$method());
                $entityCaixaArtefato  = $this->_getRepository('app:CaixaArtefato')
                                             ->findOneBy(array('sqArtefato' => $entityArtefato->getSqArtefato()));

                //gerar historico arquivo com o tipo 8 \Core_Configuration::getSgdoceTipoHistoricoArquivoEmprestado()
                $entityCaixaHistorico = $this->getServiceLocator()->getService('CaixaArtefato')
                                             ->insertHistorico($entityCaixaArtefato, $dtOperacao, $sqTipoHistorico);

                //registrar o emprestimo
                $entityEmprestimo = $this->_newEntity('app:Emprestimo');
                $entityEmprestimo->setSqCaixaHistorico($entityCaixaHistorico)
                        ->setSqPessoaEmprestimo($this->getEntityManager()->getPartialReference('app:VwPessoa',$sqPessoa))
                        ->setTxMotivo($txMotivo)
                        ->setNoPessoaEntregue($noPessoaEntregue);

                $this->getEntityManager()->persist($entityEmprestimo);
            }

            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function saveDevolucao (\Core_Dto_Search $dto)
    {
        try {

            $dtOperacao      = \Zend_Date::now();
            $sqTipoHistorico = \Core_Configuration::getSgdoceTipoHistoricoArquivoDevolvido();

            foreach($dto->getSqArtefato()->getApi() as $method) {

                $entityArtefato       = $this->_getRepository('app:Artefato')->find($dto->getSqArtefato()->$method());

                $this->_checkCanReturn($entityArtefato);

                $entityCaixaArtefato  = $this->_getRepository('app:CaixaArtefato')
                                             ->findOneBy(array('sqArtefato'=>$entityArtefato->getSqArtefato()));

                $this->getServiceLocator()->getService('CaixaArtefato')
                                          ->insertHistorico($entityCaixaArtefato, $dtOperacao, $sqTipoHistorico);
            }

            $this->getEntityManager()->flush();
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function _checkCanReturn(\Sgdoce\Model\Entity\Artefato $entityArtefato)
    {

        $sqTipoHistoricoArquivo = $this->_getRepository('app:CaixaHistorico')->getLastHistorico($entityArtefato->getSqArtefato());

        if(!is_null($sqTipoHistoricoArquivo)){
            if ($sqTipoHistoricoArquivo != \Core_Configuration::getSgdoceTipoHistoricoArquivoEmprestado()) {

                $entityTipoArtefato = $entityArtefato->getSqTipoArtefatoAssunto()->getSqTipoArtefato();
                $noTipoArtefato = $entityTipoArtefato->getNoTipoArtefato();

                if ($entityTipoArtefato->getSqTipoArtefato() == \Core_Configuration::getSgdoceTipoArtefatoProcesso()) {
                    $nuArtefato = $this->getServiceLocator()->getService('Processo')->formataProcessoAmbitoFederal($entityArtefato);
                } else {
                    $nuArtefato = $entityArtefato->getNuDigital()->getNuEtiqueta();
                }

                throw new \Core_Exception_ServiceLayer_Verification(
                        "O {$noTipoArtefato} <b>{$nuArtefato}</b> não esta emprestado. Logo, não pode ser devolvido"
                    );
            }
        }
        return $this;
    }
}
