<?php

namespace models;

/**
 * Class Vocabulary
 *
 * This class represents a Vocabulary for SocialeKaart.care. Within SocialeKaart.care there are some business rules for
 * the Vocabulary.
 * - If a Vocabulary does not exist, the code is free to create it;
 * - Terms on the highest level (with no parents) are the Main Terms those are used in the systems Label logic.
 * - Terms on the first level (with a Main Term as a parent) are used as Synonyms of the Main Terms when indexing nodes for the search engine;
 * - A Main term can only appear once on that level;
 * - A Synonym can appear multiple times on the second level.
 *
 * @package models
 */
class Vocabulary
{

    private $object = null;
    public $name = '';
    public $vid = '';
    public $machineName = '';

    /**
     * Loads a Vocabulary
     *
     * @param $vid
     * @return Vocabulary
     */
    public static function load($vid, $machineName = null)
    {
        return Factory::getInstance()->getModel($vid, 'Vocabulary', $machineName);
    }

    /**
     * Returns a Vocabulary object based on the given drupal machine_name
     *
     * @param $machineName
     * @return mixed|null
     */
    public static function loadByMachineName($machineName)
    {
        $vocabulary = null;

        // makes sure there are no illegal characters
        $machineName = \helper::toAscii($machineName, array(), '_');


        $infos = taxonomy_vocabulary_get_names();
        foreach ($infos as $info) {
            if ($info->machine_name == $machineName) {
                $vocabulary = Factory::getInstance()->getModel($info->vid, 'Vocabulary');
            }
        }
        return $vocabulary ;
    }

    /**
     * Creates a Vocabulary based on the given machineName.
     *
     * @param $machineName
     * @return Vocabulary
     */
    public static function createByMachineName($machineName)
    {
        // makes sure there are no illegal characters
        $machineName = \helper::toAscii($machineName, array(), '_');

        $vocabularyObject = db_query("select vid from taxonomy_vocabulary where machine_name = :machineName", array(':machineName'=>$machineName))->fetchObject();

        if ($vocabularyObject) {
            // it exists, let's return the existing one
            return self::load($vocabularyObject->vid);
        }

        $edit = array(
            'name' => str_replace('_', ' ', $machineName),
            'machine_name' => $machineName,
            'description' => 'Automatically generated Vocabulary ' . $machineName,
            'module' => 'taxonomy',
        );
        $vocabulary = (object) $edit;
        taxonomy_vocabulary_save($vocabulary);
        return self::loadByMachineName($machineName);
    }

    /**
     * Returns a newly created of existing Vocabulary object based on a given drupal machine_name
     *
     * @param String $machineName
     * @return Vocabulary|null
     */
    public static function loadOrCreateByMachineName($machineName)
    {
        $vacabulary = self::loadByMachineName($machineName);
        if (!$vacabulary) {
            // vocabulary not found, create new one in the dtb
            return self::createByMachineName($machineName);
        }
        return $vacabulary;
    }


    /**
     * Vocabulary constructor.
     *
     * @param $vid
     */
    public function __construct($vid, $machineName = null)
    {
        $this->init($vid, $machineName);
    }

    /**
     * Initiates all the needed information of the Vocabulary
     *
     * @param $vid
     * @throws \Exception
     */
    public function init($vid, $machineName = null)
    {
        $this->object = taxonomy_vocabulary_load($vid);

        // no known vocabulary, let's create one
        if (empty($this->object) && !is_null($machineName)) {
            $vocabulary = Vocabulary::createByMachineName($machineName);
            $this->object = taxonomy_vocabulary_load($vocabulary->vid);
        } elseif (empty($this->object) && !is_null($machineName)) {
            throw new \Exception(sprintf('No Vocabulary found with given vid %s', $vid));
        }

        $this->name = $this->object->name;
        $this->vid = $this->object->vid;
        $this->machineName = $this->object->machine_name;
    }

    /**
     * Saves the Vocabulary
     */
    public function save()
    {
        taxonomy_vocabulary_save($this->object);
    }


    /**
     * Get's a main term.
     * Main terms are terms with one child (a synonym) or zero childs, and no parents.
     *
     * @param $term
     * @return null|stdClass
     */
    public function getMainTerm($term)
    {
        $term = strtolower(trim($term));
        $terms = taxonomy_get_term_by_name($term, $this->machineName);

        foreach ($terms as $term) {
            if (count(taxonomy_get_parents($term->tid)) == 0) {
                // the term is on level zero so it's not a synonym
                return $term;
            }
        }

        return null;
    }

    /**
     * Get's a synonym term. (Still not usable)
     * Synonym terms are terms with one parent.
     *
     * @return null|stdClass
     */
    public function getSynonymTerm()
    {
        echo 'Function not ready to be used';
        exit;

        // TODO this function assumes that there is only one taxonomy term on the 2nd level.
        // but one term can appear multiple times.

        $term = strtolower(trim($term));
        $terms = taxonomy_get_term_by_name($term, $this->machineName);

        foreach ($terms as $term) {
            if (count(taxonomy_get_parents($term->tid)) >= 1) {
                // the term is not on level zero so it's a synonym
                return $term;
            }
        }
        return null;
    }

    /**
     * Adds a synonym to a term (not implemented)
     *
     * @param $parentTid
     * @param $synonymName
     */
    public function addSynonymTerm($parentTid, $synonymName)
    {
        echo 'Function not implemented';
        exit;

        // TODO implement function
    }


    /**
     * Adds a Term to this Vocabulary
     *
     * @param $term String
     * @return stdClass
     * @throws \Exception
     */
    public function addMainTerm($term)
    {
        if (empty($term)) {
            throw new \Exception(sprintf('No term provided in addTerm.'));
        }

        $storedTermObject = $this->getMainTerm($term);

        if (!$storedTermObject) {
            $termObject = new \StdClass();
            $termObject->name = $term;
            $termObject->vid = $this->vid;

            taxonomy_term_save($termObject);
            // retrieve term from the database so we have an tid
            $storedTermObject = $this->getMainTerm($termObject->name);
        }
        return $storedTermObject;
    }
}
