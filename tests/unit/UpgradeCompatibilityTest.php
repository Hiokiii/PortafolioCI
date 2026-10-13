<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use Config\Services;
use Dompdf\Dompdf;

final class UpgradeCompatibilityTest extends CIUnitTestCase
{
    use FeatureTestTrait;

    public function testLoginPageRendersWithUpdatedFramework(): void
    {
        $result = $this->get('IniciarSesion');
        $result->assertStatus(200);
        $result->assertSee('Home/validarIngreso');
    }

    public function testControllerAliasesRequireSession(): void
    {
        foreach (['AdminDashboard/borrarUsuario', 'CompraJugadorController/registrarJugador',
            'SocioController/verMensualidad', 'AgregarSponsor'] as $path) {
            Services::reset();
            $session = $this->createMock(\CodeIgniter\Session\Session::class);
            $session->method('has')->willReturn(false);
            Services::injectMock('session', $session);
            $this->get($path)->assertRedirect();
        }
    }

    public function testLegacyAutoRoutingIsDisabled(): void
    {
        $this->expectException(\CodeIgniter\Exceptions\PageNotFoundException::class);
        $this->get('Home/cerrarSesion');
    }

    public function testUpdatedDompdfProducesPdf(): void
    {
        $pdf = new Dompdf();
        $pdf->loadHtml('<html><body><h1>Los Alces</h1><p>Comprobante de prueba</p></body></html>');
        $pdf->render();
        $this->assertStringStartsWith('%PDF-', $pdf->output());
    }

    public function testExistingEncryptionConfigurationStillWorks(): void
    {
        $encrypter = Services::encrypter();
        $encrypted = $encrypter->encrypt('compatibility-test');
        $this->assertSame('compatibility-test', $encrypter->decrypt($encrypted));
    }

    public function testRegisteredRoutesResolveToExistingMethods(): void
    {
        $routes = Services::routes();
        $routes->loadRoutes();
        foreach (['GET', 'POST'] as $verb) {
            foreach ($routes->getRoutes($verb) as $path => $handler) {
                if (!is_string($handler) || !str_contains($handler, '::')) continue;
                [$controller, $method] = explode('::', $handler, 2);
                $method = explode('/', $method, 2)[0];
                $this->assertTrue(method_exists($controller, $method), "$verb $path: $handler");
            }
        }
    }
}
