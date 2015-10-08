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
 *
 * Classe transformação de arquivos GEO para outro formato (KML como default)
 *
 * @package     Core
 * @subpackage  Geo
 * @name        ogr2ogr
 * @version     1.0.0
 * @since       2012-07-06
 */
class Core_Geo_OgrToOgr
{

    const GEO_UNABLE_OPEN_FILE_DRIVERS       = 'Não foi possível abrir o arquivo %d com os seguintes drivers: %s';
    const GEO_FIND_DRIVERS                   = 'Não foi possível encontrar o driver para %d. Drivers disponíveis: %s';
    const GEO_DRIVER_DONT_SUPORT_IMPORT      = 'Driver %d não suporta importação.';
    const GEO_UNABLE_PROCESS_LAYER           = 'Não foi possível processar a camada: %d';
    const GEO_UNABLE_CREATE_LAYER            = '%s este tipo não suporta criação de outras camadas.';
    const GEO_UNABLE_TRANSLATE_FEATURE_LAYER = 'Não foi possível se traduzir a geometria %d no layer %s.';

    private static $instance = NULL;

    private $path = NULL;

    private $pathTemp = NULL;

    private $input;

    private $output;

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new \Core_Geo_OgrToOgr();
        }

        return self::$instance;
    }

    public function setOptions($options)
    {
        $this->input  = ($options['input'])  ? $options['input']  : $this->input;
        $this->output = ($options['output']) ? $options['output'] : $this->output;
    }

    /**
     *
     * função encupsulada para importar arquivos para geoJson.
     *
     * @param string $inputFile
     * @param array  $options
     * @param string $path
     * @param string $pathTemp
     */
    public function toJson($inputFile, $options = NULL, $input = NULL, $output = NULL)
    {
        if (!$input) {
            $input = $this->input;
        }

        $inputFile  = $input . $inputFile;

        if (!$output) {
            $output = $this->output;
        }

        if (!$options) {
            $options[] = 'skipfailures';
        }

        $outputFile = $output . date('YmdHis') . '_json_temp' . '.geojson';

        $result     = $this->ogrToOgr($inputFile, $outputFile, 'geoJSON', $options);

        if ($result) {
            // throw exception;
            return $result;
        }

        $content    = file_get_contents($outputFile);

        // remove arquivo temporário
        unlink($outputFile);

        return $content;
    }

    /**
     *
     * função encupsulada para importar arquivos para KML.
     *
     * @param string $inputFile
     * @param array  $options
     * @param string $path
     * @param string $pathTemp
     */
    public function toKml($inputFile, $options = NULL, $input = NULL, $output = NULL)
    {
        if (!$options) {
            $options[] = 'skipfailures';
        }

        if (!$input) {
            $input = $this->input;
        }

        if (!$output) {
            $output = $this->output;
        }

        $outputFile = $output . date('YmdHis') . '_kml_temp' . '.kml';

        $inputFile  = $input . $inputFile;

        $result     = $this->ogrToOgr($inputFile, $outputFile, 'KML', $options);

        if ($result) {
            //$this->getMessaging()->addMessageError(self::EXTENSAO_OGR_NAO_INSTALADA);
            //throw new \Core_Exception_ServiceLayer_Verification();
        }

        $content    = file_get_contents($outputFile);

        return $content;
    }
    /**
     *
     * Função que transforma o arquivo num formato qualquer para formato conhecido
     *
     * @param string $inputFile
     * @param string $outputFile
     * @param string $outputFormat
     * @param array  $options
     */
    public function ogrToOgr ($inputFile, $outputFile, $outputFormat = 'KML', $options = NULL)
    {
        // pega todos os formatos disponíveis no GDAL / OGR
        OGRRegisterAll();

        $astrLayers = NULL;
        $hSFDriver  = NULL;

        $hDS = OGROpen($inputFile, FALSE, $hSFDriver);

        // erro caso não consiga abrir o arquivo com os drivers disponíveis.
        if ($hDS == NULL) {
            for ($iDriver = 0; $iDriver < OGRGetDriverCount(); $iDriver++) {
                $drivers[] = OGR_DR_GetName(OGRGetDriver($iDriver));
            }

            return sprintf(static::GEO_UNABLE_OPEN_FILE_DRIVERS,
                           $inputFile,
                           implode(',', $drivers));
        }

        // pega o driver de saída.
        for ($iDriver = 0; $iDriver < OGRGetDriverCount() && $hSFDriver == NULL; $iDriver++) {
            if (!strcasecmp(OGR_DR_GetName(OGRGetDriver($iDriver)) , $outputFormat)) {
                $hSFDriver = OGRGetDriver($iDriver);
            }
        }

        // não encontrou driver para saída.
        if ($hSFDriver == NULL) {
            for ($iDriver = 0; $iDriver < OGRGetDriverCount(); $iDriver++) {
                $drivers[] = OGR_DR_GetName(OGRGetDriver($iDriver));
            }

            return sprintf(static::GEO_FIND_DRIVERS,
                           $outputFormat,
                           implode(',', $drivers));

        }

        // driver de saída não suporta gerar arquivo físico.
        if (!OGR_Dr_TestCapability($hSFDriver, ODrCCreateDataSource)) {
            return sprintf(static::GEO_DRIVER_DONT_SUPORT_IMPORT, $outputFormat);

        }

        // cria o arquivo de saída
        $hODS = OGR_Dr_CreateDataSource($hSFDriver, $outputFile, $options);

        if ($hODS == NULL) {
            return OGRERR_FAILURE;
        }

        // processa cada layer do arquivo
        for ($iLayer = 0; $iLayer < OGR_DS_GetLayerCount($hDS); $iLayer++) {
            $hLayer = OGR_DS_GetLayer($hDS, $iLayer);

            if ($hLayer == NULL) {
                return sprintf(STATIC::GEO_UNABLE_PROCESS_LAYER, $ilayer);
            }

            if (count($astrLayers) == 0
             || in_array(OGR_FD_GetName(OGR_L_GetLayerDefn($hLayer)), $astrLayers) != FALSE) {

                if (!$this->translateLayer($hDS, $hLayer, $hODS)) {
                    return OGRERR_FAILURE;
                }
            }
        }

        OGR_DS_Destroy($hDS);
        OGR_DS_Destroy($hODS);

        return NULL;
    }

    /**
     *
     * processas as camadas do arquivo importado
     *
     * @param string $hSrcDS
     * @param array  $hSrcLayer
     * @param string $hDstDS
     */
    public function translateLayer($hSrcDS, $hSrcLayer, $hDstDS)
    {
        // cria layer
        if (!OGR_DS_TestCapability($hDstDS, ODsCCreateLayer)) {
            printf(STATIC::GEO_UNABLE_CREATE_LAYER,
                   OGR_DS_GetName($hDstDS));
            return OGRERR_FAILURE;
        }

        $hFDefn = OGR_L_GetLayerDefn($hSrcLayer);

        $hDstLayer = OGR_DS_CreateLayer($hDstDS,
                                        OGR_FD_GetName($hFDefn),
                                        OGR_L_GetSpatialRef($hSrcLayer),
                                        OGR_FD_GetGeomType($hFDefn),
                                        NULL);

        if ($hDstLayer == NULL) {
            return FALSE;
        }

        // adiciona campos
        for ($iField = 0; $iField < OGR_FD_GetFieldCount($hFDefn); $iField++) {
            if (OGR_L_CreateField($hDstLayer, OGR_FD_GetFieldDefn($hFDefn, $iField), 0) != OGRERR_NONE) {
                return FALSE;
            }
        }

        // processa os features do layer
        OGR_L_ResetReading($hSrcLayer);

        while (($hFeature = OGR_L_GetNextFeature($hSrcLayer)) != NULL) {

            $hDstFeature = OGR_F_Create(OGR_L_GetLayerDefn($hDstLayer));

            if (OGR_F_SetFrom($hDstFeature, $hFeature, FALSE) != OGRERR_NONE) {
                OGR_F_Destroy($hFeature);

                printf(static::GEO_UNABLE_TRANSLATE_FEATURE_LAYER,
                       OGR_F_GetFID($hFeature), OGR_FD_GetName($hFDefn));
                return FALSE;
            }

            OGR_F_Destroy($hFeature);

            if (OGR_L_CreateFeature($hDstLayer, $hDstFeature) != OGRERR_NONE) {
                OGR_F_Destroy($hDstFeature);
                return FALSE;
            }

            OGR_F_Destroy($hDstFeature);
        }

        return TRUE;
    }
}
