<?php

namespace PhpSpec\Mock\CodeGeneration;

use PhpSpec\Mock\CollaboratorClassDoesNotExistException;
use ReflectionClass;

class DoubleGenerator
{
    use TypeReflection;

    private ClassGenerator $classGenerator;
    private MethodGenerator $methodGenerator;

    public function __construct()
    {
        $this->classGenerator = new ClassGenerator();
        $this->methodGenerator = new MethodGenerator(new ParametersGenerator());
    }

    /**
     * @param string|null $name
     * @return array
     * @throws CollaboratorClassDoesNotExistException
     */
    public function generate(?string $name = null): array
    {
        $namespaceToUnderscore = str_replace('\\', '_', $name ?? '');
        $className = 'PhpSpecMock__' . $namespaceToUnderscore . '_' . uniqid();
        $extendsOrImplements = $this->getExtendsOrImplements($name);

        $methodsCode = '';
        $methodsMetadata = [];
        if ($name !== null) {
            try {
                $reflection = new ReflectionClass($name);
            } catch (\ReflectionException $e) {
                throw new CollaboratorClassDoesNotExistException("Class or interface '$name' does not exist");
            }
            foreach ($reflection->getMethods() as $method) {
                if ($method->isPublic() && !$method->isConstructor()) {
                    [$metadata, $code] = $this->generateMethod($method);
                    $methodsCode .= $code;
                    $methodsMetadata[$method->getName()] = $metadata;
                }
            }
        }

        $classCode = $this->classGenerator->generate(
            $className,
            $extendsOrImplements,
            $methodsCode,
            $reflection?->isReadOnly() ?? false
        );

        return [$classCode, $className, $methodsMetadata];
    }

    /**
     * @throws CollaboratorClassDoesNotExistException
     */
    private function getExtendsOrImplements(?string $name): string
    {
        if ($name === null) {
            return ' implements \\PhpSpec\\Mock\\DoubleInterface';
        }

        try {
            $reflection = new ReflectionClass($name);

            if ($reflection->isInternal()) {
                throw new \RuntimeException("Cannot create doubles for internal PHP classes: $name");
            }

            if ($reflection->isInterface()) {
                return "implements $name, \\PhpSpec\\Mock\\DoubleInterface";
            }
        } catch (\ReflectionException $e) {
            throw new CollaboratorClassDoesNotExistException("Class or interface '$name' does not exist");
        }

        return "extends $name implements \\PhpSpec\\Mock\\DoubleInterface";
    }

    /**
     * @param \ReflectionMethod $method
     * @return array [MethodMetadata, string]
     */
    public function generateMethod(\ReflectionMethod $method): array
    {
        $variables = array_map(fn($p) => ($p->isVariadic() ? '...' : '') . '$' . $p->name, $method->getParameters());

        $returnType = $method->hasReturnType() ? $this->getTypeDeclaration($method->getReturnType()) : null;

        $body = ($returnType === 'void')
            ? sprintf("\$this->doubler->call(\"%s\", [%s]);", $method->getName(), implode(', ', $variables))
            : sprintf("return \$this->doubler->call(\"%s\", [%s]);", $method->getName(), implode(', ', $variables));

        return $this->methodGenerator->generate($method, $body);
    }
}
