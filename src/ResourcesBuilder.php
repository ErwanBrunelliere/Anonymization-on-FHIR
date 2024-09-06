<?php

namespace Openxtrem\AnonymizationOnFhir;

use Ox\Components\FHIRCore\Interfaces\Datatypes\Complex\FHIRContactDetailInterface;
use Ox\Components\FHIRCore\Interfaces\Datatypes\Complex\FHIRElementDefinitionInterface;
use Ox\Components\FHIRCore\Interfaces\Resources\FHIRImplementationGuideInterface;
use Ox\Components\FHIRCore\Interfaces\Resources\FHIRStructureDefinitionInterface;
use Ox\Components\FHIRCore\Model\R5\Datatypes\Complex\Element\FHIRElementDefinitionExample;
use Ox\Components\FHIRCore\Model\R5\Datatypes\Complex\Element\FHIRElementDefinitionType;
use Ox\Components\FHIRCore\Model\R5\Datatypes\Complex\FHIRBackboneElement;
use Ox\Components\FHIRCore\Model\R5\Datatypes\Complex\FHIRContactDetail;
use Ox\Components\FHIRCore\Model\R5\Datatypes\Complex\FHIRContactPoint;
use Ox\Components\FHIRCore\Model\R5\Datatypes\Complex\FHIRElementDefinition;
use Ox\Components\FHIRCore\Model\R5\Datatypes\Complex\FHIRPrimitiveType;
use Ox\Components\FHIRCore\Model\R5\Datatypes\Complex\FHIRReference;
use Ox\Components\FHIRCore\Model\R5\Datatypes\FHIRBoolean;
use Ox\Components\FHIRCore\Model\R5\Datatypes\FHIRDate;
use Ox\Components\FHIRCore\Model\R5\Datatypes\FHIRDecimal;
use Ox\Components\FHIRCore\Model\R5\Datatypes\FHIRInstant;
use Ox\Components\FHIRCore\Model\R5\Datatypes\FHIRInteger;
use Ox\Components\FHIRCore\Model\R5\Datatypes\FHIRString;
use Ox\Components\FHIRCore\Model\R5\Datatypes\FHIRTime;
use Ox\Components\FHIRCore\Model\R5\Resources\Backbone\FHIRImplementationGuideDefinition;
use Ox\Components\FHIRCore\Model\R5\Resources\Backbone\FHIRImplementationGuideDefinitionPage;
use Ox\Components\FHIRCore\Model\R5\Resources\Backbone\FHIRImplementationGuideDefinitionResource;
use Ox\Components\FHIRCore\Model\R5\Resources\Backbone\FHIRImplementationGuideDependsOn;
use Ox\Components\FHIRCore\Model\R5\Resources\Backbone\FHIRStructureDefinitionDifferential;
use Ox\Components\FHIRCore\Model\R5\Resources\FHIRImplementationGuide;
use Ox\Components\FHIRCore\Model\R5\Resources\FHIRStructureDefinition;

class ResourcesBuilder
{
    private const CANONICAL = 'https://build.fhir.org/ig/ErwanBrunelliere/Anonymization-on-FHIR';
    private const Publisher = 'Xtrem Santé';

    /**
     * @return FHIRContactDetailInterface[]
     */
    private function getContacts(): array
    {
        return [
            (new FHIRContactDetail())
                ->setName(self::Publisher . ' - Interop')
                ->setTelecom(
                    (new FHIRContactPoint())
                        ->setSystem('email')
                        ->setValue('dev.interop@xtremsante.fr')
                        ->setUse('work')
                        ->setRank(1)
                ),
            (new FHIRContactDetail())
                ->setName('Erwan Brunellière')
                ->setTelecom(
                    (new FHIRContactPoint())
                        ->setSystem('email')
                        ->setValue('ebrunelliere@xtremsante.fr')
                        ->setUse('work')
                        ->setRank(2)
                ),
        ];
    }

    public function generateIG(): FHIRImplementationGuideInterface
    {
        $canonical = self::CANONICAL;

        $id = 'buid.fhir.ig.anonymizationonfhir.ig';

        $index_page = (new FHIRImplementationGuideDefinitionPage())
            ->setName('index.html')
            ->setTitle('Anonymization home page')
            ->setGeneration('html');

        $IG = (new FHIRImplementationGuide())
            ->setId($id)
            ->setUrl("$canonical/ImplementationGuide/$id")
            ->setVersion('0.0.1')
            ->setName('AnonymizationOnFhir')
            ->setTitle('Anonymization-on-FHIR')
            ->setStatus('draft')
            ->setDescription("Anonymization model for FHIR")
            ->setPackageId('anonymizationonfhir')
            ->setFhirVersion('5.0.0')
            ->setDependsOn(
                (new FHIRImplementationGuideDependsOn())
                    ->setUri("http://hl7.org/fhir/ImplementationGuide/fhir")
                    ->setPackageId("hl7.fhir.r5.core")
                    ->setVersion("5.0.0")
            )
            ->setDefinition(
                (new FHIRImplementationGuideDefinition())
                    ->setPage($index_page)
                    ->setResource((new FHIRImplementationGuideDefinitionResource())
                                      ->setReference((new FHIRReference())->setReference('StructureDefinition/AnonymizationOperation')))
            )
            ->setContact(...$this->getContacts());

        return $IG;
    }

    public function generateOperationStructureDefinition(): FHIRStructureDefinitionInterface
    {
        $canonical = self::CANONICAL;
        $name      = 'AnonymizationOperation';

        $st = (new FHIRStructureDefinition())
            ->setId($name)
            ->setUrl("$canonical/StructureDefinition/$name")
            ->setVersion('0.0.1')
            ->setName($name)
            ->setTitle("Anonymization Operation")
            ->setStatus("draft")
            ->setExperimental(true)
            ->setDate(date('Y-m-d'))
            ->setPublisher(self::Publisher)
            ->setContact(...$this->getContacts())
            ->setDescription(
                'This logical model defines an operation that will or has been processed for transforming a fhir request result in a anonymized dataset. We specify by ruleset the values that we want to keep and with which operation.'
            )
            ->setPurpose('Defines past or future anonymization operation.')
            ->setKind('logical')
            ->setAbstract(false)
            ->setFhirVersion('5.0.0')
            ->setType($name)
            ->setBaseDefinition('http://hl7.org/fhir/StructureDefinition/Base')
            ->setDerivation('specialization')
            ->setDifferential($differential = new FHIRStructureDefinitionDifferential());

        $differential->addElement(
            (new FHIRElementDefinition())
                ->setPath("$name"),

            (new FHIRElementDefinition())
                ->setPath("$name.purpose")
                ->setShort("Purpose of the operation.")
                ->setDefinition("Defines what is the goal of this operation.")
                ->setMin(0)
                ->setMax('*')
                ->setType((new FHIRElementDefinitionType())->setCode(FHIRString::RESOURCE_NAME))
                ->setMustHaveValue(true),

            (new FHIRElementDefinition())
                ->setPath("$name.request")
                ->setMin(1)
                ->setMax('1')
                ->setType((new FHIRElementDefinitionType())->setCode(FHIRString::RESOURCE_NAME))
                ->setExample(
                    (new FHIRElementDefinitionExample())
                        ->setLabel('All Patient resources.')
                        ->setValue((new FHIRString())->setValue('Patient/')),
                    (new FHIRElementDefinitionExample())
                        ->setLabel('All active Patient resources.')
                        ->setValue((new FHIRString())->setValue('Patient/?active=true'))
                )
                ->setMustHaveValue(true)
                ->setIsSummary(true),

            $element = (new FHIRElementDefinition())
                ->setPath("$name.element")
                ->setShort('Values kept by the anonymization (and only ones).')
                ->setDefinition(
                    'Values that the anonymization process will keep and possibly modify with the specified rules.'
                )
                ->setMin(0)
                ->setMax('*')
                ->setType((new FHIRElementDefinitionType())->setCode(FHIRBackboneElement::RESOURCE_NAME))
                ->setIsSummary(true),

            (new FHIRElementDefinition())
                ->setPath($element->getPath()->getValue() . '.path')
                ->setShort('FHIRPath pointing the value.')
                ->setDefinition('A FHIRPath string that points the value being kept and might be modified.')
                ->setMin(1)
                ->setMax(1)
                ->setType((new FHIRElementDefinitionType())->setCode(FHIRString::RESOURCE_NAME))
                ->setMustHaveValue(true)
                ->setIsSummary(true),

            $this->getDefaultValueRule($element->getPath()->getValue()),
            $this->getNoiseRule($element->getPath()->getValue()),
            $this->getShuffleRule($element->getPath()->getValue()),
            $this->getEncryptionRule($element->getPath()->getValue()),
            $this->getHashRule($element->getPath()->getValue()),
            $this->getRankRule($element->getPath()->getValue()),
        );

        return $st;
    }

    private function getAllPrimitives(): array
    {
        return array_filter(
            get_declared_classes(),
            fn ($class) => is_subclass_of($class, FHIRPrimitiveType::class)
        );
    }

    private function getDefaultValueRule(string $parent_path): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setPath("$parent_path.defaultValue[x]")
            ->setMin(0)
            ->setMax('1')
            ->setType(...array_map(
                fn ($class) => (new FHIRElementDefinitionType())->setCode($class::RESOURCE_NAME),
                $this->getAllPrimitives()
                      ));
    }

    private function getNoiseRule(string $parent_class): FHIRElementDefinitionInterface
    {
        $types = [
            FHIRInteger::RESOURCE_NAME,
            FHIRDecimal::RESOURCE_NAME,
            FHIRDate::RESOURCE_NAME,
            FHIRTime::RESOURCE_NAME,
            FHIRInstant::RESOURCE_NAME,
        ];

        return (new FHIRElementDefinition())
            ->setPath("$parent_class.noise[x]")
            ->setMin(0)
            ->setMax('1')
            ->setType(...array_map(fn ($class) => (new FHIRElementDefinitionType())->setCode($class), $types));
    }

    private function getShuffleRule(string $parent_class): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setPath("$parent_class.shuffle")
            ->setMin(0)
            ->setMax('1')
            ->setType((new FHIRElementDefinitionType())->setCode(FHIRBoolean::RESOURCE_NAME));
    }

    private function getEncryptionRule(string $parent_class): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setPath("$parent_class.encryptionAlgorithm")
            ->setMin(0)
            ->setMax('1')
            ->setType((new FHIRElementDefinitionType())->setCode(FHIRString::RESOURCE_NAME));
    }

    private function getHashRule(string $parent_class): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setPath("$parent_class.hashFunction")
            ->setMin(0)
            ->setMax('1')
            ->setType((new FHIRElementDefinitionType())->setCode(FHIRString::RESOURCE_NAME));
    }

    private function getRankRule(string $parent_class): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setPath("$parent_class.rankRule")
            ->setMin(0)
            ->setMax('1')
            ->setType((new FHIRElementDefinitionType())->setCode(FHIRBoolean::RESOURCE_NAME));
    }
}
