<?php declare(strict_types = 1);

namespace PHPStan\Reflection\Sentry;

use Consistence\Sentry\Metadata\Visibility;
use PHPStan\Reflection\ClassMemberReflection;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\FunctionVariant;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Type;
use PHPStan\Type\VoidType;

class SentryMethodReflection implements MethodReflection
{

	/** @var string */
	private $name;

	/** @var \PHPStan\Reflection\ClassReflection */
	private $declaringClass;

	/** @var \Consistence\Sentry\Metadata\Visibility */
	private $visibility;

	/** @var \PHPStan\Type\Type */
	private $type;

	/** @var bool|null */
	private $setterParameterNullability;

	public function __construct(
		string $name,
		ClassReflection $declaringClass,
		Visibility $visibility,
		Type $type,
		?bool $setterParameterNullability
	)
	{
		$this->name = $name;
		$this->declaringClass = $declaringClass;
		$this->visibility = $visibility;
		$this->type = $type;
		$this->setterParameterNullability = $setterParameterNullability;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getDeclaringClass(): ClassReflection
	{
		return $this->declaringClass;
	}

	public function getPrototype(): ClassMemberReflection
	{
		return $this;
	}

	public function isStatic(): bool
	{
		return false;
	}

	/**
	 * @return \PHPStan\Reflection\ParametersAcceptor[]
	 */
	public function getVariants(): array
	{
		return [
			new FunctionVariant(
				$this->setterParameterNullability === null ? [] : [new SentrySetterParameter($this->type)],
				false,
				$this->setterParameterNullability === null ? $this->type : new VoidType()
			),
		];
	}

	public function isPrivate(): bool
	{
		return $this->visibility->equals(Visibility::get(Visibility::VISIBILITY_PRIVATE));
	}

	public function isPublic(): bool
	{
		return $this->visibility->equals(Visibility::get(Visibility::VISIBILITY_PUBLIC));
	}

}
