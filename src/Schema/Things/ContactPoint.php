<?php

namespace Melbahja\Seo\Schema\Things;

class ContactPoint extends \Melbahja\Seo\Schema\Thing
{
	public function __construct()
	{
		parent::__construct("ContactPoint", []);
	}

	public function setTelephone(string $value) :self
	{
		$this->data['telephone']=$value;
		return $this;
	}

	public function setContactType(string $value) :self
	{
		$this->data['contactType']=$value;
		return $this;
	}
}