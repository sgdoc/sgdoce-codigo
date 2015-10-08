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
 * Classe Service Logger WS
 *
 * @package      Principal
 * @subpackage   Services
 * @name         Perfil
* @version      1.0.0
* @since         2012-08-17
*/

namespace Principal\Service;

use Doctrine\Common\Util\Debug;
use Doctrine\ORM\Events,
Doctrine\Common\EventSubscriber,
Doctrine\ORM\Event\LifecycleEventArgs,
Doctrine\ORM\Query\ResultSetMapping;

class LoggerWs extends \Sica_Service_Crud
{
    //     protected $_entityName = 'app:Sistema';

    /**
     * Atributo de identificação do tipo de operação
     */
    public static $sqOperacao = 'I';

    const NUM_ZERO = 0;

    /*
     *
    */
    public function saveLogger($data, $type, $options = array(), $metodo)
    {
        $params = self::getParams($data, $type, $options, $metodo);
        $this->_getRepository('app:LoggerWs')->saveTrilhaAuditoria($params);
    }

    /**
     *
     */
    protected static function getParams($data, $type, $options, $metodo)
    {
        self::checaOperacaoWs($metodo);

        $session = \Core_Integration_Sica_User::has();
        $sqUsuario = (\Core_Integration_Sica_User::getUserId()) ? \Core_Integration_Sica_User::getUserId() : null;
        $perfilUsuario = $session ? \Core_Integration_Sica_User::getUserProfileExternal() : true;
        $sistema  = \Core_Integration_Sica_User::getInfoSystem(\Core_Integration_Sica_User::getUserSystem());
        $sis = \Zend_Registry::get('doctrine')->getEntityManager()
            ->getRepository('app:Sistema')->findBySgSistema('SICA-e');
        $sqSistema = $session ?
            $sistema['sqSistema'] :
            $sis[0]->getSqSistema()
        ;
        $sgSistema = $session ?
            $sistema['sgSistema'] :
            $sis[0]->getSgSistema()
        ;
        $request  = new \Zend_Controller_Request_Http();
        $arrRequest = explode('/', $request->getRequestUri());

        $params = array(
                'sqSistema'        => (int)$sqSistema,
                'sgSistema'        => (string)$sgSistema,
                'sqClasse'         => self::NUM_ZERO,
                'noClasse'         => (string)$arrRequest[1] . '/' . $arrRequest[2],
                'sqMetodo'         => self::NUM_ZERO,
                'noMetodo'         => (string) 'index'  /*$arrRequest[3]*/,
                'sqUsuario'        => $sqUsuario,
                'sgOperacao'       => self::$sqOperacao ,
                'stUsuarioExterno' => (int)$perfilUsuario
        );

        $params['xmTrilha'] = self::geraTagXml($type);

        return $params;
    }

    protected static function checaOperacaoWs($metodo)
    {
        $save = preg_match('/libCorpSave/',$metodo);
        $update = preg_match('/libCorpUpdate/',$metodo);
        $delete = preg_match('/libCorpDelete/',$metodo);

        if ($delete){
            self::$sqOperacao = 'D';
        }

        if($update){
            self::$sqOperacao = 'U';
            return true;
        }

        if($save){
            self::$sqOperacao = 'I';
            return true;
        }
    }

    private static function geraTagXml($type)
    {
        $request  = new \Zend_Controller_Request_Http();
        $arrRequest = explode('/', $request->getRequestUri());
        if (!empty($arrRequest[3])){
            $rota = (string)$arrRequest[1] . '/' . $arrRequest[2] .'/' . $arrRequest[3];
        } else {
            $rota = (string)$arrRequest[1] . '/' . $arrRequest[2];
        }

        $xml = "<schema>";
        $xml.= "<nome>corporativo</nome>";
        $xml.= "<rota>$rota</rota>";
        $xml.= "<tabela>";

        foreach($type as $key => $value){
            $nomeTabela = self::converteString($key);
            $xml.= "<nome>" . $nomeTabela."</nome>";
            foreach($value as $k => $v){
                if(!is_array($v) && !is_object($v)){
                    $xml.= "<coluna>";
                    $palavraConvertida = self::converteString($k);
                    $xml.= "<nome>" . $palavraConvertida ."</nome>";
                    $xml.= "<valor>".$v."</valor>";
                    $xml.= "</coluna>";
                }
            }
        }

        $xml.= "</tabela>";
        $xml.= "</schema>";

        return $xml;
    }

    private static  function converteString($palavra)
    {
        preg_match_all('/[A-Z]/',$palavra,$palavraSemConversao);
        $palavraConvertida = $palavra;
        foreach (current($palavraSemConversao) as $letra){
            $palavraConvertida =  str_replace($letra,'_'.strtolower($letra),$palavraConvertida);
        }

        return $palavraConvertida;
    }

}