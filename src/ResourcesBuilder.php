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
use Ox\Components\FHIRCore\Model\R5\Datatypes\FHIRUnsignedInt;
use Ox\Components\FHIRCore\Model\R5\Resources\Backbone\FHIRImplementationGuideDefinition;
use Ox\Components\FHIRCore\Model\R5\Resources\Backbone\FHIRImplementationGuideDefinitionPage;
use Ox\Components\FHIRCore\Model\R5\Resources\Backbone\FHIRImplementationGuideDefinitionResource;
use Ox\Components\FHIRCore\Model\R5\Resources\Backbone\FHIRImplementationGuideDependsOn;
use Ox\Components\FHIRCore\Model\R5\Resources\Backbone\FHIRStructureDefinitionDifferential;
use Ox\Components\FHIRCore\Model\R5\Resources\FHIRConceptMap;
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
        $id = 'ig';

        $index_page = (new FHIRImplementationGuideDefinitionPage())
            ->setName('index.html')
            ->setTitle('Anonymization home page')
            ->setGeneration('html')
            ->setPage(
                (new FHIRImplementationGuideDefinitionPage())
                    ->setName('downloads.html')
                    ->setTitle('Anonymization downloads page')
                    ->setGeneration('html'),
                (new FHIRImplementationGuideDefinitionPage())
                    ->setName('background.html')
                    ->setTitle('Anonymization background page')
                    ->setGeneration('html'),
                (new FHIRImplementationGuideDefinitionPage())
                    ->setName('spec.html')
                    ->setTitle('Anonymization spec page')
                    ->setGeneration('html'),
            );

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
            $this->getAggregationRule($element->getPath()->getValue()),
            $this->getDiversityRule($element->getPath()->getValue()),
            $this->getClosenessRule($element->getPath()->getValue()),
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
            ->setShort('A new value that will be taken by all of the elements.')
            ->setDefinition('A new value that will be taken by all of the elements. Should be used alone.')
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
            ->setShort("Noise added to values.")
            ->setDefinition("A noise that will be applied randomly on all elements.")
            ->setMin(0)
            ->setMax('1')
            ->setType(...array_map(fn ($class) => (new FHIRElementDefinitionType())->setCode($class), $types));
    }

    private function getShuffleRule(string $parent_class): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setPath("$parent_class.shuffle")
            ->setShort("Shuffle elements from resources.")
            ->setDefinition("All elements will be shuffled, none of the resources will keep their original value, but will get one from another resource.")
            ->setMin(0)
            ->setMax('1')
            ->setType((new FHIRElementDefinitionType())->setCode(FHIRBoolean::RESOURCE_NAME))
            ->setDefaultValue((new FHIRBoolean())->setValue(false));
    }

    private function getEncryptionRule(string $parent_class): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setPath("$parent_class.encryptionAlgorithm")
            ->setShort('Encryption algorithm.')
            ->setDefinition('Encryption algorithm supported, depending from the anonymizer.')
            ->setMin(0)
            ->setMax('1')
            ->setType((new FHIRElementDefinitionType())->setCode(FHIRString::RESOURCE_NAME));
    }

    private function getHashRule(string $parent_class): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setPath("$parent_class.hashFunction")
            ->setShort('Hashing algorithm.')
            ->setDefinition('Hashing algorithm supported, depending from the anonymizer.')
            ->setMin(0)
            ->setMax('1')
            ->setType((new FHIRElementDefinitionType())->setCode(FHIRString::RESOURCE_NAME));
    }

    private function getRankRule(string $parent_class): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setPath("$parent_class.rank")
            ->setShort('Defines if the value is replaced with a not related id.')
            ->setDefinition('Every value will get unique value unrelated from his original value.')
            ->setMin(0)
            ->setMax('1')
            ->setType((new FHIRElementDefinitionType())->setCode(FHIRBoolean::RESOURCE_NAME));
    }

    private function getAggregationRule(string $parent_class): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setPath("$parent_class.aggregation")
            ->setShort('Defines an aggregation, automatic with a k value or with a ConceptMap.')
            ->setDefinition('If the value is a int (k), ll the value will be in a group with at least k value. If the value is a ConceptMap, all values will get their target value.')
            ->setMin(0)
            ->setMax('1')
            ->setType(
                (new FHIRElementDefinitionType())->setCode(FHIRUnsignedInt::RESOURCE_NAME),
                (new FHIRElementDefinitionType())->setCode(FHIRConceptMap::RESOURCE_NAME),
            );
    }

    private function getDiversityRule(string $parent_class): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setPath("$parent_class.diversity")
            ->setShort("Defines l diversity for the element.")
            ->setDefinition('In addition with the k-anonymization (aggregation) of another element, the l-diversity indicate how much of ')
            ->setMin(0)
            ->setMax('1')
            ->setType((new FHIRElementDefinitionType())->setCode(FHIRUnsignedInt::RESOURCE_NAME))
            ->setDefaultValue((new FHIRUnsignedInt())->setValue(0));
    }

    private function getClosenessRule(string $parent_class): FHIRElementDefinitionInterface
    {
        return (new FHIRElementDefinition())
            ->setShort('Defines the use of t-closeness.')
            ->setDefinition('If t-closeness is set, each group from k-anonymization will get the same distribution as the whole dataset.')
            ->setPath("$parent_class.closeness")
            ->setMin(0)
            ->setMax('1')
            ->setType((new FHIRElementDefinitionType())->setCode(FHIRBoolean::RESOURCE_NAME));
    }
}
