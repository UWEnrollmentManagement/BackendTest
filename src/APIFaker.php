<?php

namespace UWDOEM\REST\Backend\Test;


class APIFaker
{

    /** @var \Faker\Generator $faker */
    protected $faker;

    /** @var callable[] $extraFormatters */
    protected $extraFormatters;

    /** @var array $fieldsMap */
    protected $fieldsMap;

    /**
     * FormsAPIFaker constructor.
     */
    public function __construct($fieldmap, array $extraFormatters = [])
    {
        $this->fieldmap = $fieldmap;
        $this->faker = \Faker\Factory::create();
        $this->extraFormatters = $extraFormatters;
    }

    /**
     * Helper function which calls the \Faker\Generator methods to produce mock
     * data.
     *
     * For example, a $template of:
     *   ['name' => 'catchphrase', 'slug' => 'slug']
     *
     * might yield:
     *   ['name' => 'Reactive Octo-Interpretter', 'slug' => 'blue-dogs-fruit']
     *
     * @param string[] $template
     * @return array
     */
    protected function makeResult($template)
    {
        $result = [];
        foreach ($template as $key => $methodSpec) {

            if(gettype($methodSpec) === "string") {
                $methodName = $methodSpec;
                $methodArguments = null;
            } else {
                $methodName = $methodSpec[0];
                $methodArguments = $methodSpec[1];
            }

            if (array_key_exists($methodName, $this->extraFormatters)) {
                $method = $this->extraFormatters[$methodName];
                $result[$key] = $method($methodArguments);
            } else {
                $result[$key] = $this->faker->$methodName($methodArguments);
            }
        }

        return $result;
    }

    /**
     * Produce fake data for a given resource type, to include required and optional
     * attributes.
     *
     * For example, `fake(
     *
     * @param string $resourceType
     * @param string[] $data
     * @return string[]
     */
    public function fake($resourceType, $data = [])
    {
        $template = array_merge(
            $this->fieldsMap[$resourceType]['required'],
            $this->fieldsMap[$resourceType]['optional']
        );

        return array_merge($this->makeResult($template), $data);
    }

    public function fakeRequiredOnly($resourceType, $data = [])
    {
        $template = $this->fieldsMap[$resourceType]['required'];

        return array_merge($this->makeResult($template), $data);
    }

}