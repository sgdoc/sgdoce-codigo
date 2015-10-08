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
 * Classe para tratamento de dados geo
 *
 * @package     Core
 * @subpackage  Geo
 * @name        ParseGeo
 * @version     1.0.0
 * @since       2012-08-17
 */
class Core_Geo_ParseGeo
{

    protected $_geoType = array(
                              'point' => 'Point',
                              'linestring' => 'LineString',
                              'multipoint' => 'MultiPoint',
                              'polygon' => 'Polygon',
                              'multipolygon' => 'MultiPolygon',
                              'multilinestring' => 'MultiLineString',
                              // para casos quando houver algum tipo de errado na geracao do arquivo vier multigeometry
                              // oq é errado pois só é aceito poligonos e pontos - filtrado para não se ter problemas
                              'multigeometry' => 'GeometryCollection'
    );

    /**
     *
     * parse kml to well known text
     *
     * @param string $text
     */
    protected function xmlToWkt($text)
    {
        $text = '<?xml version="1.0" encoding="UTF-8"?>
<kml xmlns="http://www.opengis.net/kml/2.2" xmlns:gx="http://www.google.com/kml/ext/2.2" xmlns:kml="http://www.opengis.net/kml/2.2" xmlns:atom="http://www.w3.org/2005/Atom">

<Document>
<name>MultiGeometry Example</name>
<open>1</open>

<Placemark>
<name>Denali</name>
	<LookAt>
		<longitude>-150.9796583117188</longitude>
		<latitude>63.14364409278321</latitude>
		<altitude>0</altitude>
		<range>17283.81114304024</range>
		<tilt>58.61823784728767</tilt>
		<heading>-167.1213681486934</heading>
		<altitudeMode>relativeToGround</altitudeMode>
	</LookAt>
	<Style>
		<IconStyle>
			<scale>2.3</scale>
		</IconStyle>
		<LabelStyle>
			<scale>3</scale>
		</LabelStyle>
		<LineStyle>
			<color>ff1010ff</color>
		</LineStyle>
		<PolyStyle>
			<color>ae2a16ff</color>
		</PolyStyle>
		<LineStyle>
			<color>ffff191d</color>
			<width>3.6</width>
		</LineStyle>
	</Style>
	<MultiGeometry>
		<Point>
			<coordinates>-151.0085,63.1010,0</coordinates>
		</Point>
		<LineString>
			<tessellate>1</tessellate>
			<coordinates>
			-150.8519,63.1928,0 -150.8649,63.1781,0
			-150.8809,63.1730,0 -150.8973,63.1683,0
			-150.9151,63.14773,0 -150.9251,63.1229,0
			</coordinates>
		</LineString>
		<Polygon>
			<tessellate>1</tessellate>
			<outerBoundaryIs>
				<LinearRing>
				<coordinates>
				-150.9955,63.1332,0 -150.9712,63.1586,0
				-151.0273,63.1658,0 -151.0479,63.1362,0
				-151.0112,63.1275,0 -150.9955,63.1332,0
				</coordinates>
				</LinearRing>
			</outerBoundaryIs>
		</Polygon>
	</MultiGeometry>
</Placemark>
<Placemark>
    <name>The Pentagon</name>
    <Polygon>
      <extrude>1</extrude>
      <altitudeMode>relativeToGround</altitudeMode>
      <outerBoundaryIs>
        <LinearRing>
          <coordinates>
            -77.05788457660967,38.87253259892824,100
            -77.05465973756702,38.87291016281703,100
            -77.05315536854791,38.87053267794386,100
            -77.05552622493516,38.868757801256,100
            -77.05844056290393,38.86996206506943,100
            -77.05788457660967,38.87253259892824,100
          </coordinates>
        </LinearRing>
      </outerBoundaryIs>
      <innerBoundaryIs>
        <LinearRing>
          <coordinates>
            -77.05668055019126,38.87154239798456,100
            -77.05542625960818,38.87167890344077,100
            -77.05485125901024,38.87076535397792,100
            -77.05577677433152,38.87008686581446,100
            -77.05691162017543,38.87054446963351,100
            -77.05668055019126,38.87154239798456,100
          </coordinates>
        </LinearRing>
      </innerBoundaryIs>
    </Polygon>
  </Placemark>
</Document>
</kml>';

        $text = mb_strtolower($text, mb_detect_encoding($text));
        $text = preg_replace('/<!\[cdata\[(.*?)\]\]>/s', '', $text);
        $xml  = new DOMDocument();
        $xml->loadXML($text);

        $placemark = $xml->getElementsByTagName('placemark');

        // passa por todo o obj e pega os formatos;
        if (count($placemark) > 0) {
            $placemark->
            // processa o q se encontrou
        } else {
            // processa todo arquivo como se ele já fosse o placemark
        }

        //

//
//        $geometries = array();
//        $geom_types = geoPHP::geometryList();
//        $placemark_elements = $this->xmlobj->getElementsByTagName('placemark');
//        if ($placemark_elements->length) {
//            foreach ($placemark_elements as $placemark) {
//                foreach ($placemark->childNodes as $child) {
//                    // Node names are all the same, except for MultiGeometry, which maps to GeometryCollection
//                    $node_name = $child->nodeName == 'multigeometry' ? 'geometrycollection' : $child->nodeName;
//                    if (array_key_exists($node_name, $geom_types)) {
//                        $function = 'parse'.$geom_types[$node_name];
//                        $geometries[] = $this->$function($child);
//                    }
//                }
//            }
//        } else {
//            // The document does not have a placemark, try to create a valid geometry from the root element
//            $node_name = $this->xmlobj->documentElement->nodeName == 'multigeometry' ? 'geometrycollection' : $this->xmlobj->documentElement->nodeName;
//            if (array_key_exists($node_name, $geom_types)) {
//                $function = 'parse'.$geom_types[$node_name];
//                $geometries[] = $this->$function($this->xmlobj->documentElement);
//            }
//        }
//
//        return geoPHP::geometryReduce($geometries);

    }
//
//
//    protected function childElements($xml, $nodename = '') {
//        $children = array();
//        foreach ($xml->childNodes as $child) {
//            if ($child->nodeName == $nodename) {
//                $children[] = $child;
//            }
//        }
//        return $children;
//    }
//

    /**
    * Extract geometry to a WKT string
    *
    * @param Geometry $geometry A Geometry object
    *
    * @return string
    */
    public function extractData($geometry) {
        $parts = array();
        switch ($geometry->geometryType()) {
            case 'Point':
                return $geometry->getX().' '.$geometry->getY();

            case 'LineString':
                foreach ($geometry->getComponents() as $component) {
                    $parts[] = $this->extractData($component);
                }
                return implode(', ', $parts);

            case 'Polygon':
            case 'MultiPoint':
            case 'MultiLineString':
            case 'MultiPolygon':
                foreach ($geometry->getComponents() as $component) {
                    $parts[] = '('.$this->extractData($component).')';
                }
                return implode(', ', $parts);

            case 'GeometryCollection':
                foreach ($geometry->getComponents() as $component) {
                    $parts[] = strtoupper($component->geometryType()).' ('.$this->extractData($component).')';
                }
                return implode(', ', $parts);
        }
    }

}
