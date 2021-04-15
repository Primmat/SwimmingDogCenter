<?php namespace ProcessWire;

/**
 * ProcessWire Form Builder Field
 *
 * Serves as an individual field in a Form Builder form.
 * It is an intermediary between the JSON/array form and Inputfields.
 *
 * Copyright (C) 2020 by Ryan Cramer Design, LLC
 * 
 * PLEASE DO NOT DISTRIBUTE
 * 
 * This file is commercially licensed, supported and distributed. 
 * 
 * @property string $name
 * @property string $type
 * @property string $label
 * @property string $description
 * @property string $notes
 * @property string $head
 * @property FormBuilderField|null $parent
 * @property bool $required
 * @property string $requiredIf
 * @property string $showIf
 * @property int $columnWidth
 * @property string $defaultValue
 * @property int $level
 * @property int $numChildren
 * @property array $children
 * @property FormBuilderForm $form
 * 
 */

class FormBuilderField extends FormBuilderData {

	/**
	 * Used when getting a flat representation of all fields
	 *
	 */
	static protected $allFields = array();

	/**
	 * Children of this field
	 *
	 */
	protected $children = array();

	/**
	 * Set default/starting values
	 *
	 */
	public function __construct() {
		foreach($this->getDefaultsArray() as $key => $value) {
			$this->set($key, $value); 
		}
	}

	/**
	 * Get default settings for a blank FormBuilderField object
	 * 
	 * @return array
	 * 
	 */
	public function getDefaultsArray() {
		return array(
			'name' => '',
			'type' => '',
			'label' => '',
			'description' => '',
			'notes' => '',
			'head' => '',
			'parent' => null, // containing parent
			'required' => false,
			'columnWidth' => 0,
			'defaultValue' => '',
			'level' => 0,
		);
	}

	/**
	 * Set a value to the field
	 * 
	 * @param string $key
	 * @param mixed $value
	 * @return FormBuilderData|$this
	 *
	 */
	public function set($key, $value) {
		if($key == 'name') return $this->setName($value);
		return parent::set($key, $value); 
	}
	
	protected function setName($name) {
		// $prevName = parent::get('name');
		
		if(!ctype_alnum($name)) {
			if(!ctype_alnum(str_replace(array('-', '_', '.'), '', $name))) {
				$name = preg_replace('/[^-_.a-zA-Z0-9]/', '_', $name);
			}
		}
		if(strpos($name, '_END') !== false && substr($name, -4) === '_END') {
			// end of fieldset
		} else {
			$name = strtolower($name);
		}
		
		/*
		if($prevName && $prevName !== $name) {
			unset(self::$allFields[$prevName]); 
			self::$allFields[$name] = $this;
		}
		*/
		
		return parent::set('name', $name);

	}
	

	/**
	 * Get a value from this form field
	 * 
	 * @param string $key
	 * @return mixed
 	 *
	 */
	public function get($key) {

		if($key == 'form') {
			// return the root parent (form)
			if($this->type == 'Form') return $this; 
			if($this->parent) return $this->parent->form; 
			return null;

		} else if($key == 'children') {
			return $this->children;

		} else if($key == 'numChildren') {
			return count($this->children); 

		} else if(isset($this->children[$key])) {
			return $this->children[$key];
		}

		return parent::get($key); 
	}

	/**
	 * Get a setting only
	 * 
	 * @param string $key
	 * @return mixed|null
	 * @since 0.4.0
	 * 
	 */
	public function getSetting($key) {
		return parent::get($key);
	}

	/**
	 * Given an array of data, populate the data to this form field
	 *
	 * Recursively populate 'children' field when present
	 * 
	 * @param array $data
	 * @return void
	 *
	 */
	public function setArray(array $data) {

		foreach($data as $key => $value) {

			if($key == 'children' && is_array($value)) {

				foreach($value as $name => $childData) {
					// convert each child in $value from array to object
					$child = new FormBuilderField();	
					if($this->wire) $child->setWire($this->wire);
					$child->name = $name; 
					$child->setArray($childData); 
					$this->add($child); 
				}

			} else {
				$this->set($key, $value); 
			}
		}		
	}

	/**
	 * Return an array representing this field and children (when present)
	 *
	 * @return array
	 *
	 */	
	public function getArray() {

		// get $data from WireData
		$data = parent::getArray();

		// we don't need a 'name' in the return array
		// because the field name is the key
		if(!empty($data['parent'])) unset($data['name']); 

		// remove fields that aren't needed in returns array
		// because they are already represented by the array structure
		unset($data['parent'], $data['form'], $data['level'], $data['id']); 

		// check if this field is a container for other fields (children)
		if(count($this->children)) {

			$children = array();

			foreach($this->children as $name => $child) {
				
				/** @var FormBuilderField $child */

				// use name defined with object, rather than key, in case it has changed
				$name = $child->name; 

				$children[$name] = $child->getArray(); 
			}
			$data['children'] = $children;
		}

		// remove any empty values for reduced storage requirements
		foreach($data as $key => $value) {
			if($value === null || $value === '') {
				unset($data[$key]);
			}
		}

		return $data; 	
	}

	/**
	 * Add a new child to this form/field
	 *
	 * @param FormBuilderField $child
	 * @return $this
	 *
	 */
	public function add(FormBuilderField $child) {

		// remove from old parent if it has one
		if($child->parent && $child->parent !== $this) $child->parent->remove($child); 

		// set new parent and level
		$child->parent = $this; 
		$child->level = $this->level+1; 

		// unset it first in case it's already set, so that it gets appended to the end
		unset($this->children[$child->name]); 
		unset(self::$allFields[$child->name]); 

		// now add it
		$this->children[$child->name] = $child; 
		self::$allFields[$child->name] = $child;

		return $this; 
	}

	/**
	 * Remove the given child from this form/field
	 *
	 * @param FormBuilderField|string The actual field or it's name
	 * @return FormBuilderData|$this
	 *
	 */
	public function remove($key) {
		if(is_string($key) && array_key_exists($key, $this->data)) return parent::remove($key);

		if($key instanceof FormBuilderField) $child = $key;
			else $child = $this->child($key);

		if($child) {

			// unset the child's parent
			$child->parent = null;

			// remove from our children array
			unset($this->children[$child->name]); 
			unset(self::$allFields[$child->name]); 
		}

		return $this; 
	}

	/**
	 * Return array of all children
	 *
	 * @return array
	 *
	 */
	public function children() {
		return $this->children; 
	}

	/**
	 * Return a flattened (non structured) array of all children
	 *
	 * Fieldset structure is instead represented by an opening fieldset which is 
	 * closed with a field of the same name with '_END' appended to it. 
	 *
	 * This function also sets a 'level' (integer) and 'parent' (FormBuilderField)
	 * to each child, for convenience. 
	 *
	 * @param array $options
	 *  - `level` (int): For internal recursion use
	 * @return array
	 *
	 */
	public function getChildrenFlat(array $options = array()) {
		
		$defaults = array(
			'level' => 0, 
			'skipTypes' => array(), 
			'includeNestedForms' => false,  // include nested FormBuilderForm fields?
		);

		$options = array_merge($defaults, $options);
		$children = array();

		foreach($this->children as $key => $child) {
			
			/** @var FormBuilderField $child */
			$childType = $child->type;
			
			$child->level = $options['level'];
			$child->parent = $this;

			if(!in_array($childType, $options['skipTypes'])) $children[$child->name] = $child;
			$numChildren = $child->numChildren;
			
			if($childType === 'FormBuilderForm' && $options['includeNestedForms']) {
				/** @var FormBuilderForm $form */
				$form = $this->wire('forms')->load($child->get('addForm'));
				if($form) {
					foreach($form->getChildrenFlat($options) as $c) {
						$c = clone $c;
						$c->parent = $child;
						$children["{$child->name}_{$c->name}"] = $c;
					}
				}
				
			} else if($numChildren || $childType === 'Fieldset') {
				// check if there are children 
				// we also check for Fieldset in case it's an empty Fieldset

				// append the children
				if($numChildren) {
					$o = $options;
					$o['level']++;
					foreach($child->getChildrenFlat($o) as $name => $c) {
						$children[$name] = $c;
					}
				}

				// close the fieldset
				if(!in_array('Fieldset', $options['skipTypes'])) {
					$end = new FormBuilderField();
					$name = $child->name . '_END';
					$end->name = $name;
					$end->type = '';
					$end->level = $options['level'];
					$children[$name] = $end;
				}
			}

		}

		return $children;
	}

	/**
	 * Get contents of the self::$allFields property containing all addded fields at runtime
	 * 
	 * This should only be used in cases where only 1 form is loaded in memory. For other cases,
	 * you should use the getChildrenFlat() method. 
	 * 
	 * @param array $options
	 * @return array
	 * 
	 */
	public function allFields(array $options = array()) {
		$defaults = array(
			'type' => '', 
			'getProperty' => '',
		);
		$options = array_merge($defaults, $options);
		$property = $options['getProperty'];
		$a = array();
		if($options['type']) {
			$options['type'] = strtolower($options['type']);
			foreach(self::$allFields as $name => $field) {
				if(strtolower($field->type) === $options['type']) {
					$a[$name] = $property ? $field->$property : $field;
				}
			}
		} else if($property) {
			foreach(self::$allFields as $name => $field) {
				$a[$name] = $field->$property;
			}
		} else {
			$a = self::$allFields;
		}
		return $a;
	}

	/**
	 * Return the direct child given by $name
	 *
	 * @param string $name
	 * @return FormBuilderField|null
	 *
	 */
	public function child($name) {
		if(isset($this->children[$name])) return $this->children[$name];
		return null;
	}

	/**
	 * Recursively find the field named $name (alias of getFieldByName)
	 *
	 * @param string $name
	 * @return FormBuilderField|null
	 * @deprecated Use getFieldByName instead
	 *
	 */
	public function find($name) {
		return $this->getFieldByName($name);
	}

	/**
	 * Get a field by name, within entire form
	 * 
	 * @param string $name
	 * @return FormBuilderField|null
	 * @since 0.4.4
	 * 
	 */
	public function getFieldByName($name) {
		if(isset(self::$allFields[$name])) return self::$allFields[$name];
		$field = null;
		if(strpos($name, '_')) {
			// check for field nested in type=FormBuilderForm field
			foreach(self::$allFields as $child) {
				if($child->type != 'FormBuilderForm') continue;
				if(!$child->get('addForm')) continue;
				if(strpos($name, $child->name) !== 0) continue; // (previous_location)_[city]
				/** @var FormBuilderForm $_form */
				$_form = $this->wire()->forms->load($child->get('addForm'));
				$_name = substr($name, strlen($child->name)+1);
				if($_form) $field = $_form->getFieldByName($_name);
				if(!$field) continue;
				$field = clone $field;
				$field->name = $name;
				break;
			}
			if($field) return $field;
		}
		if(ctype_digit("$name")) {
			foreach(self::$allFields as $child) {
				if($child->id == $name) {
					$field = $child;
					break;
				}
			}
		}
		return $field;
	}

	/**
	 * Get new Inputfield for this FormBuilderField (for public API usage)
	 * 
	 * Please note: 
	 *  - Returns a new Inputfield instance on every call. 
	 *  - Returned Inputfield has no value assigned yet. 
	 *  - This method is NOT used for forms rendered or processed by FormBuilder (see FormBuilderMaker::makeInputfield for that)
	 *  - This method can be used by the entries CSV Export function for some Inputfield types. 
	 *  - This method is very similar to FormBuilderMaker::makeInputfield() and should mirror most of what it does,
	 *    but the context is different enough that they need to be separate methods. The context of this method
	 *    is more specific to public API usage or other cases where an Inputfield is needed, but we are not in a 
	 *    case where an entire form will be rendered or processed. 
	 * 
	 * @param array $options
	 *  - `language` (Language|int|string): Optionally get for this non-default language
	 *  - `type` (string): Type to use, or omit to use type assigned to this FormBuilderField
	 * @return Inputfield
	 * @since 0.4.4
	 * 
	 */
	public function getInputfield(array $options = array()) {
		
		$defaults = array('language' => null, 'type' => $this->type);
		$options = array_merge($defaults, $options);
		list($type, $language, $languageID, $processor) = array($options['type'], $options['language'], 0, null);
		$skipKeys = array('id', 'name', 'type', 'children', 'level', 'parent', 'form'); // do not add to Inputfield
		$langKeys = array('label', 'description', 'notes', 'placeholder', 'detail'); // can have other languages

		if($language && $this->wire()->languages) {
			if(is_string($language) && ctype_digit("$language")) $language = (int) "$language";
			if(!is_object($language)) $language = $this->wire()->languages->get($language); /** @var Language $language */
			if($language && $language->id && !$language->isDefault()) $languageID = $language->id;
		}
		
		/** @var Inputfield|InputfieldWrapper $f */
		$f = $this->wire()->modules->get(strpos($type, 'Inputfield') === 0 ? $type : 'Inputfield' . ucfirst($type));
		if(!$f) $f = $this->wire()->modules->get('InputfieldText');

		$f->setAttributes(array('name' => $this->name, 'id' => "Inputfield_$this->name"));
		$f->setArray(array('formBuilder' => true, 'hasFieldtype' => false)); 

		// set extra values to InputfieldFormBuilder derived Inputfields
		if($f instanceof InputfieldFormBuilderInterface && $this->form && $processor = $this->form->processor()) {
			$f->set('processor', $processor);
			$f->set('formID', $processor->id);
		}

		// populate any other settings to the Inputfield
		foreach($this->data as $key => $value) {
			if(!in_array($key, $skipKeys)) $f->$key = $this->data[$key];
		}

		// if multi-language, populate the other language properties
		if($languageID) foreach($langKeys as $key) {
			$langKey = $key . $languageID;
			$langVal = $f->$langKey;
			if(strlen($langVal)) $f->$key = $langVal;
		}
		
		/** @var InputfieldFormBuilderForm $f */
		if($processor && wireInstanceOf($f, 'InputfieldFormBuilderForm')) $f->setup($processor);

		return $f;
	}

	public function __toString() {
		return $this->name; 
	}

}
