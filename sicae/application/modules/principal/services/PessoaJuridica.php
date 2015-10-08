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
/**
 * SISICMBio
 *
 * Classe Service Usuario
 *
 * @package      Principal
 * @subpackage   Services
 * @name         Perfil
 * @version      1.0.0
 * @since         2012-08-17
 */

namespace Principal\Service;

class PessoaJuridica extends \Sica_Service_Crud
{

    CONST IN_TIPO_ESTABELECIMENTO_MATRIZ = TRUE;
    CONST IN_TIPO_ESTABELECIMENTO_FILIAL = FALSE;

    /**
     * Nome da entidade
     * @var string
     */
    protected $_entityName = 'app:PessoaJuridica';

    /**
     *
     * @param type $entity
     * @param type $dto
     */
    public function preSave($entity, $dto = NULL)
    {
        if ($entity->getInTipoEstabelecimento() === '') {
            $input = $entity->getInput();
            $input['inTipoEstabelecimento'] = TRUE;

            $entity->setInput($input);
        }
    }

    /**
     * Realiza operação de save
     * @param \Core_Dto_Entity $entity
     */
    public function save(\Core_Dto_Entity $entity, $dto = NULL)
    {
        $this->_triggerPreSave($dto->getSqPessoa(), $dto);

        $arrValues = $this->getArrPessoaJuridica($dto);

        if ($dto->getSqPessoa()) {
            $method = 'libCorpUpdatePessoaJuridica';
        } else {
            $method = 'libCorpSavePessoaJuridica';
        }

        $sqPessoa = $this->saveWs('app:PessoaJuridica', $method, $arrValues);

        if (!$sqPessoa) {
            $this->getMessaging()->addErrorMessage('Erro na operação (' . $method . ').');
            throw new \Core_Exception_ServiceLayer_Verification;
        }

        $arrValues['sqIntegracaoPessoaInfoconv']['sqPessoa'] = $sqPessoa;
        $this->saveWs('app:IntegracaoPessoaInfoconv'
                , 'libCorpSaveIntegracaoPessoaInfoconv'
                , $arrValues['sqIntegracaoPessoaInfoconv']);

        //inserir dados de endereco, email e telefone caso nova PJ
        if ($method === 'libCorpSavePessoaJuridica') {
            $this->_processAddressPhoneEmail($sqPessoa, $dto);
        }

        return $sqPessoa;
    }

    /**
     * Salva dados de endereço, telefone e email vindos do infoconv quando for cadastro de nova PJ
     *
     * @param integer $sqPessoa
     * @param \Core_Dto_Mapping $dto
     * @return \Principal\Service\PessoaJuridica
     */
    private function _processAddressPhoneEmail($sqPessoa, \Core_Dto_Mapping $dto)
    {
        $infoconvAddress = $dto->getAddress_infoconv();
        $infoconvPhone   = $dto->getPhone_infoconv();
        $infoconvEmail   = $dto->getEmail_infoconv();

        if ($infoconvAddress) {
            $infoconvAddress = json_decode(str_replace('\\', '', $infoconvAddress));

            $addressMethod     = 'libCorpSaveEndereco';
            $addressRepository = 'app:Endereco';
            $addressFilters    = array('sqCep'=>'Digits');
            $arrAddressValues = array(
                array(
                    'name'=>'sqPessoa',
                    'value'=> $sqPessoa
                ),
                array(
                    'name'=>'sqTipoEndereco',
                    'value'=> \Core_Configuration::getCorpTipoEnderecoInstitucional()
                )
            );

            $fnCropString = function($str, $length){
                if (mb_strlen($str,'UTF-8') > $length){
                    $str = mb_substr($str, 0, $length, 'UTF-8');
                }
                return $str;
            };

            foreach ($infoconvAddress as $key => $value) {
                switch ($key) {
                    case 'sqEstado':
                        //infoconv retorno a sigla do estado
                        if( !is_numeric($value)){
                            $arrEntEstado = $this->_getRepository('app:Estado')->findBySgEstado(trim($value));
                            if ($arrEntEstado){
                                $value = current($arrEntEstado)->getSqEstado();
                            }
                        }
                        break;
                    case 'txComplemento':
                    case 'noBairro':
                        $value = $fnCropString($value, 100);

                        break;
                    case 'txEndereco':
                        $value = $fnCropString($value, 200);
                        break;
                    case 'nuEndereco':
                        $value = $fnCropString($value, 6);
                        break;
                    default:
                        break;
                }

                $arrAddressValues[] = array(
                    'name' => $key,
                    'value'=> $value
                );
            }//endforeach;
            //salva os dados do endereco
            $resultAddress = $this->saveFormWebService($addressRepository, $addressMethod, $arrAddressValues, $addressFilters);
        }
        if ($infoconvPhone) {
            $infoconvPhone = json_decode(str_replace('\\', '', $infoconvPhone));
            $phoneMethod     = 'libCorpSaveTelefone';
            $phoneRepository = 'app:Telefone';
            $phoneFilters    = array(
                    'nuDdd'     => 'Digits',
                    'nuTelefone'=> 'Digits'
                );

            $arrPhoneValues = array(
                array(
                    'name'=>'sqPessoa',
                    'value'=> $sqPessoa
                ),
                array(
                    'name'=>'sqTipoTelefone',
                    'value'=> \Core_Configuration::getCorpTipoTelefoneInstitucional()
                )
            );

            foreach ($infoconvPhone as $key => $value) {
                $arrPhoneValues[] = array(
                    'name' => $key,
                    'value'=> $value
                );
            }//endforeach;

            //salva os dados do telefone
            $resultPhone = $this->saveFormWebService($phoneRepository, $phoneMethod, $arrPhoneValues, $phoneFilters);
        }
        if ($infoconvEmail) {
            $infoconvEmail = json_decode(str_replace('\\', '', $infoconvEmail));
            $emailMethod     = 'libCorpSaveEmail';
            $emailRepository = 'app:Email';
            $arrEmailValues  = array(
                array(
                    'name'=>'sqPessoa',
                    'value'=> $sqPessoa
                ),
                array(
                    'name'=>'sqTipoEmail',
                    'value'=> \Core_Configuration::getCorpTipoEmailInstitucional()
                )
            );

            $arrEmailValues[] = array(
                'name' => 'txEmail',
                'value'=> $infoconvEmail
            );

            //salva os dados do email
            $resultEmail = $this->saveFormWebService($emailRepository, $emailMethod, $arrEmailValues);
        }
        return $this;
    }

    /**
     * Save Pessoa Juridica via webservice
     * @param type $method
     * @param type $arrValues
     * @return type
     */
    public function saveWs($repository, $method, $arrValues)
    {
        return $this->getServiceLocator()->getService('Pessoa')->saveLibCorp($repository, $method, $arrValues);
    }

    /**
     * Salva dados via webservice
     *
     * @param string $repository
     * @param string $method
     * @param array $arrValues
     * @param array $filters
     * @return boolean
     */
    public function saveFormWebService($repository, $method, $arrValues, $filters = array())
    {
        return $this->getServiceLocator()->getService('Pessoa')->saveFormWebService($repository, $method, $arrValues, $filters);
    }

    /**
     * Configura os dados a serem salvos pelo ws
     * @param $dto
     * @return array
     */
    public function getArrPessoaJuridica( $dto )
    {
        $arrData = array(
            'noPessoa' => $dto->getNoPessoa(),
            'noFantasia' => $dto->getNoFantasia(),
            'nuCnpj' => \Zend_Filter::filterStatic($dto->getNuCnpj(), 'Digits'),
            'sqNaturezaJuridica' => $dto->getSqNaturezaJuridica(),
            'sgEmpresa' => $dto->getSgEmpresa(),
            'inTipoEstabelecimento' => $dto->getInTipoEstabelecimento(),
            'stRegistroAtivo' => $dto->getSqPessoa() ? $dto->getStRegistroAtivo() : true
        );

        foreach ($arrData as $key => $value) {
            if (trim($value) == "") {
                unset($arrData[$key]);
            }
        }

        if ($dto->getSqPessoa()) {
            $criteria = array('sqPessoa' => $dto->getSqPessoa());
            $sqPessoaJuridica = $this->_getRepository('app:PessoaJuridica')->findOneBy($criteria);

            $arrData['sqPessoa'] = $sqPessoaJuridica->getSqPessoa()->getSqPessoa();
            $arrData['nuCnpj'] = $sqPessoaJuridica->getSqPessoa()->getSqPessoaJuridica()->getNuCnpj();
            //$arrData['noPessoa'] = $sqPessoaJuridica->getSqPessoa()->getNoPessoa();
        }

        $arrData['sqIntegracaoPessoaInfoconv']['dtIntegracao']    = $dto->getSqIntegracaoPessoaInfoconv_dtIntegracao();
        $arrData['sqIntegracaoPessoaInfoconv']['txJustificativa'] = $dto->getSqIntegracaoPessoaInfoconv_txJustificativa();
        $arrData['sqIntegracaoPessoaInfoconv']['sqPessoaAutora']  = $dto->getSqIntegracaoPessoaInfoconv_sqPessoaAutora();

        return $arrData;
    }

}