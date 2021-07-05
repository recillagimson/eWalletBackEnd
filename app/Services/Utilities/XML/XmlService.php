<?php


namespace App\Services\Utilities\XML;


use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class XmlService implements IXmlService
{
    private $xw;

    public function __construct()
    {
        $this->initialize();
    }

    public function startElement(string $elementName, string $elementValue = null)
    {
        xmlwriter_start_element($this->xw, $elementName);

        if ($elementValue) {
            xmlwriter_text($this->xw, $elementValue);
            xmlwriter_end_element($this->xw);
        }
    }

    public function getString(): Stringable
    {
        $this->endXml();
        return Str::of(xmlwriter_output_memory($this->xw))->trim()->replaceFirst("^([\\W]+)<", "<");
    }

    public function toArray(string $xml): array
    {
        $strXml = preg_replace("/(<\/?)(\w+):([^>]*>)/", "$1$2$3", $xml);
        $xmlData = simplexml_load_string($strXml);
        $json = json_encode($xmlData->soapenvBody);
        return json_decode($json, true);
    }

    private function endXml()
    {
        xmlwriter_end_element($this->xw);
        xmlwriter_end_element($this->xw);
        xmlwriter_end_element($this->xw);
    }

    private function initialize()
    {
        $this->xw = xmlwriter_open_memory();

        xmlwriter_set_indent($this->xw, 4);
        xmlwriter_set_indent_string($this->xw, ' ');

        xmlwriter_start_document($this->xw, '1.0', 'UTF-8');
        xmlwriter_start_element($this->xw, 'soapenv:Envelope');

        xmlwriter_start_attribute($this->xw, 'xmlns:soapenv');
        xmlwriter_text($this->xw, 'http://schemas.xmlsoap.org/soap/envelope/');
        xmlwriter_end_attribute($this->xw);
        xmlwriter_start_element($this->xw, 'soapenv:Body');
    }


}
