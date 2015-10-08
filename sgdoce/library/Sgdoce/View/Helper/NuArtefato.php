<?php
/**
 * Copyright 2012 do ICMBio
 *
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
use Artefato\Service\Processo,
    Sgdoce\Model\Entity\Artefato;

/**
 * @package    Sgdoce
 * @subpackage View
 * @subpackage Helper
 * @name       NuArtefato
 * @category   View Helper
 */
class Sgdoce_View_Helper_NuArtefato extends Zend_View_Helper_Abstract
{
    /**
     * @param  array  $data
     * @param  int    $selected
     * @param  array  $config
     * @return string
     */
    public function nuArtefato( Artefato $entArtefato, $sqTipoArtefato = null )
    {
        $return = NULL;
        $coAmbitoProcesso = Processo::T_TIPO_AMBITO_PROCESSO_FEDERAL;

        if( is_null($sqTipoArtefato) ) {
            $sqTipoArtefato = $entArtefato->getSqTipoArtefatoAssunto()
                                          ->getSqTipoArtefato()
                                          ->getSqTipoArtefato();
        }
        // PROCESSO
        if( $sqTipoArtefato == \Core_Configuration::getSgdoceTipoArtefatoProcesso() ) {

            $nuArtefato = $entArtefato->getNuArtefato();

            if( method_exists($entArtefato, 'getSqArtefatoProcesso')
                && $entArtefato->getSqArtefatoProcesso() ) {
                $coAmbitoProcesso = $entArtefato->getSqArtefatoProcesso()
                                                ->getCoAmbitoProcesso();
            }

            // SE AMBITO FEDERAL APLICA MÁSCARA
            if( $coAmbitoProcesso == Processo::T_TIPO_AMBITO_PROCESSO_FEDERAL ) {
                $nuArtefato = $this->mask($nuArtefato);
            }

            return $nuArtefato;
        } else {
            return $entArtefato->getNuDigital()->getNuEtiqueta(TRUE);
        }

        return $return;
    }

    /**
     * @return string
     */
    protected function mask( $nuArtefato )
    {
        $objMask = new \Core_Filter_MaskNumber();
        $mask   = null;
        switch( strlen($nuArtefato) ){
            case 21:
                $mask = Processo::T_MASK_21_DIGITS;
                break;
            case 17:
                $mask = Processo::T_MASK_17_DIGITS;
                break;
            case 15:
                $mask = Processo::T_MASK_15_DIGITS;
                break;
        }
        
        if( !is_null($mask) ){
            $objMask->setMask($mask);
            return $objMask->filter($nuArtefato);
        }
        
        return $nuArtefato;
    }
}
