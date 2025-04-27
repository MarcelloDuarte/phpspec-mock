<?php

namespace Phpspec\Mock\CodeGeneration;

use Phpspec\Mock\CollaboratorClassDoesNotExistException;
use ReflectionClass;

class DoubleGenerator
{
    use TypeReflection;
    private ClassGenerator $classGenerator;
    private MethodGenerator $methodGenerator;
    private ParametersGenerator $parametersGenerator;

    public function __construct()
    {
        $this->classGenerator = new ClassGenerator();
        $this->methodGenerator = new MethodGenerator();
        $this->parametersGenerator = new ParametersGenerator();
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
        if ($name !== null) {
            try {
                $reflection = new ReflectionClass($name);
            } catch (\ReflectionException $e) {
                throw new CollaboratorClassDoesNotExistException("Class or interface '$name' does not exist");
            }
            foreach ($reflection->getMethods() as $method) {
                $methodsCode .= $this->generateMethod($method);
            }
        }

        $classCode = $this->classGenerator->generate($className, $extendsOrImplements, $methodsCode);

        return [$classCode, $className];
    }

    private function getExtendsOrImplements(?string $name): string
    {
        if ($name === null) {
            return ' implements \Phpspec\Mock\DoubleInterface';
        }

        try {
            $reflection = new ReflectionClass($name);

            if ($reflection->isInternal()) {
                throw new \RuntimeException("Cannot create doubles for internal PHP classes: $name");
            }
            
            if ($reflection->isInterface()) {
                return "implements $name, \\Phpspec\\Mock\\DoubleInterface";
            }
        } catch (\ReflectionException $e) {
        }
        

        return "extends $name implements \\Phpspec\\Mock\\DoubleInterface";
    }

    /**
     * @param \ReflectionMethod $method
     * @return string
     */
    public function generateMethod(\ReflectionMethod $method): string
    {
        $methodsCode = '';
        if ($method->isPublic() && ! $method->isConstructor()) {
            [$params, $variables] = $this->parametersGenerator->generate($method->getParameters());
            $returnType = $method->hasReturnType() ? $this->getTypeDeclaration($method->getReturnType()) : null;

            if ($returnType === 'void') {
                $methodBody = sprintf(
                    "\$this->doubler->call(\"%s\", [%s]);",
                    $method->name,
                    implode(', ', $variables)
                );
            } else {
                $methodBody = sprintf(
                    "return \$this->doubler->call(\"%s\", [%s]);",
                    $method->name,
                    implode(', ', $variables)
                );
            }

            $methodsCode .= $this->methodGenerator->generate(
                $method->name,
                $params,
                $returnType,
                $methodBody
            );
        }
        return $methodsCode;
    }
}
