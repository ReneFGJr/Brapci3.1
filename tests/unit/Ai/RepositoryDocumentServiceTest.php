<?php

namespace Tests\Unit\Ai;

use App\Services\Ai\RepositoryDocumentService;
use CodeIgniter\Test\CIUnitTestCase;

final class RepositoryDocumentServiceTest extends CIUnitTestCase
{
    public function testRecognizesLoadCommandIgnoringCaseAndWhitespace(): void
    {
        $service = new RepositoryDocumentService();

        $this->assertSame(384240, $service->commandId("  CARREGUE 384240\n"));
    }

    public function testRejectsTextThatIsNotAnExactLoadCommand(): void
    {
        $service = new RepositoryDocumentService();

        $this->assertNull($service->commandId('Por favor, carregue 384240'));
        $this->assertNull($service->commandId('Carregue 0'));
    }
}
