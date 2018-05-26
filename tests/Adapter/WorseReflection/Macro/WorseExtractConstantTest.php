<?php

namespace Phpactor\CodeTransform\Tests\Adapter\WorseReflection\Macro;

use Phpactor\CodeTransform\Tests\Adapter\WorseReflection\WorseTestCase;
use Phpactor\CodeTransform\Adapter\WorseReflection\Macro\WorseExtractConstant;
use Phpactor\CodeTransform\Domain\SourceCode;
use Phpactor\CodeTransform\Domain\Exception\TransformException;
use Phpactor\TestUtils\ExtractOffset;

class WorseExtractConstantTest extends WorseTestCase
{
    /**
     * @dataProvider provideExtractMethod
     */
    public function testExtractConstant(string $test, $name)
    {
        list($source, $expected, $offset) = $this->sourceExpectedAndOffset(__DIR__ . '/fixtures/' . $test);

        $extractConstant = new WorseExtractConstant($this->reflectorFor($source), $this->updater());
        $transformed = $this->executeMacro($extractConstant, [
            'sourceCode' => SourceCode::fromString($source),
            'offset' => $offset,
            'constantName' => $name
        ]);

        $this->assertEquals(trim($expected), trim($transformed));
    }

    public function provideExtractMethod()
    {
        return [
            'string' => [
                'extractConstant1.test',
                'HELLO_WORLD'
            ],
            'numeric' => [
                'extractConstant2.test',
                'HELLO_WORLD'
            ],
            'array_key' => [
                'extractConstant3.test',
                'HELLO_WORLD'
            ],
            'namespaced' => [
                'extractConstant4.test',
                'HELLO_WORLD'
            ],
            'replace all' => [
                'extractConstant5.test',
                'HELLO_WORLD'
            ],
            'replace all numeric' => [
                'extractConstant6.test',
                'HOUR'
            ],
        ];
    }

    public function testNoClass()
    {
        $this->expectException(TransformException::class);
        $this->expectExceptionMessage('Node does not belong to a class');

        $code = <<<'EOT'
<?php 1234;
EOT
        ;

        $extractConstant = new WorseExtractConstant($this->reflectorFor($code), $this->updater());
        $this->executeMacro($extractConstant, [
            'sourceCode' => SourceCode::fromString($code),
            'offset' => 8, 
            'constantName' => 'asd'
        ]);
        $transformed = $extractConstant->extractConstant(SourceCode::fromString($code), 8, 'asd');
    }
}
