<?php

require __DIR__ . '/../vendor/autoload.php';

use Ox\Components\FHIRCore\Serializer\XMLSerializer;
use Openxtrem\AnonymizationOnFhir\ResourcesBuilder;

$builder = new ResourcesBuilder();

$s_ig = (new XMLSerializer())->serializeResource($builder->generateIG());
$s_operation = (new XMLSerializer())->serializeResource($builder->generateOperationStructureDefinition());

file_put_contents(__DIR__ . '/../input/AnonymizationImplementationGuide.xml', $s_ig);
file_put_contents(__DIR__ . '/../input/resources/AnonymizationOperation.xml', $s_operation);